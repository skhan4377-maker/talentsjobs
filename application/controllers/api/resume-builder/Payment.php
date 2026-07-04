<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once FCPATH . 'vendor/autoload.php';
use Razorpay\Api\Api;

class Payment extends CI_Controller {

    private $api;
    private $key_id;

    public function __construct() {
        parent::__construct();
        $this->load->model('api/Payment_model');
        $this->load->model('api/Cart_model');
        $this->load->model('candidate/Profile_mdl');
        $this->config->load('razorpay', TRUE);

        $razorpay_key_id     = $this->config->item('razorpay_key_id', 'razorpay');
        $razorpay_key_secret = $this->config->item('razorpay_key_secret', 'razorpay');

        $this->api = new Api($razorpay_key_id, $razorpay_key_secret);
        $this->key_id = $razorpay_key_id;

        // All methods are accessed via AJAX, so ensure JSON output
        header('Content-Type: application/json');
    }

    /**
     * Helper to output JSON consistently
     */
    private function _json_output($data, $status_code = 200) {
        $this->output
            ->set_content_type('application/json')
            ->set_status_header($status_code)
            ->set_output(json_encode($data));
    }

    /**
     * POST: /payment/create-order
     * Creates a Razorpay order and returns key, order_id, amount
     */
    public function create_order() {
        $user_id = $this->session->userdata('user_id');
        if (!$user_id) {
            return $this->_json_output(['success' => false, 'error' => 'Login required'], 401);
        }

        try {
            $cart = $this->Cart_model->getUserCart($user_id);
            if (empty($cart['items'])) {
                return $this->_json_output(['success' => false, 'error' => 'Cart is empty'], 400);
            }

            $amount = (int)($cart['summary']['grand_total'] * 100);
            $receipt = 'rcpt_' . time();

            $order = $this->api->order->create([
                'amount'   => $amount,
                'currency' => 'INR',
                'receipt'  => $receipt,
                'notes'    => [
                    'user_id' => $user_id,
                    'item_count' => count($cart['items']),
                    'cart_total' => $cart['summary']['grand_total']
                ]
            ]);

            // In create_order(), after the successful order creation:
			$this->_json_output([
				'success'  => true,
				'order_id' => $order['id'],
				'amount'   => $order['amount'],
				'currency' => $order['currency'],
				'key'      => $this->key_id,
				'cart_summary' => $cart['summary'],
				// ADD these two lines:
				'csrf_token' => $this->security->get_csrf_hash(),
				'csrf_name'  => $this->security->get_csrf_token_name()
			]);
        } catch (Exception $e) {
            $this->_save_failed_event([
                'user_id' => $user_id,
                'event' => 'order_creation_error',
                'error' => $e->getMessage(),
                'data' => json_encode(['user_id' => $user_id])
            ]);
            $this->_json_output(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * POST: /payment/verify-payment
     * Verifies Razorpay signature, saves payment, activates plans
     */
    public function verify_payment() {
        $user_id = $this->session->userdata('user_id');
        if (!$user_id) {
            return $this->_json_output(['success' => false, 'error' => 'Login required'], 401);
        }

        $input = $this->input->post(NULL, TRUE);
        $is_retry = !empty($input['is_retry']);

        if (empty($input['razorpay_order_id']) || empty($input['razorpay_payment_id']) || (!$is_retry && empty($input['razorpay_signature']))) {
            $this->_save_failed_event([
                'user_id' => $user_id,
                'event'   => 'invalid_request',
                'error'   => 'Missing required fields',
                'data'    => json_encode($input)
            ]);
            return $this->_json_output(['success' => false, 'error' => 'Missing required fields'], 400);
        }

        try {
            // Verify signature (skip for retry)
            if (!$is_retry) {
                $attributes = [
                    'razorpay_order_id'   => $input['razorpay_order_id'],
                    'razorpay_payment_id' => $input['razorpay_payment_id'],
                    'razorpay_signature'  => $input['razorpay_signature'],
                ];
                $this->api->utility->verifyPaymentSignature($attributes);
            }

            // Duplicate check
            if ($this->Payment_model->paymentExists($input['razorpay_payment_id'])) {
                $this->_save_failed_event([
                    'user_id'    => $user_id,
                    'order_id'   => $input['razorpay_order_id'],
                    'payment_id' => $input['razorpay_payment_id'],
                    'error'      => 'Duplicate payment attempt'
                ]);
                return $this->_json_output(['success' => false, 'error' => 'Payment already processed'], 400);
            }

            // Fetch payment from Razorpay
            $payment = $this->api->payment->fetch($input['razorpay_payment_id']);
            $amount  = $payment['amount'] / 100;
            $method  = $payment['method'] ?? 'Razorpay';

            // Save payment
            $invoice_data = $this->Payment_model->saveOrder([
                'user_id'    => $user_id,
                'order_id'   => $input['razorpay_order_id'],
                'payment_id' => $input['razorpay_payment_id'],
                'amount'     => $amount,
                'currency'   => 'INR',
                'status'     => 'paid',
                'method'     => $method,
            ]);

            // Get cart summary for email
            $cart = $this->Cart_model->getUserCart($user_id);
            $cart_summary = $cart['summary'] ?? [];

            // Process each cart item (supports bundles)
            $activated_plans = [];
            $failed_plans = [];
            $cart_items = null;

            if (!empty($input['cart_items'])) {
                // Ensure it's an array (could be a JSON string from React)
                $cart_items = is_array($input['cart_items']) ? $input['cart_items'] : json_decode($input['cart_items'], true);
                if ($cart_items) {
                    foreach ($cart_items as $item) {
                        try {
                            $plan = $this->Payment_model->getPlanDuration($item['feature_id'], $item['plan_id']);
                            $duration = $plan ? $plan->duration : '1 Month';

                            $user_purchase_id = $this->Payment_model->addUserPurchase(
                                $user_id,
                                $item['feature_id'],
                                $item['plan_id'],
                                $invoice_data['payment_id'],
                                $input['razorpay_payment_id'],
                                $duration
                            );

                            $activated_plans[] = [
                                'user_purchase_id' => $user_purchase_id,
                                'feature_id'       => $item['feature_id'],
                                'plan_id'          => $item['plan_id'],
                                'duration'         => $duration
                            ];
                        } catch (Exception $e) {
                            $failed_plans[] = [
                                'feature_id' => $item['feature_id'] ?? 'unknown',
                                'plan_id'    => $item['plan_id'] ?? 'unknown',
                                'error'      => $e->getMessage()
                            ];
                        }
                    }
                }
            }

            // Log partial failures
            if (!empty($failed_plans)) {
                $this->_save_failed_event([
                    'user_id'    => $user_id,
                    'order_id'   => $input['razorpay_order_id'],
                    'payment_id' => $input['razorpay_payment_id'],
                    'amount'     => $amount,
                    'error'      => 'Partial plan activation failure',
                    'data'       => json_encode($failed_plans)
                ]);
            }

            // Clear cart only if no failures
            if (empty($failed_plans)) {
                $this->Cart_model->clearCartByUser($user_id);
            }

            // Send confirmation emails
            $payment_data = [
                'user_id'         => $user_id,
                'invoice_no'      => $invoice_data['invoice_no'],
                'payment_id'      => $input['razorpay_payment_id'],
                'order_id'        => $input['razorpay_order_id'],
                'amount'          => $amount,
                'method'          => $method,
                'cart_items'      => $cart_items ?? [],
                'activated_plans' => $activated_plans,
                'failed_plans'    => $failed_plans,
                'cart_summary'    => $cart_summary
            ];
            $email_result = $this->_send_payment_confirmation_emails($payment_data);

            if (!$email_result['user_email'] || !$email_result['admin_email']) {
                $this->_save_failed_event([
                    'user_id'    => $user_id,
                    'order_id'   => $input['razorpay_order_id'],
                    'payment_id' => $input['razorpay_payment_id'],
                    'event'      => 'email_partial_failure',
                    'data'       => json_encode($email_result)
                ]);
            }

            $this->_json_output([
                'success'          => true,
                'message'          => 'Payment verified successfully',
                'invoice_no'       => $invoice_data['invoice_no'],
                'activated_plans'  => $activated_plans,
                'failed_plans'     => $failed_plans,
                'cart_cleared'     => empty($failed_plans),
                'emails_sent'      => $email_result
            ]);

        } catch (Exception $e) {
            $this->_save_failed_event([
                'user_id'    => $user_id,
                'order_id'   => $input['razorpay_order_id'] ?? null,
                'payment_id' => $input['razorpay_payment_id'] ?? null,
                'error'      => $e->getMessage(),
                'data'       => json_encode($input)
            ]);
            $this->_send_failure_email($input, $e->getMessage());
            $this->_json_output(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * POST: /payment/failed
     * Logs failed payment attempts from Razorpay
     */
    public function failed() {
        $input = $this->input->post(NULL, TRUE);
        $user_id = $this->session->userdata('user_id') ?? $input['user_id'] ?? null;

        log_message('error', 'Razorpay Failed Payload: ' . json_encode($input));

        $this->_save_failed_event([
            'user_id'    => $user_id,
            'order_id'   => $input['order_id'] ?? null,
            'payment_id' => $input['payment_id'] ?? null,
            'amount'     => $input['amount'] ?? 0,
            'error'      => $input['error'] ?? 'Razorpay payment failed',
            'data'       => json_encode($input)
        ]);

        if ($user_id) {
            $this->_send_failure_email(array_merge($input, ['user_id' => $user_id]), $input['error'] ?? 'Payment failed');
        }

        $this->_json_output(['success' => true, 'message' => 'Failure logged successfully']);
    }

    /**
     * GET: /payment/history
     * Returns user's payment history
     */
    public function history() {
        $user_id = $this->session->userdata('user_id');
        if (!$user_id) {
            return $this->_json_output(['status' => false, 'message' => 'Login required'], 401);
        }
        $history = $this->Payment_model->getUserPaymentHistory($user_id);
        $this->_json_output(['status' => true, 'data' => $history, 'count' => count($history)]);
    }

    /**
     * GET: /payment/invoice/{invoice_no}
     * Returns invoice details for a specific invoice
     */
    public function invoice($invoice_no = null) {
        $user_id = $this->session->userdata('user_id');
        if (!$user_id) {
            return $this->_json_output(['status' => false, 'message' => 'Login required'], 401);
        }
        if (empty($invoice_no)) {
            return $this->_json_output(['status' => false, 'message' => 'Invoice number is required'], 400);
        }
        $invoice = $this->Payment_model->getInvoiceDetails($invoice_no, $user_id);
        if (!$invoice) {
            return $this->_json_output(['status' => false, 'message' => 'Invoice not found or access denied'], 404);
        }
        $this->_json_output(['status' => true, 'data' => $invoice]);
    }

    // ------------------------------------------------------------------------
    // PRIVATE HELPERS (unchanged logic, just adapted to use $this->output)
    // ------------------------------------------------------------------------

    private function _save_failed_event($data) {
        try {
            $masked_data = isset($data['data'])
                ? json_encode($this->_mask_sensitive_data(json_decode($data['data'], true) ?: []))
                : null;

            $event_data = [
                'user_id'       => $data['user_id'] ?? null,
                'order_id'      => $data['order_id'] ?? null,
                'payment_id'    => $data['payment_id'] ?? null,
                'amount'        => $data['amount'] ?? 0,
                'event'         => $data['event'] ?? 'payment_failure',
                'error_message' => $data['error'] ?? $data['error_message'] ?? 'Unknown error',
                'payment_data'  => $masked_data,
                'created_at'    => date('Y-m-d H:i:s')
            ];

            $this->db->insert('tb_ft_failed_payments', $event_data);

            if ($this->db->affected_rows() === 0) {
                log_message('error', 'FAILED PAYMENT INSERT ERROR: ' . json_encode($this->db->error()));
            }
        } catch (Exception $e) {
            log_message('error', 'FAILED PAYMENT EXCEPTION: ' . $e->getMessage());
        }
    }

    private function _mask_sensitive_data($input) {
        $masked = $input;
        if (isset($masked['razorpay_signature'])) {
            $masked['razorpay_signature'] = substr($masked['razorpay_signature'], 0, 10) . '***';
        }
        if (isset($masked['razorpay_payment_id'])) {
            $masked['razorpay_payment_id'] = substr($masked['razorpay_payment_id'], 0, 8) . '***';
        }
        return $masked;
    }

    private function _send_failure_email($input, $error_message) {
        try {
            $user_id = $input['user_id'] ?? null;
            if (empty($user_id)) return;
            $user = $this->Profile_mdl->get_candidate_details($user_id);
            if (!$user || empty($user['email'])) return;

            $subject = 'Payment Failed - Order #' . ($input['razorpay_order_id'] ?? 'N/A');
            $message = $this->_build_failure_email($user, $input, $error_message);
            $this->_send_email_helper($user['email'], $subject, $message);

            $admin_subject = 'Payment Failed Alert - ' . date('d M Y H:i:s');
            $admin_message = $this->_build_admin_failure_email($user, $input, $error_message);
            $this->_send_email_helper('skhan4377@gmail.com', $admin_subject, $admin_message);
        } catch (Exception $e) {
            $this->_save_failed_event([
                'user_id' => $input['user_id'] ?? null,
                'event'   => 'email_send_error',
                'error'   => 'Failed to send failure email: ' . $e->getMessage(),
                'data'    => json_encode($input)
            ]);
        }
    }

    private function _build_failure_email($user, $input, $error) {
        $error_display = (ENVIRONMENT === 'production') ? 'Payment verification failed. Please try again.' : $error;
        return '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Payment Failed</title>
            <style>
                body { margin:0; padding:0; font-family: Arial, Helvetica, sans-serif; background:#f5f5f5; }
                .container { width:100%; background:#fff; }
                .header { background:#d32f2f; color:#fff; padding:20px; text-align:center; }
                .content { padding:20px; }
                .error-box { background:#ffebee; padding:15px; border-radius:4px; margin:15px 0; }
                .footer { text-align:center; padding:15px; font-size:12px; color:#666; border-top:1px solid #eee; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1 style="margin:0;">Payment Failed</h1>
                </div>
                <div class="content">
                    <p>Dear ' . htmlspecialchars($user['name']) . ',</p>
                    <p>Your payment could not be processed. Here are the details:</p>
                    <div class="error-box">
                        <table width="100%">
                            <tr><td><strong>Order ID:</strong></td><td>' . htmlspecialchars($input['razorpay_order_id'] ?? 'N/A') . '</td></tr>
                            <tr><td><strong>Error:</strong></td><td>' . htmlspecialchars($error_display) . '</td></tr>
                            <tr><td><strong>Date:</strong></td><td>' . date('d M Y, h:i A') . '</td></tr>
                        </table>
                    </div>
                    <p><strong>What you can do:</strong></p>
                    <ol>
                        <li>Try the payment again</li>
                        <li>Check your payment method details</li>
                        <li>Contact your bank if needed</li>
                        <li>Contact our support team for assistance</li>
                    </ol>
                    <p><strong>Support:</strong> ' . (defined('CONTACT_EMAIL') ? CONTACT_EMAIL : (defined('SITE_EMAIL') ? SITE_EMAIL : '')) . '</p>
                    <p>Best regards,<br>' . (defined('SITE_NAME') ? SITE_NAME : 'Our Team') . ' Team</p>
                </div>
                <div class="footer">
                    © ' . date('Y') . ' ' . (defined('SITE_NAME') ? SITE_NAME : '') . '
                </div>
            </div>
        </body>
        </html>';
    }

    private function _build_admin_failure_email($user, $input, $error) {
        return '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Payment Failed Alert</title>
            <style>
                body { font-family: Arial, sans-serif; }
                table { border-collapse: collapse; width:100%; }
                td, th { border:1px solid #ddd; padding:8px; text-align:left; }
            </style>
        </head>
        <body>
            <h3>⚠️ Payment Failed Alert</h3>
            <p>A payment has failed on the platform:</p>
            <table>
                <tr><th>User</th><td>' . htmlspecialchars($user['name'] ?? 'N/A') . '</td></tr>
                <tr><th>Email</th><td>' . htmlspecialchars($user['email'] ?? 'N/A') . '</td></tr>
                <tr><th>User ID</th><td>' . htmlspecialchars($input['user_id'] ?? 'N/A') . '</td></tr>
                <tr><th>Order ID</th><td>' . htmlspecialchars($input['razorpay_order_id'] ?? 'N/A') . '</td></tr>
                <tr><th>Payment ID</th><td>' . htmlspecialchars($input['razorpay_payment_id'] ?? 'N/A') . '</td></tr>
                <tr><th>Error</th><td>' . htmlspecialchars($error) . '</td></tr>
                <tr><th>Time</th><td>' . date('d M Y, H:i:s') . '</td></tr>
            </table>
            <p style="color:#666; font-size:12px;">This is an automated alert.</p>
        </body>
        </html>';
    }

    private function _send_payment_confirmation_emails($payment_data) {
        $result = ['user_email' => false, 'admin_email' => false, 'messages' => []];
        try {
            $candidate = $this->Profile_mdl->get_candidate_details($payment_data['user_id']);
            if (!$candidate || empty($candidate['email'])) {
                $result['messages'][] = 'Candidate details not found for user_id: ' . $payment_data['user_id'];
                return $result;
            }

            $all_plan_details = [];
            foreach ($payment_data['cart_items'] as $item) {
                $plan_details = $this->Payment_model->getPlanDetails($item['feature_id'], $item['plan_id']);
                if ($plan_details) $all_plan_details[] = $plan_details;
            }

            $email_data = [
                'user_name'     => $candidate['name'] ?? 'User',
                'email'         => $candidate['email'],
                'contact'       => $candidate['mobile'] ?? '',
                'invoice_no'    => $payment_data['invoice_no'],
                'transaction_id'=> $payment_data['payment_id'],
                'order_id'      => $payment_data['order_id'],
                'amount'        => number_format($payment_data['amount'], 2),
                'currency'      => 'INR',
                'currency_symbol' => '₹',
                'payment_method'=> $payment_data['method'],
                'payment_date'  => date('d M Y, h:i A'),
                'company_name'  => defined('SITE_NAME') ? SITE_NAME : '',
                'support_email' => defined('CONTACT_EMAIL') ? CONTACT_EMAIL : (defined('SITE_EMAIL') ? SITE_EMAIL : ''),
                'website_url'   => base_url('candidate/dashboard'),
                'all_plans'     => $all_plan_details,
                'total_plans'   => count($all_plan_details),
                'cart_summary'  => $payment_data['cart_summary'],
                'activated_plans_count' => count($payment_data['activated_plans'] ?? []),
                'failed_plans_count' => count($payment_data['failed_plans'] ?? [])
            ];

            $user_email_result = $this->_send_payment_success_email($email_data);
            $result['user_email'] = $user_email_result['status'] === 'success';
            $result['messages'][] = $user_email_result['message'];

            $admin_email_result = $this->_send_admin_notification($payment_data, $candidate, $all_plan_details);
            $result['admin_email'] = $admin_email_result['status'] === 'success';
            $result['messages'][] = 'Admin notification: ' . $admin_email_result['message'];
        } catch (Exception $e) {
            $this->_save_failed_event([
                'user_id' => $payment_data['user_id'] ?? null,
                'event' => 'success_email_error',
                'error' => 'Failed to send success email: ' . $e->getMessage()
            ]);
            $result['messages'][] = 'Email sending error: ' . $e->getMessage();
        }
        return $result;
    }

    private function _send_payment_success_email($email_data) {
        $subject = 'Payment Confirmation - Invoice #' . $email_data['invoice_no'];
        $message = $this->_build_multi_plan_email_template($email_data);
        return $this->_send_email_helper($email_data['email'], $subject, $message);
    }

	private function _build_multi_plan_email_template($data) {
		// Build plan rows (three columns: Feature, Duration, Amount)
		$plan_rows = '';
		$total_amount = 0;
		foreach ($data['all_plans'] as $plan) {
			$plan_amount = $plan->plan_total ?? 0;
			$total_amount += $plan_amount;
			$plan_rows .= '<tr style="border-bottom:1px solid #e0e0e0;">
				<td style="padding:12px 8px;"><strong>' . htmlspecialchars($plan->feature_name ?? 'Feature') . '</strong><br><small>' . htmlspecialchars($plan->plan_level ?? 'Standard') . '</small></td>
				<td style="padding:12px 8px;">' . htmlspecialchars($plan->duration ?? '1 month') . '</td>
				<td style="padding:12px 8px;text-align:right;">₹' . number_format($plan_amount, 2) . '</td>
			</tr>';
		}

		// Feature descriptions
		$feature_descriptions = '<ul style="margin:0;padding-left:20px;">';
		foreach ($data['all_plans'] as $plan) {
			if (!empty($plan->feature_full_description)) {
				$clean_desc = htmlspecialchars(substr(strip_tags($plan->feature_full_description), 0, 150));
				$feature_descriptions .= '<li style="margin-bottom:6px;"><strong>' . htmlspecialchars($plan->feature_name ?? 'Feature') . ':</strong> ' . $clean_desc . '...</li>';
			}
		}
		$feature_descriptions .= '</ul>';

		// ----- Cart Summary (with % for discount and plan tax rate) -----
		$cart_summary = $data['cart_summary'] ?? [];
		$cart_html = '';
		if (!empty($cart_summary)) {
			$total_mrp      = $cart_summary['total_mrp'] ?? 0;
			$total_discount = $cart_summary['total_discount'] ?? 0;
			$total_taxes    = $cart_summary['total_taxes'] ?? 0;

			// Discount percent (saved vs MRP) – matches front-end
			$discountPercent = ($total_mrp > 0) ? round(($total_discount / $total_mrp) * 100, 1) : 0;

			// Tax rate directly from the plan (plan_taxes) – not effective rate
			$plan_tax_rate = 0;
			if (!empty($data['all_plans'])) {
				$first_plan = reset($data['all_plans']);
				$plan_tax_rate = $first_plan->plan_taxes ?? 0;       // e.g. 5.00
			}

			$cart_html = '
			<div style="background:#e8f5e9;padding:15px;margin:20px 0;border-radius:8px;">
				<h4 style="margin:0 0 10px 0;">Cart Summary</h4>
				<table width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;">
					<tr><td style="padding:4px 0;">Total MRP</td><td style="padding:4px 0;text-align:right;">₹' . number_format($total_mrp, 2) . '</td></tr>
					<tr><td style="padding:4px 0;">Discount <span style="font-size:12px;">(' . $discountPercent . '% Saved)</span></td><td style="padding:4px 0;text-align:right;color:#4CAF50;">-₹' . number_format($total_discount, 2) . '</td></tr>
					<tr><td style="padding:4px 0;">Taxes & Fees <span style="font-size:12px;">(' . $plan_tax_rate . '%)</span></td><td style="padding:4px 0;text-align:right;">+₹' . number_format($total_taxes, 2) . '</td></tr>
					<tr style="font-weight:bold;"><td style="padding:8px 0 0;">Grand Total</td><td style="padding:8px 0 0;text-align:right;">₹' . number_format($cart_summary['grand_total'] ?? 0, 2) . '</td></tr>
				</table>
			</div>';
		}

		// Payment summary
		$payment_summary = '
		<div class="summary-box" style="background:#f9f9f9; border-left:4px solid #4CAF50; padding:15px; margin-bottom:20px;">
			<h3 style="margin-top:0;">Payment Summary</h3>
			<table width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;">
				<tr><td style="padding:4px 0;"><strong>Invoice No:</strong></td><td style="padding:4px 0;text-align:right;">#' . htmlspecialchars($data['invoice_no']) . '</td></tr>
				<tr><td style="padding:4px 0;"><strong>Transaction ID:</strong></td><td style="padding:4px 0;text-align:right;">' . htmlspecialchars($data['transaction_id']) . '</td></tr>
				<tr><td style="padding:4px 0;"><strong>Total Amount:</strong></td><td style="padding:4px 0;text-align:right;">₹' . $data['amount'] . ' (INR)</td></tr>
				<tr><td style="padding:4px 0;"><strong>Payment Method:</strong></td><td style="padding:4px 0;text-align:right;">' . htmlspecialchars($data['payment_method']) . '</td></tr>
				<tr><td style="padding:4px 0;"><strong>Payment Date:</strong></td><td style="padding:4px 0;text-align:right;">' . $data['payment_date'] . '</td></tr>
			</table>
		</div>';

		// Full HTML template
		return '<!DOCTYPE html>
		<html>
		<head>
			<meta charset="utf-8">
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<title>Payment Confirmation</title>
			<style>
				body { margin:0; padding:0; font-family: Arial, Helvetica, sans-serif; background:#f5f5f5; }
				.container { width:100%; background:#fff; }
				.header { background:#4CAF50; color:#fff; padding:20px; text-align:center; }
				.content { padding:20px; }
				table { width:100%; border-collapse:collapse; }
				th, td { text-align:left; padding:8px; }
				th { background:#f5f5f5; }
				.footer { text-align:center; padding:15px; font-size:12px; color:#666; border-top:1px solid #eee; }
				@media only screen and (max-width: 600px) {
					.content { padding:15px; }
					.plan-table th, .plan-table td { display:block; width:100% !important; text-align:left; }
					.plan-table td[align="right"] { text-align:left !important; }
					.plan-table tr { border-bottom:1px solid #ddd; margin-bottom:10px; display:block; }
				}
			</style>
		</head>
		<body>
			<div class="container">
				<div class="header">
					<h1 style="margin:0;">Payment Successful!</h1>
					<p style="margin:5px 0 0;">' . $data['total_plans'] . ' Plan(s) Activated</p>
				</div>
				<div class="content">
					<p>Dear <strong>' . htmlspecialchars($data['user_name']) . '</strong>,</p>
					<p>Thank you for your payment. Your subscription(s) are now active.</p>
					
					' . $payment_summary . '

					<h3>Plan Details</h3>
					<table class="plan-table" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #ddd;">
						<thead>
							<tr style="background:#f5f5f5;">
								<th>Feature</th>
								<th>Duration</th>
								<th>Amount</th>
							</tr>
						</thead>
						<tbody>' . $plan_rows . '</tbody>
						<tfoot>
							<tr style="font-weight:bold; background:#f9f9f9;">
								<td colspan="2" style="text-align:right;">Total:</td>
								<td style="text-align:right;">₹' . number_format($total_amount, 2) . '</td>
							</tr>
						</tfoot>
					</table>

					' . $cart_html . '

					<div style="background:#f3e5f5;padding:15px;margin:20px 0;border-radius:8px;">
						<h4 style="margin:0 0 10px;">Features Included</h4>
						' . $feature_descriptions . '
					</div>

					<p style="text-align:center; margin-top:30px;">
						<a href="' . $data['website_url'] . '" style="display:inline-block;background:#4CAF50;color:#fff;padding:12px 25px;text-decoration:none;border-radius:4px;">Access Dashboard</a>
					</p>
					<p>Need help? Contact us at <a href="mailto:' . $data['support_email'] . '">' . $data['support_email'] . '</a></p>
				</div>
				<div class="footer">
					© ' . date('Y') . ' ' . $data['company_name'] . '. All rights reserved.
				</div>
			</div>
		</body>
		</html>';
	}
	private function _send_admin_notification($payment_data, $candidate, $all_plan_details) {
        $admin_emails = ['skhan4377@gmail.com'];
        $subject = 'New Payment Received - ' . date('d M Y, h:i A');
        $plan_details_html = '';
        foreach ($all_plan_details as $plan) {
            $plan_details_html .= "<tr><td>" . htmlspecialchars($plan->feature_name ?? 'N/A') . "</td><td>" . htmlspecialchars($plan->plan_level ?? 'Standard') . "</td><td>" . htmlspecialchars($plan->duration ?? '1 month') . "</td><td>₹" . number_format($plan->plan_total ?? 0, 2) . "</td></tr>";
        }
        $message = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Payment Received</title>
    <style>
        body { font-family: Arial, sans-serif; margin:0; padding:20px; background:#f5f5f5; }
        .container { max-width:800px; margin:0 auto; background:#fff; border-radius:8px; overflow:hidden; }
        .header { background:#4CAF50; color:#fff; padding:15px; text-align:center; }
        .content { padding:20px; }
        table { width:100%; border-collapse:collapse; margin-bottom:20px; }
        th, td { border:1px solid #ddd; padding:8px; text-align:left; }
        th { background:#f5f5f5; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 style="margin:0;">🤑 New Payment Received</h2>
        </div>
        <div class="content">
            <p><strong>' . count($all_plan_details) . ' Plan(s)</strong> | <strong>₹' . number_format($payment_data['amount'], 2) . '</strong></p>
            <h3>Customer Details</h3>
            <table>
                <tr><th>Name</th><td>' . htmlspecialchars($candidate['name'] ?? 'N/A') . '</td></tr>
                <tr><th>Email</th><td>' . htmlspecialchars($candidate['email'] ?? 'N/A') . '</td></tr>
                <tr><th>Phone</th><td>' . htmlspecialchars($candidate['mobile'] ?? 'N/A') . '</td></tr>
                <tr><th>User ID</th><td>' . $payment_data['user_id'] . '</td></tr>
            </table>
            <h3>Transaction Details</h3>
            <table>
                <tr><th>Invoice No</th><td>#' . htmlspecialchars($payment_data['invoice_no']) . '</td></tr>
                <tr><th>Transaction ID</th><td>' . htmlspecialchars($payment_data['payment_id']) . '</td></tr>
                <tr><th>Order ID</th><td>' . htmlspecialchars($payment_data['order_id']) . '</td></tr>
                <tr><th>Amount</th><td>₹' . number_format($payment_data['amount'], 2) . '</td></tr>
                <tr><th>Method</th><td>' . htmlspecialchars($payment_data['method']) . '</td></tr>
                <tr><th>Time</th><td>' . date('d M Y, h:i A') . '</td></tr>
            </table>
            <h3>Plan Details</h3>
            <table>
                <thead><tr><th>Feature</th><th>Plan Level</th><th>Duration</th><th>Amount</th></tr></thead>
                <tbody>' . $plan_details_html . '</tbody>
            </table>
        </div>
    </div>
</body>
</html>';
        return $this->_send_email_helper($admin_emails, $subject, $message);
    }

    private function _send_email_helper($to, $subject, $message, $bcc = "") {
        try {
            $result = SendEmailTo($to, $subject, $message, $bcc);
            return $result ? ['status' => 'success', 'message' => 'Email sent successfully'] : ['status' => 'error', 'message' => 'Failed to send email'];
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}