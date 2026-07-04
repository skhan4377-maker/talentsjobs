<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AdminRole extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('admin/Role_model');
        $this->load->library('form_validation');
    }

    /* ===============================
       ROLE LIST
    =============================== */
    public function index() {
        $data['title'] = 'Manage Roles';
        $data['roles'] = $this->Role_model->get_roles();
        $data['content'] = $this->load->view('admin/roles/index', $data, true);
        $this->load->view('templates/master', $data);
    }

    /* ===============================
       CREATE ROLE
    =============================== */
    public function create() {
        $data['title'] = 'Create Role';
        $data['content'] = $this->load->view('admin/roles/create', [], true);
        $this->load->view('templates/master', $data);
    }

    public function store() {
        $this->form_validation->set_rules('role_name', 'Role Name', 'required|trim|is_unique[tb_roles.role_name]');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('admin/AdminRole/create');
        }

        $this->Role_model->insert_role($this->input->post('role_name', true));

        $this->session->set_flashdata('success', 'Role created successfully');
        redirect('admin/AdminRole');
    }

    /* ===============================
       EDIT ROLE
    =============================== */
    public function edit($id) {
		$data['role'] = $this->Role_model->get_role($id);
		if (!$data['role']) show_404();

		$data['title'] = 'Edit Role';
		$data['content'] = $this->load->view('admin/roles/create', $data, true);
		$this->load->view('templates/master', $data);
	}

    public function update($id) {
		$role_name = $this->input->post('role_name', true);

		if(empty($role_name)){
			$this->session->set_flashdata('error','Role name is required');
			redirect('admin/AdminRole/edit/'.$id);
		}

		$this->Role_model->update_role($id, $role_name);

		$this->session->set_flashdata('success','Role updated successfully');
		redirect('admin/AdminRole');
	}

    /* ===============================
       DELETE ROLE
    =============================== */
    public function delete($id)
	{
		$role = $this->Role_model->get_role($id);

		if($role->role_name == 'super_admin'){
			$this->session->set_flashdata('error','Super Admin role cannot be deleted');
			redirect('admin/AdminRole');
		}

		$this->Role_model->delete_role($id);
		$this->session->set_flashdata('success','Role deleted successfully');
		redirect('admin/AdminRole');
	}

    /* ===============================
       PERMISSIONS
    =============================== */
    public function permissions($role_id) {
		$data['role'] = $this->Role_model->get_role($role_id);
		if (!$data['role']) show_404();

		// ❌ Block Super Admin permission editing
		if ($data['role']->role_name === 'super_admin') {
			$this->session->set_flashdata('error','Super Admin permissions cannot be modified');
			redirect('admin/AdminRole');
		}

		$data['title'] = 'Assign Permissions';
		$data['permissions'] = $this->Role_model->get_permissions();
		$data['assigned'] = $this->Role_model->get_role_permissions($role_id);

		$data['content'] = $this->load->view('admin/roles/permissions', $data, true);
		$this->load->view('templates/master', $data);
	}

    public function save_permissions() {
        $role_id = $this->input->post('role_id');
        $permissions = $this->input->post('permissions');

        $this->Role_model->save_role_permissions($role_id, $permissions);

        $this->session->set_flashdata('success', 'Permissions updated successfully');
        redirect('admin/AdminRole');
    }
}