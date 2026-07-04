<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AdminRefunds extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('admin/Refund_model');
        $this->load->model('admin/Payment_model');
        $this->load->model('admin/AdminUser_model');
        $this->load->helper('url');
    }

    public function index() {

        $data['title'] = 'Refund Requests';

        $filter = [
            'status'    => $this->input->get('status'),
            'date_from'=> $this->input->get('date_from'),
            'date_to'  => $this->input->get('date_to'),
            'search'   => $this->input->get('search')
        ];

        $data['refunds'] = $this->Refund_model->get_all_refund_requests($filter);
        $data['stats']   = $this->Refund_model->get_refund_statistics();

        $data['content'] = $this->load->view('admin/refunds/index', $data, true);
        $this->load->view('templates/master', $data);
    }

    public function view($id) {

        $data['title']  = 'Refund Request Details';
        $data['refund'] = $this->Refund_model->get_refund_request_by_id($id);

        if (!$data['refund']) {
            $this->session->set_flashdata('error', 'Refund request not found');
            redirect('admin/refunds');
        }

        $this->load->model('admin/PurchasePlan_model');

        $data['plan']    = $this->PurchasePlan_model
            ->get_plan_by_order_id($data['refund']['order_id']);

        $data['user']    = $this->AdminUser_model
            ->get_user_by_id($data['refund']['user_id']);

        $data['payment'] = $this->Payment_model
            ->get_payment_by_order_id($data['refund']['order_id']);

        $data['content'] = $this->load->view('admin/refunds/view', $data, true);
        $this->load->view('templates/master', $data);
    }

    /**
     * Admin decision only (NO gateway call)
     */
    public function process_refund() {

        $refund_id = (int) $this->input->post('refund_id');
        $action    = $this->input->post('action');
        $notes     = $this->input->post('admin_notes');

        $refund = $this->Refund_model->get_refund_request_by_id($refund_id);

        if (!$refund || $refund['status'] !== 'pending') {
            $this->session->set_flashdata('error', 'Invalid refund request');
            redirect('admin/refunds');
        }

        $this->db->trans_begin();

        /* ===================== REJECT ===================== */
        if ($action === 'reject') {

            $this->Refund_model->update_refund_status(
                $refund_id,
                'rejected',
                ['admin_notes' => $notes]
            );

            $this->Refund_model->log_action(
                $refund_id,
                'pending',
                'rejected',
                $notes
            );

            $this->db->trans_commit();

            $this->session->set_flashdata('success', 'Refund rejected');
            redirect('admin/refunds/view/' . $refund_id);
            return;
        }

        /* ===================== APPROVE ===================== */
        $this->Refund_model->update_refund_status(
            $refund_id,
            'approved',
            [
                'admin_notes' => $notes,
                'processed_by'=> $this->session->user_id
            ]
        );

        $this->Refund_model->log_action(
            $refund_id,
            'pending',
            'approved',
            $notes
        );

        $this->db->trans_commit();

        $this->session->set_flashdata(
            'success',
            'Refund approved and queued for processing'
        );

        redirect('admin/refunds/view/' . $refund_id);
    }

    /**
     * Bulk reject only
     */
    public function bulk_action() {

        $action     = $this->input->post('bulk_action');
        $refund_ids = $this->input->post('refund_ids');

        if (empty($refund_ids) || !is_array($refund_ids)) {
            $this->session->set_flashdata('error', 'Please select refund requests');
            redirect('admin/refunds');
        }

        if ($action !== 'reject_selected') {
            $this->session->set_flashdata('error', 'Invalid bulk action');
            redirect('admin/refunds');
        }

        foreach ($refund_ids as $id) {

            $refund = $this->Refund_model->get_refund_request_by_id($id);

            if (!$refund || $refund['status'] !== 'pending') {
                continue;
            }

            $this->db->trans_begin();

            $this->Refund_model->update_refund_status(
                $id,
                'rejected',
                ['admin_notes' => 'Bulk rejected by admin']
            );

            $this->Refund_model->log_action(
                $id,
                'pending',
                'rejected',
                'Bulk rejected by admin'
            );

            $this->db->trans_commit();
        }

        $this->session->set_flashdata(
            'success',
            'Selected refund requests rejected successfully'
        );

        redirect('admin/refunds');
    }
}
