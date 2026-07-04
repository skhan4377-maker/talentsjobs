<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Campaigns extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('admin/Campaign_model');
        $this->load->library('form_validation');
    }

    // ===============================
    // CAMPAIGN LIST PAGE
    // ===============================
    public function index() {
        $data['title'] = 'Campaigns';
        $data['content'] = $this->load->view('admin/campaigns/list', $data, TRUE);
        $this->load->view('templates/master', $data);
    }

    // ===============================
    // AJAX: LOAD CAMPAIGN LIST (with filters, stats, pagination)
    // ===============================
    public function ajax_list() {
        $filters = [
            'search'     => $this->input->get('search'),
            'status'     => $this->input->get('status'),
            'start_from' => $this->input->get('start_from'),
            'start_to'   => $this->input->get('start_to'),
        ];
        $page     = (int) $this->input->get('page') ?: 1;
        $per_page = 10;

        $result = $this->Campaign_model->get_filtered($filters, $page, $per_page);
        $campaigns  = $result['campaigns'];
        $total_rows = $result['total_rows'];

        $data = [];
        $no = ($page - 1) * $per_page + 1;
        foreach ($campaigns as $c) {
            $row = [];
            $row['no']           = $no++;
            $row['title']        = htmlspecialchars($c->title);
            $row['start_date']   = date('d M Y', strtotime($c->start_date));
            $row['end_date']     = date('d M Y', strtotime($c->end_date));
            $row['status_badge'] = '<span class="badge bg-' . ($c->status == 'active' ? 'success' : 'secondary') . '">' . ucfirst($c->status) . '</span>';
            $row['total_leads']  = $c->total_leads;
            $row['pending']      = $c->pending;
            $row['sent']         = $c->sent;
            $row['failed']       = $c->failed;
            $row['actions']      = '
                <button class="btn btn-sm btn-info upload-btn" data-id="' . $c->id . '">
                    <i class="fas fa-upload"></i> Upload
                </button>
                <button class="btn btn-sm btn-warning edit-btn" data-id="' . $c->id . '">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-danger delete-btn" data-id="' . $c->id . '">
                    <i class="fas fa-trash"></i>
                </button>';
            $data[] = $row;
        }

        $total_pages = ceil($total_rows / $per_page);

        echo json_encode([
            'data'       => $data,
            'pagination' => [
                'current_page' => $page,
                'total_pages'  => $total_pages,
                'total_rows'   => $total_rows,
                'per_page'     => $per_page
            ],
            'csrf_token' => $this->security->get_csrf_hash(),
            'csrf_name'  => $this->security->get_csrf_token_name()
        ]);
    }

    // ===============================
    // AJAX: GET SINGLE CAMPAIGN (for edit modal)
    // ===============================
    public function ajax_get($id) {
        $campaign = $this->Campaign_model->get($id);
        if ($campaign) {
            echo json_encode(['success' => true, 'data' => $campaign]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Campaign not found']);
        }
    }

    // ===============================
    // AJAX: ADD NEW CAMPAIGN
    // ===============================
    public function ajax_add() {
        $this->form_validation->set_rules('title', 'Title', 'required|max_length[255]');
        $this->form_validation->set_rules('start_date', 'Start Date', 'required');
        $this->form_validation->set_rules('end_date', 'End Date', 'required');
        $this->form_validation->set_rules('status', 'Status', 'required|in_list[active,inactive]');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $data = [
			'title'         => $this->input->post('title'),
			'description'   => $this->input->post('description'),
			'email_service' => $this->input->post('email_service'), 
			'start_date'    => $this->input->post('start_date'),
			'end_date'      => $this->input->post('end_date'),
			'status'        => $this->input->post('status')
		];

        $insert_id = $this->Campaign_model->insert($data);
        if ($insert_id) {
            echo json_encode([
                'success'    => true,
                'message'    => 'Campaign added successfully',
                'csrf_token' => $this->security->get_csrf_hash(),
                'csrf_name'  => $this->security->get_csrf_token_name()
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add campaign']);
        }
    }

    // ===============================
    // AJAX: UPDATE CAMPAIGN
    // ===============================
    public function ajax_update() {
        $id = $this->input->post('id');
        $this->form_validation->set_rules('title', 'Title', 'required|max_length[255]');
        $this->form_validation->set_rules('start_date', 'Start Date', 'required');
        $this->form_validation->set_rules('end_date', 'End Date', 'required');
        $this->form_validation->set_rules('status', 'Status', 'required|in_list[active,inactive]');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $data = [
			'title'         => $this->input->post('title'),
			'description'   => $this->input->post('description'),
			'email_service' => $this->input->post('email_service'),
			'start_date'    => $this->input->post('start_date'),
			'end_date'      => $this->input->post('end_date'),
			'status'        => $this->input->post('status')
		];

        $updated = $this->Campaign_model->update($id, $data);
        if ($updated) {
            echo json_encode([
                'success'    => true,
                'message'    => 'Campaign updated successfully',
                'csrf_token' => $this->security->get_csrf_hash(),
                'csrf_name'  => $this->security->get_csrf_token_name()
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update campaign']);
        }
    }

    // ===============================
    // AJAX: DELETE CAMPAIGN
    // ===============================
    public function ajax_delete($id) {
        $deleted = $this->Campaign_model->delete($id);
        if ($deleted) {
            echo json_encode([
                'success'    => true,
                'message'    => 'Campaign deleted',
                'csrf_token' => $this->security->get_csrf_hash(),
                'csrf_name'  => $this->security->get_csrf_token_name()
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Delete failed']);
        }
    }

  
	public function ajax_upload_leads() {
		// ==================== CONFIGURATION ====================
		$MAX_ROWS = 50000;       // maximum allowed data rows (excluding header)
		$BATCH_SIZE = 5000;      // rows processed per batch
		// =======================================================

		ini_set('memory_limit', '1024M');
		ini_set('max_execution_time', '900');
		set_time_limit(900);

		$campaign_id = $this->input->post('campaign_id');
		if (!$campaign_id) {
			echo json_encode(['success' => false, 'message' => 'Campaign ID required']);
			return;
		}

		$campaign = $this->Campaign_model->get($campaign_id);
		if (!$campaign) {
			echo json_encode(['success' => false, 'message' => 'Campaign not found']);
			return;
		}

		if ($campaign->status != 'active') {
			echo json_encode(['success' => false, 'message' => 'Only active campaigns can receive leads']);
			return;
		}

		if (empty($_FILES['leads_csv']['name'])) {
			echo json_encode(['success' => false, 'message' => 'No file uploaded']);
			return;
		}

		$config['upload_path']   = FCPATH . 'uploads/tmp/';
		$config['allowed_types'] = 'csv';
		$config['max_size']      = 153600; // 150 MB
		$config['file_ext_tolower'] = TRUE;

		if (!is_dir($config['upload_path'])) {
			mkdir($config['upload_path'], 0755, true);
		}

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload('leads_csv')) {
			echo json_encode(['success' => false, 'message' => $this->upload->display_errors()]);
			return;
		}

		$upload_data = $this->upload->data();
		$file_path   = $upload_data['full_path'];

		// ---------- Open file and read header ----------
		if (($handle = fopen($file_path, 'r')) === FALSE) {
			unlink($file_path);
			echo json_encode(['success' => false, 'message' => 'Failed to open file']);
			return;
		}

		$headers = fgetcsv($handle);
		if ($headers === false) {
			fclose($handle);
			unlink($file_path);
			echo json_encode(['success' => false, 'message' => 'Empty or unreadable CSV header']);
			return;
		}

		$header_lower = array_map('strtolower', $headers);
		$email_col       = array_search('email', $header_lower);
		$name_col        = array_search('name', $header_lower);
		$designation_col = array_search('designation', $header_lower);

		if ($email_col === false) {
			fclose($handle);
			unlink($file_path);
			echo json_encode(['success' => false, 'message' => 'CSV must contain "email" column']);
			return;
		}

		// ---------- Batch processor (unchanged) ----------
		$process_batch = function($rows) use ($campaign_id, $campaign) {
			$unique_rows = [];
			foreach ($rows as $r) {
				$email = $r['email'];
				if (!isset($unique_rows[$email])) {
					$unique_rows[$email] = $r;
				}
			}
			$emails = array_keys($unique_rows);

			$candidate_map = [];
			foreach (array_chunk($emails, 1000) as $chunk) {
				$result = $this->db
					->select('email, candidate_id')
					->where_in('email', $chunk)
					->get('tb_candidate')
					->result();
				foreach ($result as $r) {
					$candidate_map[$r->email] = $r->candidate_id;
				}
			}

			$existing_emails = [];
			foreach (array_chunk($emails, 1000) as $chunk) {
				$result = $this->db
					->select('email')
					->where('campaign_id', $campaign_id)
					->where_in('email', $chunk)
					->get('tb_campaign_queue')
					->result();
				foreach ($result as $r) {
					$existing_emails[$r->email] = true;
				}
			}

			$insert_data = [];
			$skipped_in_db = 0;
			foreach ($unique_rows as $email => $info) {
				if (isset($existing_emails[$email])) {
					$skipped_in_db++;
					continue;
				}
				$insert_data[] = [
					'campaign_id'  => $campaign_id,
					'candidate_id' => $candidate_map[$email] ?? 0,
					'email'        => $email,
					'name'         => $info['name'],
					'designation'  => $info['designation'],
					'status'       => 'pending',
					'created_at'   => date('Y-m-d H:i:s'),
					'scheduled_at' => date('Y-m-d H:i:s')
				];
			}

			$inserted = 0;
			if (!empty($insert_data)) {
				foreach (array_chunk($insert_data, 1000) as $chunk) {
					$sql = "INSERT IGNORE INTO tb_campaign_queue 
							(campaign_id, candidate_id, email, name, designation, status, created_at, scheduled_at) VALUES ";
					$values = [];
					foreach ($chunk as $row) {
						$values[] = "(" 
							. $this->db->escape($row['campaign_id']) . ", "
							. $this->db->escape($row['candidate_id']) . ", "
							. $this->db->escape($row['email']) . ", "
							. $this->db->escape($row['name']) . ", "
							. $this->db->escape($row['designation']) . ", "
							. $this->db->escape($row['status']) . ", "
							. $this->db->escape($row['created_at']) . ", "
							. $this->db->escape($row['scheduled_at']) . ")";
					}
					$sql .= implode(', ', $values);
					$this->db->query($sql);
					$inserted += $this->db->affected_rows();
				}
			}

			return [
				'inserted'            => $inserted,
				'skipped_in_db'       => $skipped_in_db,
				'duplicates_in_batch' => count($rows) - count($unique_rows)
			];
		};

		// ---------- Read rows with limit & error detection ----------
		$batch = [];
		$total_inserted = 0;
		$total_skipped_db = 0;
		$total_invalid_emails = 0;
		$total_dup_in_batch = 0;
		$line_number = 1;         // header is line 1
		$row_count = 0;           // data rows read so far

		while (($row = fgetcsv($handle)) !== FALSE) {
			$line_number++;
			$row_count++;

			// --- ROW LIMIT CHECK ---
			if ($row_count > $MAX_ROWS) {
				fclose($handle);
				unlink($file_path);
				echo json_encode([
					'success' => false,
					'message' => "File contains more than {$MAX_ROWS} data rows. Please reduce the number of rows and try again."
				]);
				return;
			}

			$email = trim($row[$email_col] ?? '');
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$total_invalid_emails++;
				continue;
			}

			$batch[] = [
				'email'       => $email,
				'name'        => ($name_col !== false && isset($row[$name_col])) ? trim($row[$name_col]) : '',
				'designation' => ($designation_col !== false && isset($row[$designation_col])) ? trim($row[$designation_col]) : ''
			];

			if (count($batch) >= $BATCH_SIZE) {
				$res = $process_batch($batch);
				$total_inserted += $res['inserted'];
				$total_skipped_db += $res['skipped_in_db'];
				$total_dup_in_batch += $res['duplicates_in_batch'];
				$batch = [];
			}
		}

		// ---------- Post-loop: check WHY the loop ended ----------
		if (!feof($handle)) {
			// fgetcsv() stopped because of a parsing error, NOT end-of-file
			$error_info = error_get_last();
			fclose($handle);
			unlink($file_path);
			echo json_encode([
				'success' => false,
				'message' => "CSV parsing error at line {$line_number}. " . ($error_info['message'] ?? 'Unknown error')
			]);
			return;
		}

		// ---------- Normal EOF reached ----------
		fclose($handle);
		unlink($file_path);

		// Process last batch
		if (!empty($batch)) {
			$res = $process_batch($batch);
			$total_inserted += $res['inserted'];
			$total_skipped_db += $res['skipped_in_db'];
			$total_dup_in_batch += $res['duplicates_in_batch'];
		}

		// ---------- Success response ----------
		$message = "Uploaded {$total_inserted} leads";
		if ($total_skipped_db > 0) $message .= ", skipped {$total_skipped_db} already existing";
		if ($total_dup_in_batch > 0) $message .= ", {$total_dup_in_batch} duplicates within file ignored";
		if ($total_invalid_emails > 0) $message .= ", {$total_invalid_emails} invalid emails";

		echo json_encode([
			'success'    => true,
			'message'    => $message,
			'inserted'   => $total_inserted,
			'duplicates' => $total_skipped_db + $total_dup_in_batch,
			'errors'     => $total_invalid_emails,
			'csrf_token' => $this->security->get_csrf_hash(),
			'csrf_name'  => $this->security->get_csrf_token_name()
		]);
	}
}