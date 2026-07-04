<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ResumeTemplates_model extends CI_Model {

	protected $table = 'tb_ft_resume_templates';

	/* =====================================================
	   ADMIN METHODS (UNCHANGED BEHAVIOR)
	===================================================== */

	public function get_all() {
		return $this->db
			->select('t.*, i.industry_name')
			->from($this->table . ' t')
			->join('tb_industry i', 'i.industry_id = t.industry_id', 'left')
			->order_by('t.template_id', 'DESC')
			->get()
			->result_array();
	}
	
	public function get_by_id($template_id) {
		return $this->db
			->where('template_id', $template_id)
			->get($this->table)
			->row_array();
	}
	
	public function get_industries() {
		return $this->db->get('tb_industry')->result_array();
	}
	
	public function insert($data) {

		$insert = [
			'feature_id'       => $data['feature_id'] ?? null,
			'duration_id'      => $data['duration_id'] ?? null,
			'name'             => $data['name'],
			'slug'             => url_title($data['name'], '-', true),
			'layout_type'      => $data['layout_type'],
			'industry_id'      => $data['industry_id'],
			'html_layout'      => $data['html_layout'],
			'layout_config'    => $data['layout_config'] ?? null,
			'schema_json'      => $data['schema_json'] ?? null,
			'zones_supported'  => $data['zones_supported'] ?? null,
			'description'      => $data['description'] ?? null,
			'experience_level' => $data['experience_level'],
			'template_type'    => $data['template_type'],
			'preview_image'    => $data['preview_image'] ?? null,
			'template_version' => 1,
			'is_premium'       => !empty($data['is_premium']) ? 1 : 0,
			'is_active'        => !empty($data['is_active']) ? 1 : 0,
			'created_by'       => 1,
			'created_at'       => date('Y-m-d H:i:s')
		];

		$this->db->insert($this->table, $insert);
		return $this->db->insert_id();
	}

	public function update($template_id, $data) {

		$update = [
			'name'             => $data['name'],
			'slug'             => url_title($data['name'], '-', true),
			'layout_type'      => $data['layout_type'],
			'industry_id'      => $data['industry_id'],
			'html_layout'      => $data['html_layout'],
			'layout_config'    => $data['layout_config'] ?? null,
			'schema_json'      => $data['schema_json'] ?? null,
			'zones_supported'  => $data['zones_supported'] ?? null,
			'description'      => $data['description'] ?? null,
			'experience_level' => $data['experience_level'],
			'template_type'    => $data['template_type'],
			'is_premium'       => !empty($data['is_premium']) ? 1 : 0,
			'is_active'        => !empty($data['is_active']) ? 1 : 0,
			'updated_at'       => date('Y-m-d H:i:s')
		];

		if (!empty($data['preview_image'])) {
			$update['preview_image'] = $data['preview_image'];
		}

		return $this->db
			->where('template_id', $template_id)
			->update($this->table, $update);
	}

	public function delete($template_id) {
		return $this->db
			->where('template_id', $template_id)
			->delete($this->table);
	}

	public function get_total_count() {
		return $this->db
			->where('is_active', 1)
			->from($this->table)
			->count_all_results();
	}

	/* =====================================================
	   API METHODS (ENGINE READY)
	===================================================== */

	public function api_get_all($limit = 20, $offset = 0) {

		$this->db
			->select('
				t.template_id,
				t.slug,
				t.name,
				t.template_type,
				t.layout_type,
				t.experience_level,
				t.is_premium,
				t.template_version,
				t.description,
				t.preview_image,
				t.layout_config,
				t.schema_json,
				t.zones_supported,
				i.industry_id,
				i.industry_name
			')
			->from($this->table . ' t')
			->join('tb_industry i', 'i.industry_id = t.industry_id', 'left')
			->where('t.is_active', 1)
			->order_by('t.template_id', 'DESC')
			->limit($limit, $offset);

		$rows = $this->db->get()->result_array();

		foreach ($rows as &$r) {
			$r['thumbnail_url'] = $r['preview_image']
				? base_url($r['preview_image'])
				: null;

			unset($r['preview_image']);

			$r['layout_config']   = $this->safe_json($r['layout_config']);
			$r['schema_json']     = $this->safe_json($r['schema_json']);
			$r['zones_supported'] = $this->safe_json($r['zones_supported'], []);

			if (empty($r['industry_name'])) {
				$r['industry_name'] = 'General';
				$r['industry_id']   = null;
			}
		}

		return $rows;
	}

	public function api_get_by_id($template_id) {

		$r = $this->db
			->select('
				template_id,
				slug,
				name,
				html_layout,
				template_type,
				layout_type,
				experience_level,
				is_premium,
				template_version,
				description,
				preview_image,
				layout_config,
				schema_json,
				zones_supported
			')
			->from($this->table)
			->where('template_id', $template_id)
			->get()
			->row_array();

		if (!$r) return null;

		$r['thumbnail_url'] = $r['preview_image']
			? base_url($r['preview_image'])
			: null;

		unset($r['preview_image']);

		$r['layout_config']   = $this->safe_json($r['layout_config']);
		$r['schema_json']     = $this->safe_json($r['schema_json']);
		$r['zones_supported'] = $this->safe_json($r['zones_supported'], []);

		return $r;
	}

	/* =====================================================
	   UTIL
	===================================================== */

	private function safe_json($json, $default = null) {
		if (empty($json)) return $default;
		$decoded = json_decode($json, true);
		return json_last_error() === JSON_ERROR_NONE ? $decoded : $default;
	}
	
	// use this function for resume ads show for frontend tempalte stats
	public function get_download_count($template_id) {
		$this->db->where('template_id', $template_id);
		return $this->db->count_all_results('tb_ft_resume_downloads');
	}
}