<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Role_model extends CI_Model {

    /* ROLE CRUD */
    public function get_roles() {
        return $this->db->order_by('id','DESC')->get('tb_roles')->result();
    }

    public function get_role($id) {
        return $this->db->where('id',$id)->get('tb_roles')->row();
    }

    public function insert_role($role_name){
        return $this->db->insert('tb_roles',[
            'role_name' => strtolower(trim($role_name))
        ]);
    }

    public function update_role($id,$role_name){
        return $this->db->where('id',$id)->update('tb_roles',[
            'role_name' => strtolower(trim($role_name))
        ]);
    }

    public function delete_role($id){
        $this->db->where('role_id',$id)->delete('tb_role_permissions');
        return $this->db->where('id',$id)->delete('tb_roles');
    }

    /* PERMISSIONS */
    public function get_permissions() {
        return $this->db->order_by('id','ASC')->get('tb_permissions')->result();
    }

    public function get_role_permissions($role_id) {
        $rows = $this->db->where('role_id',$role_id)->get('tb_role_permissions')->result();
        return array_column($rows,'permission_id');
    }

    public function save_role_permissions($role_id, $permissions) {
        $this->db->where('role_id',$role_id)->delete('tb_role_permissions');

        if(!empty($permissions)){
            $insert_data = [];
            foreach($permissions as $perm){
                $insert_data[] = [
                    'role_id'=>$role_id,
                    'permission_id'=>$perm
                ];
            }
            $this->db->insert_batch('tb_role_permissions', $insert_data);
        }
    }
	
}