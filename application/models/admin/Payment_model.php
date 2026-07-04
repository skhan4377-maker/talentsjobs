<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_model extends CI_Model {

    /* ===================== FETCH ===================== */

    public function get_payment_by_id($id) {

        if (empty($id)) {
            return null;
        }

        return $this->db
            ->where('id', $id)
            ->get('tb_ft_payments')
            ->row_array();
    }

    public function get_payment_by_order_id($order_id) {

        if (empty($order_id)) {
            return null;
        }

        return $this->db
            ->where('order_id', $order_id)
            ->get('tb_ft_payments') 
            ->row_array();
    }

    /* ===================== REFUND UPDATE ===================== */
	
    public function update_payment_refund_status(
        $order_id,
        $refund_status,
        $refund_amount = 0
    ) {

        if (empty($order_id) || empty($refund_status)) {
            return false;
        }

        $data = [
            'refund_status' => $refund_status,
            'updated_at'    => date('Y-m-d H:i:s')
        ];

        if ($refund_status === 'processed') {
            $data['refund_amount'] = $refund_amount;
            $data['refund_date']   = date('Y-m-d H:i:s');
        }

        if ($refund_status === 'rejected') {
            $data['refund_amount'] = 0.00;
            $data['refund_date']   = NULL;
        }

        $this->db->where('order_id', $order_id);
        return $this->db->update('tb_ft_payments', $data); 
    }
}