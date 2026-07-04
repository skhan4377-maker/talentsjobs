<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ResumeTemplates_model extends CI_Model {

    protected $table = 'tb_ft_resume_templates';

    /* =====================================================
       ADMIN METHODS
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
        // Generate unique slug
        $slug = url_title($data['name'], '-', true);
        $original_slug = $slug;
        $counter = 1;
        while ($this->db->where('slug', $slug)->get($this->table)->num_rows() > 0) {
            $slug = $original_slug . '-' . $counter++;
        }

        $insert = [
            'feature_id'       => $data['feature_id'] ?? null,
            'name'             => $data['name'],
            'slug'             => $slug,
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
            'created_by'       => 1, // or get from session
            'created_at'       => date('Y-m-d H:i:s')
        ];

        $this->db->insert($this->table, $insert);
        return $this->db->insert_id();
    }

    public function update($template_id, $data) {
        // Generate unique slug (excluding current template)
        $slug = url_title($data['name'], '-', true);
        $original_slug = $slug;
        $counter = 1;
        while ($this->db->where('slug', $slug)->where('template_id !=', $template_id)->get($this->table)->num_rows() > 0) {
            $slug = $original_slug . '-' . $counter++;
        }

        $update = [
            'name'             => $data['name'],
            'slug'             => $slug,
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

        $this->db->where('template_id', $template_id);
        return $this->db->update($this->table, $update);
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
       FRONTEND API METHODS (for candidates)
    ===================================================== */

    /**
     * Get templates based on user's active feature (free/paid)
     * @param int $user_feature_id The feature_id from user subscription
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function get_templates_by_feature($user_feature_id, $limit = 20, $offset = 0) {
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
            ->where('t.is_active', 1);

        // Show templates that belong to the user's feature OR are free (feature_id=1)
        // This ensures free users see only free templates, paid users see their own + free ones
        if ($user_feature_id == 1) {
            // Free user: only free templates (feature_id = 1)
            $this->db->where('t.feature_id', 1);
        } else {
            // Paid user: templates from their feature OR free templates
            $this->db->group_start()
                ->where('t.feature_id', $user_feature_id)
                ->or_where('t.feature_id', 1)
            ->group_end();
        }

        $this->db->order_by('t.is_premium', 'ASC')  // free first, then premium
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

    /**
     * Get a single template by ID with access check
     * @param int $template_id
     * @param int $user_feature_id
     * @return array|null
     */
    public function get_template_by_id_for_user($template_id, $user_feature_id) {
        $this->db
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
                zones_supported,
                feature_id
            ')
            ->from($this->table)
            ->where('template_id', $template_id)
            ->where('is_active', 1);

        $template = $this->db->get()->row_array();
        if (!$template) return null;

        // Check if user has access
        if ($user_feature_id == 1 && $template['feature_id'] != 1) {
            // Free user trying to access a paid template
            return null;
        }
        if ($user_feature_id != 1 && $template['feature_id'] != $user_feature_id && $template['feature_id'] != 1) {
            // Paid user but template belongs to a different feature (e.g., standalone booster vs resume builder)
            return null;
        }

        $template['thumbnail_url'] = $template['preview_image']
            ? base_url($template['preview_image'])
            : null;
        unset($template['preview_image']);

        $template['layout_config']   = $this->safe_json($template['layout_config']);
        $template['schema_json']     = $this->safe_json($template['schema_json']);
        $template['zones_supported'] = $this->safe_json($template['zones_supported'], []);

        return $template;
    }

    /**
     * Get download count for a template (for popularity)
     * @param int $template_id
     * @return int
     */
    public function get_download_count($template_id) {
        $this->db->where('template_id', $template_id);
        return $this->db->count_all_results('tb_ft_resume_downloads');
    }

    /**
     * Record template usage (download or view)
     * @param int $user_id
     * @param int $template_id
     * @param string $ip
     * @param string $user_agent
     * @return bool
     */
    public function record_usage($user_id, $template_id, $ip = null, $user_agent = null) {
        $data = [
            'user_id'      => $user_id,
            'template_id'  => $template_id,
            'ip_address'   => $ip ?: $this->input->ip_address(),
            'user_agent'   => $user_agent ?: ($_SERVER['HTTP_USER_AGENT'] ?? ''),
            'created_at'   => date('Y-m-d H:i:s')
        ];
        return $this->db->insert('tb_ft_template_usage', $data);
    }

    /* =====================================================
       UTILITY METHODS
    ===================================================== */

    private function safe_json($json, $default = null) {
        if (empty($json)) return $default;
        $decoded = json_decode($json, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : $default;
    }
}