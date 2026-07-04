<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Google\Auth\Credentials\ServiceAccountCredentials;

class Push extends CI_Controller {

    private $projectId = 'govtjobs-ai-prod';
    private $dailyLimit = 6;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
/*
    |---------------------------------------------
    | 🔑 Firebase Access Token
    |---------------------------------------------
    */
    private function _getAccessToken()
    {
        $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];
        $keyPath = FCPATH . 'keys/firebase.json';

        if (!file_exists($keyPath)) return false;

        $credentials = new ServiceAccountCredentials(
            $scopes,
            json_decode(file_get_contents($keyPath), true)
        );

        $authToken = $credentials->fetchAuthToken();
        return $authToken['access_token'] ?? false;
    }

    /*
    |---------------------------------------------
    | 💾 Save Token
    |---------------------------------------------
    */
    public function save_token()
    {
        $token = $this->input->post('token');
        $device_id = $this->input->post('device_id');
    
        if(!$token || !$device_id){
            return $this->json(["status"=>false]);
        }
    
        // ✅ prevent duplicate token
        $exists = $this->db
            ->where('device_id',$device_id)
            ->or_where('token',$token)
            ->get('tb_push_tokens')
            ->row();
    
        $data = [
            'token' => $token,
            'device_id' => $device_id,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
            'is_active' => 1,
            'updated_at' => date('Y-m-d H:i:s')
        ];
    
        if($exists){
            $this->db->where('id',$exists->id)->update('tb_push_tokens',$data);
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert('tb_push_tokens',$data);
        }
    
        return $this->json(["status"=>true]);
    }

    private function json($data) {
    // ✅ always attach fresh CSRF
        $data['csrf'] = [
            "name" => $this->security->get_csrf_token_name(),
            "token" => $this->security->get_csrf_hash()
        ];
    
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
	
	 /* ================= RUN PUSH ================= */

    public function run()
    {
        echo "🚀 Smart Push Cron Started\n";

        //$slot = $this->_getCurrentSlot();
        //if(!$slot){
            //echo "⏭ Outside slot\n";
           // return;
        //}

        ///echo "🕒 Slot: $slot\n";

        $accessToken = $this->_getAccessToken();
        if(!$accessToken){
            echo "❌ Token failed\n";
            return;
        }

        $stats = ['users'=>0,'sent'=>0,'failed'=>0];

        $users = $this->db->where('is_active',1)->get('tb_push_tokens')->result();

        foreach($users as $u){

            echo "👤 {$u->device_id}\n";

            $this->_ensureUserState($u->device_id);

            //if(!$this->_canSendInSlot($u->device_id,$slot)){
             //   echo "⏭ Slot skip\n";
             //   continue;
           // }

            //if(!$this->_canSendToday($u->device_id)){
               // echo "⏭ Daily limit reached\n";
                //continue;
            //}

            $job = $this->_getJob($u->device_id,$slot);

            if(!$job){
                echo "⏭ No new job\n";
                continue;
            }

            $sent = $this->_fire($u,$job,$slot,$accessToken);

            if($sent){
                $stats['sent']++;
                $this->_incrementDaily($u->device_id);
                $this->_updateSlot($u->device_id,$slot);
                $this->_updateLastJob($u->device_id,$job->job_id);
                echo "✅ Sent Job ID: {$job->job_id}\n";
            } else {
                $stats['failed']++;
                echo "❌ Failed\n";
            }

            $stats['users']++;
        }

        echo "\n📲 FINAL REPORT\n";
        print_r($stats);
    }

    /* ================= JOB QUERY ================= */

    private function _getJob($device_id,$slot)
    {
        $state = $this->db->where('device_id',$device_id)->get('tb_user_push_state')->row();
        $lastJobId = $state->last_job_id ?? 0;

        $this->db->select('j.job_id, j.job_title, j.slug, j.min_salary, j.max_salary,
                           e.company_name,
                           c.city_name');

        $this->db->from('tb_post_job j');
        $this->db->join('tb_employer e','e.employer_id = j.employer_id','left');
        $this->db->join('tb_employer ce','ce.employer_id = j.employer_id','left');
        $this->db->join('tb_cities c','c.city_id = ce.city_id','left');

        $this->db->where('j.is_deleted',0);
        $this->db->where('j.status','active');
        $this->db->where('j.job_id >',$lastJobId);

        // 🎯 SLOT LOGIC
        if($slot == 9 || $slot == 18){
            $this->db->order_by('j.job_id','DESC');
        }
        elseif($slot == 12 || $slot == 21){
            $this->db->order_by('j.max_salary','DESC');
        }
        elseif($slot == 15){
            $this->db->order_by('RAND()');
        }

        return $this->db->limit(1)->get()->row();
    }

    private function _updateLastJob($device_id,$jobId){
        $this->db->where('device_id',$device_id)
                 ->update('tb_user_push_state',['last_job_id'=>$jobId]);
    }

    /* ================= PUSH ================= */

    private function _fire($u,$job,$slot,$accessToken)
    {
        $title = "✨ New Job";

        if($slot == 9 || $slot == 18) $title = "🔥 Jobs Matching You";
        if($slot == 12 || $slot == 21) $title = "🚀 Trending Jobs";

        $company = $job->company_name ?? 'Company';
        $location = $job->city_name ?? 'India';

        // Salary format
        $salary = "Salary Not Disclosed";
        if($job->min_salary > 0 || $job->max_salary > 0){
            $salary = "₹".$job->min_salary." - ₹".$job->max_salary;
        }

        $body = $job->job_title . " | " . $company . " | " . $salary . " | " . $location;

        $data = [
            "title"=>$title,
            "body"=>$body,
            "link"=>base_url($job->slug),
            "icon"=>base_url('assets/favicon.png'),
            "job_id"=>$job->job_id
        ];

        $res = $this->_sendPush($u->token,$data,$accessToken);

        return isset($res['name']);
    }

    private function _sendPush($token,$data,$accessToken){
        $ch = curl_init();

        curl_setopt_array($ch,[
            CURLOPT_URL=>"https://fcm.googleapis.com/v1/projects/".$this->projectId."/messages:send",
            CURLOPT_POST=>true,
            CURLOPT_HTTPHEADER=>[
                "Authorization: Bearer ".$accessToken,
                "Content-Type: application/json"
            ],
            CURLOPT_RETURNTRANSFER=>true,
            CURLOPT_POSTFIELDS=>json_encode([
                "message"=>[
                    "token"=>$token,
                    "data"=>$data
                ]
            ])
        ]);

        $res = curl_exec($ch);
        curl_close($ch);

        return json_decode($res,true);
    }

    /* ================= STATE ================= */

    private function _ensureUserState($device_id){
        $exists = $this->db->where('device_id',$device_id)->get('tb_user_push_state')->row();

        if(!$exists){
            $this->db->insert('tb_user_push_state',[
                'device_id'=>$device_id,
                'last_job_id'=>0,
                'daily_count'=>0,
                'last_sent_date'=>NULL
            ]);
        }
    }

    private function _incrementDaily($device_id){
        $this->db->set('daily_count','daily_count+1',false)
                 ->where('device_id',$device_id)
                 ->update('tb_user_push_state');
    }

    private function _updateSlot($device_id,$slot){
        $this->db->where('device_id',$device_id)
                 ->update('tb_user_push_state',['last_slot_hour'=>$slot]);
    }

    private function _canSendToday($device_id){
        $row = $this->db->where('device_id',$device_id)->get('tb_user_push_state')->row();
        $today = date('Y-m-d');

        if(!$row) return true;

        if($row->last_sent_date != $today){
            $this->db->where('device_id',$device_id)->update('tb_user_push_state',[
                'daily_count'=>0,
                'last_sent_date'=>$today
            ]);
            return true;
        }

        return ($row->daily_count < $this->dailyLimit);
    }

    private function _canSendInSlot($device_id,$slot){
        $row = $this->db->where('device_id',$device_id)->get('tb_user_push_state')->row();
        if(!$row) return true;

        $today = date('Y-m-d');
        $lastDate = date('Y-m-d', strtotime($row->updated_at));

        if($today != $lastDate) return true;

        return ($row->last_slot_hour != $slot);
    }

    private function _getCurrentSlot(){
        $h = (int)date('H');
        if($h>=9 && $h<12) return 9;
        if($h>=12 && $h<15) return 12;
        if($h>=15 && $h<18) return 15;
        if($h>=18 && $h<21) return 18;
        if($h>=21 && $h<24) return 21;
        return null;
    }

   
}