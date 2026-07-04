<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bundles_model extends CI_Model {

    protected $table_bundles = 'tb_ft_bundles';
    protected $table_bundle_features = 'tb_ft_bundle_features';

    public function __construct() {
        parent::__construct();
    }

    // Get all bundles with feature names (for listing)
    public function get_all_bundles() {
        $this->db->select('b.*, GROUP_CONCAT(f.feature_name SEPARATOR ", ") as feature_names');
        $this->db->from($this->table_bundles . ' b');
        $this->db->join($this->table_bundle_features . ' bf', 'b.bundle_id = bf.bundle_id', 'left');
        $this->db->join('tb_ft_features f', 'bf.feature_id = f.feature_id', 'left');
        $this->db->group_by('b.bundle_id');
        $this->db->order_by('b.bundle_id', 'DESC');
        return $this->db->get()->result_array();
    }

    // Get single bundle by ID
    public function get_bundle_by_id($id) {
        return $this->db->get_where($this->table_bundles, ['bundle_id' => $id])->row_array();
    }

    // Get feature IDs associated with a bundle
    public function get_bundle_features($bundle_id) {
        $result = $this->db->select('feature_id')
            ->get_where($this->table_bundle_features, ['bundle_id' => $bundle_id])
            ->result_array();
        return array_column($result, 'feature_id');
    }

    // Insert bundle
    public function insert_bundle($data) {
        $this->db->insert($this->table_bundles, $data);
        return $this->db->insert_id();
    }

    // Update bundle
    public function update_bundle($id, $data) {
        $this->db->where('bundle_id', $id);
        return $this->db->update($this->table_bundles, $data);
    }

    // Delete bundle (cascade deletes bundle_features via foreign key)
    public function delete_bundle($id) {
        $this->db->where('bundle_id', $id);
        return $this->db->delete($this->table_bundles);
    }

    // Assign features to bundle (replace existing)
    public function assign_features($bundle_id, $feature_ids) {
        // Delete existing
        $this->db->where('bundle_id', $bundle_id)->delete($this->table_bundle_features);
        if (!empty($feature_ids)) {
            foreach ($feature_ids as $fid) {
                $this->db->insert($this->table_bundle_features, [
                    'bundle_id' => $bundle_id,
                    'feature_id' => $fid
                ]);
            }
        }
        return true;
    }

    // Check if slug exists (for uniqueness)
    public function slug_exists($slug, $exclude_id = null) {
        $this->db->where('bundle_slug', $slug);
        if ($exclude_id) {
            $this->db->where('bundle_id !=', $exclude_id);
        }
        return $this->db->get($this->table_bundles)->num_rows() > 0;
    }
}
?>