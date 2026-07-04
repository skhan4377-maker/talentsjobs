<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
require_once FCPATH . 'vendor/autoload.php';

class PaymentController extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
       
        $this->load->model('admin/services/CareerService_model');
        $this->config->load('razorpay', TRUE); // Load the custom configuration file
    }

    public function checkout() {
        // Ensure the request is coming from a valid source
        if ($this->input->is_ajax_request()) {
            // Set your Razorpay API keys
            $razorpay_key_id = $this->config->item('razorpay_key_id', 'razorpay');
            $razorpay_key_secret = $this->config->item('razorpay_key_secret', 'razorpay');
        
            // Initialize the Razorpay API
            $api = new Razorpay\Api\Api($razorpay_key_id, $razorpay_key_secret);
        
            $user_id = $this->session->userdata('user_id');
            $cart_items = $this->CareerService_model->get_cart_items($user_id);
        
            // Calculate the total amount from cart items
            $total_amount = 0;
            foreach ($cart_items as $item) {
                // Validate plan_total before adding
                $plan_total = (float) $item['plan_total'];
                if ($plan_total < 0) {
                    // Handle invalid amount case
                    continue;
                }
                $total_amount += $plan_total;
            }
        
            // Convert to paise (multiply by 100)
            $total_amount_in_paise = (int) ($total_amount * 100);
        
            // Razorpay order data
            $orderData = [
                'receipt' => (string) rand(),
                'amount' => $total_amount_in_paise,
                'currency' => 'INR',
                'payment_capture' => 1
            ];
        
            try {
                $razorpayOrder = $api->order->create($orderData);
                $orderId = $razorpayOrder['id'];
        
                // Return order details, including amount and cart items' feature and duration IDs
                $featureIds = array_column($cart_items, 'feature_id');
                $durationIds = array_column($cart_items, 'duration_id');
            
                // Return order details
                echo json_encode([
                    'order_id' => $orderId,
                    'razorpay_key' => $razorpay_key_id,
                    'amount' => $total_amount_in_paise, // Total amount in paise
                    'feature_ids' => $featureIds, // Send feature IDs
                    'duration_ids' => $durationIds // Send duration IDs
                ]);
            } catch (\Razorpay\Api\Errors\BadRequestError $e) {
                // Log error instead of echoing to user
                log_message('error', 'Razorpay Order Error: ' . $e->getMessage());
                echo json_encode(['error' => 'Failed to create order.']);
            }
        } else {
            show_404(); // Not an AJAX request
        }
    }

    public function handlePaymentResponse() {
		if ($this->input->is_ajax_request()) {
			$razorpayPaymentId = $this->input->post('razorpay_payment_id', true);
			$razorpayOrderId = $this->input->post('razorpay_order_id', true);
			$signature = $this->input->post('razorpay_signature', true);
			$amount = $this->input->post('amount', true);
			$featureIds = $this->input->post('feature_ids', true);
			$durationIds = $this->input->post('duration_ids', true);
			$user_id = $this->session->userdata('user_id');

			$verified = $this->verifySignature([
				'razorpay_payment_id' => $razorpayPaymentId,
				'razorpay_order_id' => $razorpayOrderId,
				'razorpay_signature' => $signature
			]);

			$invoiceNumber = strtoupper('INV-' . uniqid());

			$data = [
				'user_id' => $user_id,
				'upi_id' => $razorpayPaymentId,
				'order_id' => $razorpayOrderId,
				'amount' => (int)$amount / 100,
				'currency' => 'INR',
				'status' => $verified ? 'success' : 'failed',
				'invoice_number' => $invoiceNumber,
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			];

			$this->db->insert('career_service_payments', $data);
			$paymentId = $this->db->insert_id();

			if ($verified) {				
				$this->insertPurchasedPlanDetails($paymentId, $featureIds, $durationIds, $invoiceNumber);
				$this->sendPaymentEmailAsync($user_id, $invoiceNumber);

				echo json_encode([
					'status' => 'success',
					'redirect_url' => site_url('my-purchases'),
					'message' => 'Payment successfully.'
				]);
			} else {
				echo json_encode([
					'status' => 'error',
					'message' => 'Payment verification failed.'
				]);
			}
		} else {
			show_404();
		}
	}

    
    private function verifySignature($postData) {
        // Your Razorpay Secret Key
        $secretKey = $this->config->item('razorpay_key_secret', 'razorpay');
    
        // Extract the received signature from the post data
        $receivedSignature = $postData['razorpay_signature'];
    
        // Remove the received signature from the post data
        unset($postData['razorpay_signature']);
    
        // Sort the post data by key
        ksort($postData);
    
        // Create a string by concatenating all the post data values
        $data = implode("|", $postData);
    
        // Generate the HMAC-SHA256 hash using your secret key
        $generatedSignature = hash_hmac('sha256', $data, $secretKey);
    
        // Verify if the received signature matches the generated signature
        return $receivedSignature === $generatedSignature;
    }
    
    public function insertPurchasedPlanDetails($paymentId, $featureIds, $durationIds, $invoiceNumber) {
        $userId = $this->session->userdata('user_id');
        $cartItems = $this->CareerService_model->get_cart_items($userId);
    
        $insertData = [];
        $validCartItems = [];
        foreach ($cartItems as $item) {
            $validCartItems[$item['feature_id'] . '_' . $item['duration_id']] = $item;
        }
    
        $purchaseDate = date('Y-m-d H:i:s');
        for ($i = 0; $i < count($featureIds); $i++) {
            $planKey = $featureIds[$i] . '_' . $durationIds[$i];
            if (isset($validCartItems[$planKey])) {
                $duration = $validCartItems[$planKey]['plan_duration'];
                $endDate = date('Y-m-d H:i:s', strtotime("+$duration", strtotime($purchaseDate)));
                
                // Verify plan_taxes is included correctly
                $insertData[] = [
                    //'user_id' => $userId,
                    //'feature_id' => $featureIds[$i],
                    'duration_id' => $durationIds[$i],
                    'payment_id' => $paymentId,
                    'invoice_number' => $invoiceNumber, // Store invoice number here
                    'plan_mrp' => $validCartItems[$planKey]['plan_mrp'],
                    'plan_taxes' => $validCartItems[$planKey]['plan_taxes'],
                    'plan_discount' => $validCartItems[$planKey]['plan_discount'],
                    'plan_total' => $validCartItems[$planKey]['plan_total'],
                    'purchase_date' => $purchaseDate,
                    'end_date' => $endDate,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
            }
        }
    
        // Batch insert and clear cart items
        if (!empty($insertData)) {
			$this->db->insert_batch('tb_career_service_purchased_plans', $insertData);

			// Remove items from cart
			$this->db->where('user_id', $userId)
					 ->where_in('feature_id', $featureIds)
					 ->where_in('duration_id', $durationIds)
					 ->delete('tb_career_service_cart');

			// Update draft resume templates as purchased
			$this->db->where('user_id', $userId)
					 ->where('is_purchased', 0)
					 ->update('tb_user_resume_templates', ['is_purchased' => 1]);
			}
    }
      

    // Function to calculate totals
    private function calculateTotals($purchasedPlans) {
        $totalMrp = $totalDiscount = $totalTax = $grandTotal = 0;
        $taxPercentage = 0;
    
        foreach ($purchasedPlans as $plan) {
            $mrp = $plan['plan_mrp'];
            $discountAmount = $this->calculateDiscount($mrp, $plan['plan_discount']);
            $taxableAmount = $mrp - $discountAmount;
            $gstAmount = $this->calculateGST($taxableAmount, $plan['plan_taxes'] * 100);
    
            $totalMrp += $mrp;
            $totalDiscount += $discountAmount;
            $totalTax += $gstAmount;
            $grandTotal += $plan['plan_total'];
            $taxPercentage = $plan['plan_taxes']; // Assume single tax rate
        }
    
        return [
            'totalMrp' => round($totalMrp, 2),
            'totalDiscount' => round($totalDiscount, 2),
            'totalTax' => round($totalTax, 2),
            'grandTotal' => round($grandTotal, 2),
            'taxPercentage' => $taxPercentage
        ];
    }
    
    /**
     * Invoice के लिए PDF generate करके email भेजने का main function
     *
     * @param int $userId
     * @param string $invoiceNumber
     */
	 
	 public function sendPaymentEmailAsync($userId, $invoiceNumber) {		
		// 1. Fetch invoice related data
		$purchasedPlans = $this->CareerService_model->getPurchasedPlans($userId, $invoiceNumber);
		$totals = $this->calculateTotals($purchasedPlans);
		
		$this->load->model('candidate/Profile_mdl');
		$candidateDetails = $this->Profile_mdl->get_candidate_details($userId);
		
		// Prepare data for the invoice email
		$data = array_merge([
			'purchasedPlans'  => $purchasedPlans,
			'invoiceNumber'   => $invoiceNumber,
			'invoiceDate'     => date('d-m-Y'),
			'payment_success' => true
		], $totals);

		// Generate email HTML from the view
		$html = $this->load->view('website/services/email/invoice_email', ['data' => $data], true);

		// 2. Send the email directly with the HTML as message
		$toMail = $candidateDetails['email'];
		$emailStatus = SendEmailTo($toMail, $invoiceNumber, $html);

		if (!$emailStatus) {
			//log_message('error', 'Email sending failed for invoice: ' . $invoiceNumber);
		}
	}
	
	 /*public function payment_status() {
		// Check if payment is successful from session data
		if ($this->session->userdata('payment_success')) {
			$userId = $this->session->userdata('user_id');
			$invoiceNumber = $this->session->userdata('invoice_number');

			// Fetch purchased plans and initialize totals
			$purchasedPlans = $this->CareerService_model->getPurchasedPlans($userId, $invoiceNumber);
			$totals = $this->calculateTotals($purchasedPlans);

			// Prepare data for the view
			$data = array_merge([
				'purchasedPlans' => $purchasedPlans,
				'invoiceNumber'  => $invoiceNumber,
				'invoiceDate'    => date('d-m-Y')
			], $totals);				
			
			// Trigger email and PDF generation asynchronously
			$this->sendPaymentEmailAsync($userId, $invoiceNumber);

			// Load the view
			//$this->load->view('website/services/payment_status', $data);
		} else {
			// Optionally redirect or load an error view if no payment success session is set
			//redirect(base_url());
		}
	}*/
	
	/**
	 * Sends the email using CodeIgniter's email library.
	 *
	 * @param string $toMail Recipient email address.
	 * @param string $invoiceNumber Invoice number for subject line.
	 * @param string $html HTML content for the email body.
	 * @return bool True if email was sent successfully, false otherwise.
	 */
	/*private function sendEmail($toMail, $invoiceNumber, $html) {
		$this->load->library('email');
		
		// SMTP configuration
		$config = array(
			'protocol'    => 'smtp',
			'smtp_host'   => 'smtp.hostinger.com',
			'smtp_port'   => 587,
			'smtp_user'   => SITE_EMAIL,
			'smtp_pass'   => SECRET_SITE_PASSWORD,
			'mailtype'    => 'html',
			'charset'     => 'utf-8',
			'wordwrap'    => TRUE,
			'smtp_crypto' => 'tls'
		);
		$this->email->initialize($config);
		$this->email->set_newline("\r\n");

		// Set email details
		$this->email->from(SITE_EMAIL, SITE_NAME);
		$this->email->to($toMail);
		$this->email->subject("Payment Successful - Invoice #$invoiceNumber");
		$this->email->message($html);

		// Send email and check for success
		if (!$this->email->send()) {
			log_message('error', 'Email sending failed: ' . $this->email->print_debugger());
			return false;
		}
		return true;
	}*/


    // Calculate discount based on MRP and discount percentage
    private function calculateDiscount($mrp, $discountPercentage) {
        return round($mrp * ($discountPercentage / 100), 2); // Round discount amount
    }
    
    // Calculate GST based on taxable amount and tax percentage
    private function calculateGST($taxableAmount, $gstPercentage) {
        return round($taxableAmount * ($gstPercentage / 100), 2); // Round GST amount
    }
 
    

}
?>
