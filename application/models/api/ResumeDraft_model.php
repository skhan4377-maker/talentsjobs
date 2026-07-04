<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ResumeDraft_model extends CI_Model
{
    protected $table_drafts = 'tb_ft_resume_drafts';
    protected $table_templates = 'tb_ft_resume_templates';
    protected $table_user_purchases = 'tb_ft_user_purchases';
    protected $table_payments = 'tb_ft_payments';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_by_user($user_id) {
		$this->db->select('
			d.draft_id,
			d.user_id,
			d.form_data,
			d.template_id,
			d.updated_at,
			d.is_finalized,

			t.name AS template_name,
			t.html_layout,
			t.preview_image,
			t.template_type,
			t.is_premium,
			t.layout_type,
			t.experience_level,
			t.industry_id,

			up.id AS user_purchase_id,
			up.feature_id AS purchase_feature_id,
			up.start_date,
			up.end_date,
			up.status AS purchase_status,

			p.id AS payment_id,
			p.status AS payment_status
		');

		$this->db->from($this->table_drafts . ' d');

		// TEMPLATE JOIN
		$this->db->join(
			$this->table_templates . ' t',
			't.template_id = d.template_id',
			'left'
		);

		// 🔥 FINAL FIX (NO feature_id, NO NOW())
		$this->db->join(
			$this->table_user_purchases . ' up',
			'up.user_id = d.user_id AND up.status = "active"',
			'left'
		);

		// PAYMENT JOIN
		$this->db->join(
			$this->table_payments . ' p',
			'p.id = up.payment_id',
			'left'
		);

		$this->db->where('d.user_id', $user_id);
		$this->db->order_by('d.draft_id', 'DESC');
		$this->db->limit(1);

		return $this->db->get()->row_array();
	}

    public function save_draft($user_id, $form_data, $template_id = null)
	{
		/*
		|--------------------------------------------------------------------------
		| START TRANSACTION
		|--------------------------------------------------------------------------
		*/
		$this->db->trans_start();

		try {

			/*
			|--------------------------------------------------------------------------
			| GET EXISTING DRAFT
			|--------------------------------------------------------------------------
			*/
			$existing = $this->db
				->where('user_id', $user_id)
				->get($this->table_drafts)
				->row_array();

			$existing_data = [];

			if ($existing && !empty($existing['form_data'])) {

				$decoded = json_decode($existing['form_data'], true);

				if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
					$existing_data = $decoded;
				}
			}

			/*
			|--------------------------------------------------------------------------
			| VALIDATE REQUEST
			|--------------------------------------------------------------------------
			*/

			if (empty($form_data) || !is_array($form_data)) {

				log_message(
					'error',
					'Blocked empty/non-array draft save for user: ' . $user_id
				);

				return $existing['draft_id'] ?? false;
			}

			/*
			|--------------------------------------------------------------------------
			| REMOVE NULL / EMPTY VALUES RECURSIVELY
			|--------------------------------------------------------------------------
			*/
			$cleanRecursive = function ($array) use (&$cleanRecursive) {

				if (!is_array($array)) {
					return $array;
				}

				$cleaned = [];

				foreach ($array as $key => $value) {

					if (is_array($value)) {

						$nested = $cleanRecursive($value);

						if (!empty($nested)) {
							$cleaned[$key] = $nested;
						}

					} else {

						if (
							$value !== null &&
							$value !== '' &&
							$value !== 'null'
						) {
							$cleaned[$key] = $value;
						}
					}
				}

				return $cleaned;
			};

			$clean_data = $cleanRecursive($form_data);

			/*
			|--------------------------------------------------------------------------
			| BLOCK EMPTY OVERWRITE
			|--------------------------------------------------------------------------
			*/

			if (
				empty($clean_data) ||
				$clean_data === [] ||
				count($clean_data) === 0
			) {

				log_message(
					'error',
					'Blocked EMPTY overwrite attempt for user: ' . $user_id
				);

				return $existing['draft_id'] ?? false;
			}

			/*
			|--------------------------------------------------------------------------
			| MERGE EXISTING + NEW DATA
			|--------------------------------------------------------------------------
			*/

			$merged_data = array_replace_recursive(
				$existing_data,
				$clean_data
			);

			/*
			|--------------------------------------------------------------------------
			| FINAL SAFETY CHECK
			|--------------------------------------------------------------------------
			*/

			if (
				empty($merged_data) ||
				!is_array($merged_data)
			) {

				log_message(
					'error',
					'Merged data became empty for user: ' . $user_id
				);

				return $existing['draft_id'] ?? false;
			}

			/*
			|--------------------------------------------------------------------------
			| JSON ENCODE
			|--------------------------------------------------------------------------
			*/

			$json_data = json_encode(
				$merged_data,
				JSON_UNESCAPED_UNICODE
			);

			if (
				$json_data === false ||
				$json_data === 'null' ||
				$json_data === '{}' ||
				$json_data === '[]'
			) {

				log_message(
					'error',
					'Prevented invalid JSON overwrite for user: ' . $user_id
				);

				return $existing['draft_id'] ?? false;
			}

			/*
			|--------------------------------------------------------------------------
			| PREPARE DATA
			|--------------------------------------------------------------------------
			*/

			$data = [
				'form_data'    => $json_data,
				'updated_at'   => date('Y-m-d H:i:s'),
				'is_finalized' => 0
			];

			/*
			|--------------------------------------------------------------------------
			| TEMPLATE ID ONLY IF PRESENT
			|--------------------------------------------------------------------------
			*/

			if (!empty($template_id)) {
				$data['template_id'] = $template_id;
			}

			/*
			|--------------------------------------------------------------------------
			| UPDATE OR INSERT
			|--------------------------------------------------------------------------
			*/

			if ($existing) {

				$this->db
					->where('user_id', $user_id)
					->update($this->table_drafts, $data);

				$draft_id = $existing['draft_id'];

			} else {

				$data['user_id'] = $user_id;

				$this->db->insert(
					$this->table_drafts,
					$data
				);

				$draft_id = $this->db->insert_id();
			}

			/*
			|--------------------------------------------------------------------------
			| COMPLETE TRANSACTION
			|--------------------------------------------------------------------------
			*/

			$this->db->trans_complete();

			if ($this->db->trans_status() === false) {

				log_message(
					'error',
					'Transaction failed while saving draft for user: ' . $user_id
				);

				return false;
			}

			return $draft_id;

		} catch (Exception $e) {

			$this->db->trans_rollback();

			log_message(
				'error',
				'Draft save exception: ' . $e->getMessage()
			);

			return false;
		}
	}
    /**
     * Finalize resume after successful payment (move draft to permanent tables)
     */
    public function finalize_resume_after_payment($user_id) {
        $draft = $this->db->where('user_id', $user_id)
                          ->where('is_finalized', 0)
                          ->order_by('draft_id', 'DESC')
                          ->limit(1)
                          ->get($this->table_drafts)
                          ->row_array();

        if (!$draft) {
            return ['status' => false, 'message' => 'No draft found'];
        }

        $form_data = json_decode($draft['form_data'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['status' => false, 'message' => 'Invalid draft data'];
        }

        $result = $this->save_full_resume($user_id, $form_data);

        if ($result['status']) {
            $this->db->where('draft_id', $draft['draft_id'])
                     ->update($this->table_drafts, [
                         'is_finalized' => 1,
                         'updated_at'   => date('Y-m-d H:i:s')
                     ]);
        }

        return $result;
    }

    /**
     * Save full resume into all respective tables (personal, employment, education, skills, etc.)
     */
    public function save_full_resume($user_id, $data) {
        $this->db->trans_start();
        try {
            // 1️⃣ Personal Info (tb_candidate)
            $personal = $data['personal'] ?? [];
            if (!empty($personal)) {
                $personalData = [
                    'name'         => $personal['name'] ?? '',
                    'last_name'    => $personal['last_name'] ?? '',
                    'designations' => $personal['designation'] ?? '',
                    'mobile'       => $personal['phone'] ?? '',
                    'email'        => $personal['email'] ?? '',
                    'portfolioUrl' => $personal['portfolioUrl'] ?? '',
                    'address'      => $personal['address'] ?? '',
                    'city_id'      => $personal['city_id'] ?? null,
                    'country_id'   => $personal['country_id'] ?? null,
                    'dob'          => $personal['dob'] ?? null,
                    'about'        => $personal['summary'] ?? '',
                    'updated_at'   => date('Y-m-d H:i:s'),
                ];
                $personalData = array_filter($personalData, function($v) { return $v !== null && $v !== ''; });
                $exists = $this->db->get_where('tb_candidate', ['candidate_id' => $user_id])->row();
                if ($exists) {
                    $this->db->where('candidate_id', $user_id)->update('tb_candidate', $personalData);
                } else {
                    $personalData['candidate_id'] = $user_id;
                    $personalData['created_at']   = date('Y-m-d H:i:s');
                    $personalData['status']       = 'active';
                    $personalData['is_verified']  = 1;
                    $this->db->insert('tb_candidate', $personalData);
                }
            }

            // 2️⃣ Child tables
            $this->insert_or_update_child($user_id, 'tb_employment_history', 'employment', $data);
            $this->insert_or_update_child($user_id, 'tb_education_history', 'education', $data);
            $this->insert_or_update_child($user_id, 'tb_candidate_skills', 'skills', $data, 'name');
            $this->insert_or_update_child($user_id, 'tb_certifications', 'certifications', $data);
            $this->insert_or_update_child($user_id, 'tb_language', 'languages', $data);

            $this->db->trans_complete();
            if ($this->db->trans_status() === false) {
                throw new Exception('Transaction failed');
            }
            return ['status' => true, 'message' => 'Resume finalized successfully'];
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Resume finalization error: ' . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    private function insert_or_update_child($user_id, $table, $key, $data, $customField = null) {
        if (empty($data[$key])) return;
        $this->db->where('candidate_id', $user_id)->delete($table);
        foreach ($data[$key] as $item) {
            if (empty($item)) continue;
            $insertData = ['candidate_id' => $user_id];
            switch ($table) {
                case 'tb_employment_history':
                    $is_current = (isset($item['end_date']) && strtolower(trim($item['end_date'])) === 'present') ? 1 : 0;
                    $insertData += [
                        'job_title'        => $item['job_title'] ?? '',
                        'employer_name'    => $item['employer_name'] ?? '',
                        'start_date'       => !empty($item['start_date']) ? (strlen($item['start_date']) == 7 ? $item['start_date'] . '-01' : $item['start_date']) : null,
                        'end_date'         => ($is_current ? null : (!empty($item['end_date']) && strlen($item['end_date']) == 7 ? $item['end_date'] . '-01' : $item['end_date'])),
                        'is_current'       => $is_current,
                        'responsibilities' => $item['responsibilities'] ?? '',
                    ];
                    break;
                case 'tb_education_history':
                    $insertData += [
                        'degreeName'      => $item['degree'] ?? '',
                        'institutionName' => $item['school'] ?? '',
                        'fieldOfStudy'    => $item['field_of_study'] ?? '',
                        'startYear'       => $item['start_date'] ?? '',
                        'endYear'         => $item['end_date'] ?? '',
                        'honors'          => $item['grade'] ?? '',
                    ];
                    break;
                case 'tb_candidate_skills':
                    $insertData += [
                        'skill_name'  => $item[$customField] ?? '',
                        'skill_level' => $item['level'] ?? '',
                    ];
                    break;
                case 'tb_certifications':
                    $dateIssued = $item['date'] ?? null;
                    if (!empty($dateIssued) && preg_match('/^\d{4}-\d{2}$/', $dateIssued)) {
                        $dateIssued .= '-01';
                    }
                    $insertData += [
                        'certificationName' => $item['certificate_name'] ?? '',
                        'issuingAuthority'  => $item['issuer'] ?? '',
                        'dateIssued'        => $dateIssued,
                        'description'       => $item['description'] ?? '',
                    ];
                    break;
                case 'tb_language':
                    $insertData += [
                        'languageName'     => $item['name'] ?? '',
                        'proficiencyLevel' => $item['level'] ?? '',
                    ];
                    break;
            }
            $this->db->insert($table, $insertData);
        }
    }
}
?>