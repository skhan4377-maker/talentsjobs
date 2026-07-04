<?php
class Login_mdl extends CI_Model {
    public function __construct() {
        parent::__construct();
    }
	

    public function get_user($username, $role) {
		if (in_array($role, ['admin', 'employer'])) {
			$this->db->from('tb_employer');
		} else {
			$this->db->from('tb_candidate');
		}

		$this->db->where('role', $role)
				 ->group_start()
				 ->where('email', $username)
				 ->or_where('mobile', $username)
				 ->group_end();

		return $this->db->get()->row();
	}

    public function update_login_token($user_id, $role, $token) {
		$table = ($role == 'candidate') ? 'candidate' : 'employer';
		$id_field = ($role == 'candidate') ? 'candidate_id' : 'employer_id';

		$this->db->where($id_field, $user_id)
				 ->update($table, [
					 'login_token' => $token,
					 'last_login'  => date('Y-m-d H:i:s') // ✅ Add last_login update
				 ]);

		return $this->db->affected_rows();
	}


    public function update_last_login($user_id, $role) {
        $table = ($role === 'candidate') ? 'candidate' : 'employer';
        $id_field = ($role === 'candidate') ? 'candidate_id' : 'employer_id';

        return $this->db->where($id_field, $user_id)
                        ->update($table, ['last_login' => date('Y-m-d H:i:s')]);
    }
	
    /*public function get_token($user_id, $role) {
        $table = ($role == 'candidate') ? 'candidate' : 'employer';
        $where = ($role == 'candidate') 
                 ? ['candidate_id' => $user_id, 'role' => $role] 
                 : ['employer_id' => $user_id, 'role' => $role];
    
        return $this->db->select('login_token')
                        ->get_where($table, $where)
                        ->row()->login_token ?? null;
    }*/
	
	public function get_user_by_id ($user_id, $role) {
		$table = ($role === 'candidate') ? 'candidate' : 'employer';
		$id_field = ($role === 'candidate') ? 'candidate_id' : 'employer_id';

		return $this->db->where($id_field, $user_id)
						->where('role', $role)
						->get($table)
						->row();
	}	
	
	//jwt token use in resume builder
	public function save_refresh_token($user_id, $refresh_token, $expires_at) {
		return $this->db->insert('tb_refresh_tokens', [
			'user_id'       => $user_id,
			'refresh_token' => $refresh_token,
			'expires_at'    => date('Y-m-d H:i:s', $expires_at),
			'revoked'       => 0
		]);
	}

	
	public function verify_refresh_token($refresh_token) {
		return $this->db
			->where('refresh_token', $refresh_token)
			->where('revoked', 0)
			->where('expires_at >', date('Y-m-d H:i:s'))
			->get('tb_refresh_tokens')
			->row();
	}
	
	public function revoke_refresh_token($refresh_token) {
		return $this->db
			->where('refresh_token', $refresh_token)
			->update('tb_refresh_tokens', ['revoked' => 1]); // ✅ FIXED
	}
	/*rest api end*/

	// ✅ Get user permissions by role
    public function get_user_permissions($role_id)
    {
        // Super Admin = All permissions
        if ($role_id == 1) {
            $rows = $this->db->select('perm_key')->get('tb_permissions')->result_array();
            return array_column($rows, 'perm_key');
        }
    
        $rows = $this->db
            ->select('p.perm_key')
            ->from('tb_role_permissions rp')
            ->join('tb_permissions p', 'p.id = rp.permission_id')
            ->where('rp.role_id', $role_id)
            ->get()
            ->result_array();
    
        return array_column($rows, 'perm_key');
    }
    
    
    // ✅ Get role_id from role name
    public function get_role_id($role_name)
    {
        $row = $this->db
            ->select('id')
            ->from('tb_roles')
            ->where('role_name', $role_name)
            ->limit(1)
            ->get()
            ->row();
    
        return $row ? $row->id : null;
    }
	
	/**
	 * Check if a candidate has any active premium plan.
	 *
	 * @param int $candidate_id
	 * @return bool
	 */
	public function has_active_candidate_plan($candidate_id)
	{
		return $this->db
			->where('user_id', $candidate_id)
			->where('status', 'active')
			->where('start_date <=', date('Y-m-d H:i:s'))
			->where('end_date >=', date('Y-m-d H:i:s'))
			->count_all_results('tb_ft_user_purchases') > 0;
	}
	

}
?>
