<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * IMPORTANT:
 * REST base controller manually load
 */
//require APPPATH . 'libraries/MY_REST_Controller.php';

class Refund extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /* =========================================================
     * ACTIVE PLAN → REFUND REQUEST (24 HOURS)
     * POST: /api/refund/request
     * ========================================================= */
    public function request_post() {

        // 🔐 Get user_id from JWT authentication
        $user_id  = $this->user_id; // JWT से
        $order_id = trim($this->post('order_id'));
        $reason   = trim($this->post('reason'));

        if (!$user_id || !$order_id) {
            return $this->response([
                'status'  => false,
                'message' => 'User authentication and Order ID are required'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        /* ===================== TRANSACTION START ===================== */
        $this->db->trans_begin();

        /* 1️⃣ Fetch ACTIVE purchase + payment (ownership + paid check) */
        $purchase = $this->db->query("
            SELECT 
                up.id        AS user_purchase_id,
                up.status    AS purchase_status,
                p.id         AS payment_id,
                p.amount,
                p.status     AS payment_status,
                p.created_at AS payment_time
            FROM tb_ft_user_purchases up
            INNER JOIN tb_ft_payments p ON p.id = up.payment_id
            WHERE up.user_id = ?
              AND p.user_id = ?
              AND p.order_id = ?
              AND up.status = 'active'
              AND p.status = 'paid'
            LIMIT 1
        ", [$user_id, $user_id, $order_id])->row();

        if (!$purchase) {
            $this->db->trans_rollback();
            return $this->response([
                'status'  => false,
                'message' => 'Refund allowed only for ACTIVE paid plans'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        /* 2️⃣ Prevent duplicate refund request */
        $exists = $this->db->get_where('tb_ft_refund_requests', [
            'user_id'  => $user_id,
            'order_id' => $order_id
        ])->row();

        if ($exists) {
            $this->db->trans_rollback();
            return $this->response([
                'status'  => false,
                'message' => 'Refund request already submitted'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        /* 3️⃣ 24-hour refund window */
        if ((time() - strtotime($purchase->payment_time)) > (24 * 60 * 60)) {
            $this->db->trans_rollback();
            return $this->response([
                'status'  => false,
                'message' => 'Refund window expired (24 hours)'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        /* 4️⃣ Insert refund request (PENDING) */
        $this->db->insert('tb_ft_refund_requests', [
            'user_id'      => $user_id,
            'order_id'     => $order_id,
            'amount'       => $purchase->amount,
            'reason'       => $reason ?: 'Refund requested by user',
            'status'       => 'pending',
            'requested_at' => date('Y-m-d H:i:s'),
            'created_ip'   => $this->input->ip_address()
        ]);

        /* 5️⃣ OPTIONAL: mark payment as refund requested (mirror only) */
        $this->db->where('id', $purchase->payment_id)
                 ->update('tb_ft_payments', [
                     'refund_status' => 'requested'
                 ]);

        /* ===================== TRANSACTION END ===================== */
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return $this->response([
                'status'  => false,
                'message' => 'Failed to submit refund request'
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }

        $this->db->trans_commit();

        return $this->response([
            'status'  => true,
            'message' => 'Refund request submitted. Awaiting admin approval.',
            'data'    => [
                'refund_amount' => $purchase->amount
            ]
        ], REST_Controller::HTTP_OK);
    }

    /* =========================================================
     * UPCOMING PLAN → CANCEL & REFUND REQUEST
     * POST: /api/refund/cancel-upcoming
     * ========================================================= */
    public function cancel_upcoming_post() {

        // 🔐 Get user_id from JWT authentication
        $user_id  = $this->user_id;
        $order_id = trim($this->post('order_id'));

        if (!$user_id || !$order_id) {
            return $this->response([
                'status'  => false,
                'message' => 'User authentication and Order ID are required'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $this->db->trans_begin();

        /* 1️⃣ Fetch UPCOMING purchase */
        $purchase = $this->db->query("
            SELECT 
                up.id AS user_purchase_id,
                p.id  AS payment_id,
                p.amount,
                p.status AS payment_status
            FROM tb_ft_user_purchases up
            INNER JOIN tb_ft_payments p ON p.id = up.payment_id
            WHERE up.user_id = ?
              AND p.user_id = ?
              AND p.order_id = ?
              AND up.status = 'upcoming'
              AND p.status = 'paid'
            LIMIT 1
        ", [$user_id, $user_id, $order_id])->row();

        if (!$purchase) {
            $this->db->trans_rollback();
            return $this->response([
                'status'  => false,
                'message' => 'No UPCOMING paid plan found for this order'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        /* 2️⃣ Cancel upcoming plan */
        $this->db->where('id', $purchase->user_purchase_id)
                 ->update('tb_ft_user_purchases', [
                     'status'     => 'cancelled',
                     'updated_at' => date('Y-m-d H:i:s')
                 ]);

        /* 3️⃣ Create refund request */
        $this->db->insert('tb_ft_refund_requests', [
            'user_id'      => $user_id,
            'order_id'     => $order_id,
            'amount'       => $purchase->amount,
            'reason'       => 'Upcoming plan cancelled by user',
            'status'       => 'pending',
            'requested_at' => date('Y-m-d H:i:s'),
            'created_ip'   => $this->input->ip_address()
        ]);

        /* 4️⃣ Mirror update in payments */
        $this->db->where('id', $purchase->payment_id)
                 ->update('tb_ft_payments', [
                     'refund_status' => 'requested'
                 ]);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return $this->response([
                'status'  => false,
                'message' => 'Failed to cancel plan'
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }

        $this->db->trans_commit();

        return $this->response([
            'status'  => true,
            'message' => 'Upcoming plan cancelled. Refund request sent to admin.'
        ], REST_Controller::HTTP_OK);
    }
}
?>