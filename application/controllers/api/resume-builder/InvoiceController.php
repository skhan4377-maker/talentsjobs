<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 🔐 Centralized JWT REST base controller
 * Handles Authorization header + token validation
 */
require_once APPPATH . 'libraries/MY_REST_Controller.php';

class InvoiceController extends MY_REST_Controller
{
    public function __construct(){
        parent::__construct();
        $this->load->model('api/InvoiceModel');
    }

    /**
     * =====================================================
     * GET: /api/invoice?payment_id=XX
     * View / Download single invoice (logged-in user)
     * =====================================================
     */
    public function invoice_data_get(){
        $user_id    = $this->user_id; // 🔐 from JWT
        $payment_id = $this->get('payment_id');

        if (empty($payment_id)) {
            return $this->response([
                'success' => false,
                'error'   => 'Missing payment_id'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        /**
         * 🔐 SECURITY:
         * Invoice sirf wahi user dekh sakta hai
         * jiska payment record hai
         */
        $invoice = $this->InvoiceModel->getInvoiceDataByUser($payment_id, $user_id);

        if (!$invoice) {
            return $this->response([
                'success' => false,
                'error'   => 'Invoice not found or access denied'
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        return $this->response([
            'success' => true,
            'data'    => $invoice
        ], REST_Controller::HTTP_OK);
    }

    /**
     * =====================================================
     * GET: /api/invoices
     * Logged-in user purchase history (multi-plan)
     * =====================================================
     */
    public function purchase_history_get()
    {
        $user_id = $this->user_id; // 🔐 from JWT

        $invoices = $this->InvoiceModel->getUserInvoices($user_id);

        if (empty($invoices)) {
            return $this->response([
                'success' => true,
                'data'    => []
            ], REST_Controller::HTTP_OK);
        }

        /**
         * Frontend-friendly formatting
         */
        $response = array_map(function ($inv) {

            if (empty($inv['plans'])) {
                return null;
            }

            $total_amount  = 0;
            $feature_names = [];

            foreach ($inv['plans'] as $plan) {
                $total_amount += $plan['plan_total'] ?? 0;

                if (!empty($plan['feature_name'])) {
                    $feature_names[] = $plan['feature_name'];
                }
            }

            // Duration logic
            $durations = array_unique(array_column($inv['plans'], 'duration'));
            $duration  = count($durations) === 1 ? $durations[0] : 'Multiple durations';

            return [
                'payment_id'          => $inv['payment_id'],
                'razorpay_payment_id' => $inv['razorpay_payment_id'] ?? $inv['payment_id'],
                'invoice_no'          => $inv['invoice_no'],
                'status'              => ucfirst($inv['status']),
                'feature_name'        => !empty($feature_names)
                                            ? implode(', ', array_unique($feature_names))
                                            : 'Multiple Plans',
                'feature_count'       => count($inv['plans']),
                'invoiced_at'         => $inv['invoiced_at'],
                'created_at'          => $inv['created_at'],
                'paid_at'             => $inv['paid_at'],
                'amount'              => $total_amount > 0 ? $total_amount : $inv['amount'],
                'currency'            => $inv['currency'] ?? 'INR',
                'method'              => $inv['method'] ?? 'Online',
                'duration'            => $duration,
                'plan_level'          => $inv['plans'][0]['plan_level'] ?? 'Standard',
                'plans'               => $inv['plans'],
                'is_multi_plan'       => count($inv['plans']) > 1
            ];
        }, $invoices);

        // Remove null entries (safety)
        $response = array_values(array_filter($response));

        return $this->response([
            'success' => true,
            'data'    => $response
        ], REST_Controller::HTTP_OK);
    }
}
