<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CartController extends CI_Controller {

    public function __construct() { 
        parent::__construct();
		// Load necessary libraries, models
        $this->load->model('admin/services/CareerService_model');
        $this->load->helper('cookie'); // Load the cookie helper 
    }
    
	public function add_to_cart() {
		// Sanitize user inputs to prevent XSS
		$feature_id = $this->security->xss_clean($this->input->post('feature_id', true));
		$durationId = $this->security->xss_clean($this->input->post('duration_id', true));

		// Validate that the inputs are numeric to prevent injection attacks
		if (!is_numeric($feature_id) || !is_numeric($durationId)) {
			echo json_encode(['status' => 'error', 'message' => 'Invalid input data.']);
			return;
		}   
		
		// -------------------------------------------------------------------------
		// Logged-in Candidate Branch: Use Database for Cart Operations
		// -------------------------------------------------------------------------
		if ($this->session->userdata('logged_in') === TRUE && $this->session->userdata('role') === 'candidate') {
			$user_id = $this->session->userdata('user_id');
			
			// Fetch plan details from the database based on the provided IDs
			$plan = $this->CareerService_model->get_plan_details_by_duration_id($durationId, $feature_id);
			if ($plan) {
				// Check if the item already exists in the cart for this user
				$this->db->where('user_id', $user_id);
				$this->db->where('feature_id', $feature_id);
				$existing_cart_item = $this->db->get('career_service_cart')->row();

				if ($existing_cart_item) {
					// Check if the duration_id is different
					if ($existing_cart_item->duration_id !== $durationId) {
						// Update the duration_id for the existing cart item
						$this->db->where('id', $existing_cart_item->id);
						$this->db->update('career_service_cart', ['duration_id' => $durationId]);

						echo json_encode([
							'status' => 'success', 
							'message' => 'Plan updated in cart successfully!'
						], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
					} else {
						// If the item already exists with the same duration_id
						echo json_encode([
							'status' => 'error', 
							'message' => 'This plan is already in your cart!'
						], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
					}
				} else {
					// If it doesn't exist, insert the new cart item
					$cart_data = [
						'user_id'    => $user_id,
						'feature_id' => $feature_id,
						'duration_id'=> $durationId,
					];

					$this->db->insert('career_service_cart', $cart_data);
					
					// Remove any candidate cart items from the session cart
					if (isset($this->cart)) {
						$cartItems = $this->cart->contents();
						foreach ($cartItems as $item) {
							if (isset($item['options']['user_type']) && $item['options']['user_type'] === 'candidate') {
								$this->cart->remove($item['rowid']);
							}
						}
					}
				
					echo json_encode([
						'status' => 'success', 
						'message' => 'Plan added to cart successfully!'
					], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
				}
			} else {
				// Invalid plan selected
				echo json_encode([
					'status' => 'error', 
					'message' => 'Invalid plan selected.'
				], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
			}
		} 
		// -------------------------------------------------------------------------
		// Guest Branch: Use CodeIgniter's Cart Library for Session-Based Cart
		// -------------------------------------------------------------------------
		else {
			if (!isset($this->cart)) {
				$this->load->library('cart');
			}

			// Retrieve current cart contents
			$cart_items = $this->cart->contents();

			$existing_cart_item = null;

			// Loop through the cart items to find a candidate item with the same feature_id
			foreach ($cart_items as $item) {
				if (isset($item['options']['user_type']) && $item['options']['user_type'] === 'candidate' && $item['id'] == $feature_id) {
					$existing_cart_item = $item;
					break;
				}
			}

			if ($existing_cart_item) {
				// Check if the duration_id is different
				if ($existing_cart_item['options']['duration_id'] !== $durationId) {
					// Prepare the update data with the new duration_id
					$update_data = [
						'rowid'   => $existing_cart_item['rowid'],
						'qty'     => $existing_cart_item['qty'],    // Keep the same quantity
						'price'   => $existing_cart_item['price'],  // Keep the same price
						'name'    => $existing_cart_item['name'],   // Keep the same name
						'options' => array_merge($existing_cart_item['options'], ['duration_id' => $durationId])
					];
					// Update the existing cart item with new duration_id
					$this->cart->update($update_data);
					echo json_encode([
						'status' => 'success', 
						'message' => 'Duration updated in cart successfully!'
					], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
					return;
				} else {
					// If duration is the same, return a message
					echo json_encode([
						'status' => 'error', 
						'message' => 'This plan is already in your cart with the selected duration.'
					], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
					return;
				}
			} 

			// If no existing item was found, prepare cart data for candidate and insert a new item
			$data = [
				'id'      => $feature_id,
				'qty'     => 1,
				'price'   => 0, // Set price if applicable; assumed as 0 here.
				'name'    => 'Plan', // Set plan name if available.
				'options' => [
					'duration_id' => $durationId,
					'user_type'   => 'candidate' // Candidate cart item
				]
			];

			// Insert the new item into the session cart
			$this->cart->insert($data);
			echo json_encode([
				'status' => 'success', 
				'message' => 'Plan added to cart successfully!'
			], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		}
	}
    
	public function get_cart_count() {
		$cart_count = 0;
		
		// Use the database cart if the user is logged in as a candidate.
		if ($this->session->userdata('logged_in') === TRUE && $this->session->userdata('role') === 'candidate') {
			$user_id = $this->session->userdata('user_id');
			
			// Count cart items from the database for the logged-in candidate
			$this->db->where('user_id', $user_id);
			$cart_count = $this->db->count_all_results('career_service_cart');
		} else {
			// For guest users or users not logged in as candidate, load the Cart library if not already loaded.
			if (!isset($this->cart)) {
				$this->load->library('cart');
			}
			
			// Count only candidate items from the session cart.
			$cart_items = $this->cart->contents();
			$candidate_cart_count = 0;

			foreach ($cart_items as $item) {
				if (isset($item['options']['user_type']) && $item['options']['user_type'] === 'candidate') {
					$candidate_cart_count++;
				}
			}
			$cart_count = $candidate_cart_count;
		}
		
		echo json_encode(
			['count' => $cart_count, 'status' => 'success'], 
			JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
		);
	}
    
	public function view_cart() {	
	   $data['title']= 'Talents Jobs';
		$data['description']='Talents Jobs';
		// Load header and navigation views
		$this->load->view('particles/header',$data);
		$this->load->view('particles/nav');
		
		$this->load->view('website/services/cart_view');
		$this->load->view('particles/footer');		
	}
    
	public function load_cart_items() {
		// Check if the user is logged in as a candidate.
		if ($this->session->userdata('logged_in') === TRUE && $this->session->userdata('role') === 'candidate') {
			$user_id = $this->session->userdata('user_id');
			// Retrieve cart items for the logged-in candidate
			$cart_items = $this->CareerService_model->get_cart_items($user_id);
			$has_paid_template = $this->CareerService_model->has_paid_template($cart_items); // ✅ Also check here
		} else {
			// For guest users (or non-candidate logged-in users), ensure the Cart library is loaded.
			if (!isset($this->cart)) {
				$this->load->library('cart');
			}

			// Get guest cart items from the session cart.
			$session_cart = $this->cart->contents();
			$guest_items = [];

			if (!empty($session_cart)) {
				foreach ($session_cart as $item) {
					// Check if the item belongs to a candidate.
					if (isset($item['id']) && isset($item['options']['duration_id']) 
						&& isset($item['options']['user_type']) 
						&& $item['options']['user_type'] == 'candidate') {
						$guest_items[] = [
							'feature_id'  => $item['id'],
							'duration_id' => $item['options']['duration_id']
						];
					}
				}
			}
			// Retrieve cart item details for candidate guest items.
			$cart_items = $this->CareerService_model->get_cart_items(null, $guest_items);
			$has_paid_template = $this->CareerService_model->has_paid_template($cart_items); // ✅ Already present
		}
		
		// Validation: if the cart is empty, return a redirect URL in the JSON response.
		if (empty($cart_items)) {
			echo json_encode(['redirect_url' => site_url('career-services')]);
			return;
		}
		
		$html = $this->get_cart_items_html($cart_items, $has_paid_template);
		echo json_encode([
			'status' => 'success',
			'html'   => $html
		]);
	}

   
    public function get_cart_items_html($cart_items, $has_paid_template = false) {
        // Start with a responsive container design
        $html = '<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Mobile Header -->
            <div class="lg:hidden py-4 flex items-center justify-between">
                <h1 class="text-xl font-bold text-gray-900">Your Cart</h1>
                <span class="text-sm text-gray-500">(' . count($cart_items) . ' item' . (count($cart_items) > 1 ? 's' : '') . ')</span>
            </div>
            <!-- Desktop Header -->
            <div class="hidden lg:block py-6 border-b border-gray-200">
                <h1 class="text-3xl font-bold text-gray-900">Shopping Cart</h1>
                <p class="mt-1 text-gray-500 text-sm">(' . count($cart_items) . ' item' . (count($cart_items) > 1 ? 's' : '') . ' in your cart)</p>
            </div>
            <!-- Cart Layout -->
            <div class="mt-4 lg:grid lg:grid-cols-12 lg:gap-8">
                <!-- Cart Items Section -->
                <div class="lg:col-span-8">';

        // Loop through each cart item
        foreach ($cart_items as $item) {
            // Calculate discount if available
            $discount_amount = 0;
            if ($item['plan_discount'] > 0) {
                $discount_amount = ($item['plan_mrp'] * $item['plan_discount'] / 100);
                $price_after_discount = $item['plan_mrp'] - $discount_amount;
            } else {
                $price_after_discount = $item['plan_mrp'];
            }

            $html .= '<div class="bg-white rounded-xl shadow-sm p-4 mb-4">
                        <div class="flex flex-col sm:flex-row gap-4">
                            <!-- Product Image -->
                            <div class="w-full sm:w-32 flex-shrink-0">
                                <img src="' . $item['feature_logo'] . '" 
                                     class="w-full h-32 object-contain bg-gray-100 rounded-lg p-2"
                                     alt="' . $item['feature_name'] . '">
                            </div>
                            <!-- Product Details -->
                            <div class="flex-1">
                                <!-- Title & Price -->
                                <div class="flex justify-between items-start">
                                    <h3 class="text-lg font-semibold text-gray-900">' . $item['feature_name'] . '</h3>
                                    <span class="text-lg font-bold text-gray-900">₹' . round($price_after_discount, 0) . '</span>
                                </div>
                                <!-- Badges & Additional Info -->
                                <div class="mt-2 flex flex-wrap gap-2">
                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">
                                        2254 bought
                                    </span>';
            // If level info is available
            if (!empty($item['experience_level'])) {
                $html .= '<span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                            Level: ' . $item['plan_level'] . ' (' . $item['experience_level'] . ' yrs exp)
                          </span>';
            } else {
                $html .= '<span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                            Level: ' . $item['plan_level'] . '
                          </span>';
            }
            $html .= '  </div>
                            <!-- Controls -->
                        <div class="mt-4 flex flex-col sm:flex-row sm:items-center gap-3">
                            <!-- Duration Selector -->
                            <select class="w-full sm:w-48 text-sm px-3 py-2 border rounded-lg 
                                            focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            data-feature-id="' . $item['feature_id'] . '" id="cart-plan-duration">';
						// Fetch available durations (assume this function returns an array)
						$durations = $this->CareerService_model->get_plan_durations($item['feature_id'], $item['plan_level']);
						foreach ($durations as $duration) {
							$selected = ($duration['plan_duration'] == $item['plan_duration']) ? ' selected' : '';
							$html .= '<option value="' . $duration['duration_id'] . '"' . $selected . '>' . $duration['plan_duration'] . '</option>';
						}
						$html .= '   </select>
                                    <!-- Remove Button -->
                                    <button class="text-red-600 hover:text-red-800 text-sm font-medium 
                                            flex items-center gap-1 cart-page-remove-item" 
                                            data-id="' . @$item['cart_id'] . '" 
											data-feature-id="' . $item['feature_id'] . '" 
											data-duration-id="' . $item['duration_id'] . '">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Remove
                                    </button>
                                </div>
                            </div>
                        </div>';

            // Price details with discount if applicable
            if ($item['plan_discount'] > 0) {
                $html .= '<div class="mt-3 flex items-center gap-2">
                            <span class="text-lg font-bold text-gray-900">₹' . round($price_after_discount, 0) . '</span>
                            <span class="text-sm text-gray-500 line-through">₹' . round($item['plan_mrp'], 0) . '</span>
                            <span class="text-xs text-green-600 font-medium">' . round($item['plan_discount'], 0) . '% OFF</span>
                          </div>';
            }
            $html .= '</div>';
        }
        // Disclaimer section
        $html .= '<div class="text-center mt-4 text-sm text-gray-500">
                    By placing your order, you agree to our 
                    <a href="#" class="text-blue-600 hover:underline">terms of service</a>
                  </div>
                </div>';

        // Order Summary Section (Sidebar)
        $html .= '<div class="lg:col-span-4 mt-6 lg:mt-0">
                    ' . $this->render_cart_summary($cart_items, $has_paid_template) . '
                  </div>
            </div>
        </div>';

        // Mobile Bottom Checkout Bar
        $total_amount = $this->calculate_total_amount($cart_items);
		if ($this->session->userdata('logged_in') === TRUE) {
			$html .= '<div class="lg:hidden fixed bottom-0 left-0 right-0 bg-white shadow-lg border-t border-gray-100">
						<div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
							<div class="text-right">
								<div class="text-sm font-semibold text-gray-900">₹' . number_format($total_amount, 0) . '</div>
								<div class="text-xs text-gray-500">Incl. taxes</div>
							</div>';

			if ($has_paid_template) {
				$html .= '<button class="bg-blue-600 text-white px-6 py-2 rounded-lg font-medium 
									hover:bg-blue-700 transition-colors" id="place-order-btn">
								Checkout
							</button>';
			} else {
				$html .= '<button class="bg-gray-400 text-white px-6 py-2 rounded-lg font-medium cursor-not-allowed" disabled>
							Checkout disabled
						  </button>';
			}

			$html .= '</div></div>';
		}


        return $html;
    }

    private function render_cart_summary($cart_items, $has_paid_template = false) {
		// Check if the user is logged in and retrieve the role from session data.
		$is_logged_in = $this->session->userdata('logged_in') === TRUE;
		$login_role   = $is_logged_in ? $this->session->userdata('role') : 'guest';
		
		// Determine if the logged in user is a candidate.
		$is_candidate = ($is_logged_in && $login_role === 'candidate');

		// Initialize calculations
		$total_mrp      = 0;
		$total_discount = 0; // Sum of individual item discounts
		$coupon_discount= 0; // Apply coupon logic here if needed
		$total_taxes    = 0;
		$item_count     = count($cart_items);

		foreach ($cart_items as $item) {
			$total_mrp += $item['plan_mrp'];

			if ($item['plan_discount'] > 0) {
				$discount_amount = ($item['plan_mrp'] * $item['plan_discount'] / 100);
				$total_discount += $discount_amount;
			} else {
				$discount_amount = 0;
			}

			// Calculate taxes on the discounted amount
			$taxable_amount = $item['plan_mrp'] - $discount_amount;
			$item_taxes     = ($taxable_amount * $item['plan_taxes'] / 100);
			// Assuming tax needs to be scaled (example: multiplied by 100)
			$total_taxes += $item_taxes * 100;
		}

		// Calculate final total amount
		$total_amount = ($total_mrp - $total_discount - $coupon_discount) + $total_taxes;

		// Build the Order Summary HTML
		$html = '<div class="bg-white rounded-xl shadow-lg lg:shadow-sm p-4 lg:sticky lg:top-8">
					<!-- Coupon Section -->
					<div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg mb-6 
							  cursor-pointer hover:bg-gray-100 transition-colors">
						<span class="text-sm font-medium text-gray-700">Apply Coupon Code</span>
						<svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
						</svg>
					</div>
					<!-- Price Breakdown -->
					<div class="space-y-3 text-sm text-gray-700">
						<div class="flex justify-between">
							<span>Total MRP</span>
							<span>₹' . number_format($total_mrp, 0) . '</span>
						</div>
						<div class="flex justify-between">
							<span>Discount</span>
							<span class="text-green-600">-₹' . number_format($total_discount, 0) . '</span>
						</div>
						<div class="flex justify-between">
							<span>Coupon Discount</span>
							<span class="text-green-600">-₹' . number_format($coupon_discount, 0) . '</span>
						</div>
						<div class="flex justify-between">
							<span>Taxes</span>
							<span>₹' . number_format($total_taxes, 0) . '</span>
						</div>
						<!-- Total Amount -->
						<div class="pt-4 border-t border-gray-200">
							<div class="flex justify-between items-center mt-3">
								<span class="font-semibold text-gray-900">Total Amount</span>
								<div class="text-right">
									<div class="text-lg font-bold text-gray-900">₹' . number_format($total_amount, 0) . '</div>
									<div class="text-xs text-gray-500">Inclusive of all taxes</div>
								</div>
							</div>
						</div>
					</div>';

		// Add action button based on login status
		if ($is_candidate) {
			if ($has_paid_template) {
				$html .= '<div class="hidden lg:block">
							<button class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 
								rounded-lg font-medium mt-6 transition-colors shadow-md 
								hover:shadow-lg active:scale-95" id="place-order-btn">
								Checkout
							</button>
						  </div>';
			} else {
				$html .= '<div class="hidden lg:block">
							<button class="w-full bg-gray-400 text-white py-3 px-4 rounded-lg font-medium mt-6 
								cursor-not-allowed" disabled>
								Checkout disabled
							</button>
						  </div>';
			}

		} else {
			// Not logged in or not a candidate: show login/sign up prompt.
			// Using the first cart item to retrieve feature_id and duration_id if available.
			$first_item = !empty($cart_items) ? $cart_items[0] : [];
			$feature_id = isset($first_item['feature_id']) ? $first_item['feature_id'] : '';
			$duration_id= isset($first_item['duration_id']) ? $first_item['duration_id'] : '';

			$html .= '<div class="mt-6">
					  <div class="flex items-center mb-3">
						  <input type="checkbox" id="terms-checkbox" class="mr-2">
						  <label for="terms-checkbox" class="text-sm text-gray-700">
							  I\'ve read and accept the <a href="#" class="text-blue-600 hover:underline">terms & conditions</a>
						  </label>
					  </div>
					  <button class="w-full bg-gray-400 text-white py-3 px-4 rounded-lg font-medium mt-2 cursor-not-allowed" 
							  id="openLoginModal" disabled
							  data-feature-id="' . htmlspecialchars($feature_id) . '" 
							  data-duration-id="' . htmlspecialchars($duration_id) . '">
						  Login/Sign Up to Place Order
					  </button>
				  </div>';
		}
		
		// Script to toggle button enable/disable based on terms checkbox
		$html .= '<script>
					"use strict";
					var termsCheckbox = document.getElementById("terms-checkbox");
					if(termsCheckbox) {
						termsCheckbox.addEventListener("change", function() {
							var placeOrderBtn = document.getElementById("' . ($is_candidate ? 'place-order-btn' : 'openLoginModal') . '");
							if (this.checked) {
								placeOrderBtn.classList.remove("bg-gray-400", "cursor-not-allowed");
								placeOrderBtn.classList.add("bg-blue-600");
								placeOrderBtn.disabled = false;
							} else {
								placeOrderBtn.classList.add("bg-gray-400", "cursor-not-allowed");
								placeOrderBtn.classList.remove("bg-blue-600");
								placeOrderBtn.disabled = true;
							}
						});
					}
				  </script>
				  </div>';
		
		return $html;
	}
    
    private function calculate_total_amount($cart_items) {
        $total_mrp = 0;
        $total_discount = 0;
        $coupon_discount = 0;
        $total_taxes = 0;

        foreach ($cart_items as $item) {
            $total_mrp += $item['plan_mrp'];

            if ($item['plan_discount'] > 0) {
                $discount_amount = ($item['plan_mrp'] * $item['plan_discount'] / 100);
                $total_discount += $discount_amount;
            } else {
                $discount_amount = 0;
            }
            $taxable_amount = $item['plan_mrp'] - $discount_amount;
            $item_taxes = ($taxable_amount * $item['plan_taxes'] / 100);
            $total_taxes += $item_taxes * 100;
        }

        $total_amount = ($total_mrp - $total_discount - $coupon_discount) + $total_taxes;
        return $total_amount;
    }
	
    public function remove_cart_item() {
		// Sanitize inputs
		$item_id    = $this->security->xss_clean($this->input->post('id', true));
		$feature_id = $this->security->xss_clean($this->input->post('feature_id', true));
		$durationId = $this->security->xss_clean($this->input->post('duration_id', true));

		// For logged-in candidate users: Remove item from the database using item_id
		if ($this->session->userdata('logged_in') === TRUE && $this->session->userdata('role') === 'candidate') {
			$user_id = $this->session->userdata('user_id');

			if (!is_numeric($item_id)) {
				echo json_encode(['success' => false, 'message' => 'Invalid item ID']);
				return;
			}

			// Verify that the cart item belongs to the current user
			$this->db->where('id', $item_id);
			$this->db->where('user_id', $user_id);
			$cart_item = $this->db->get('tb_career_service_cart')->row();

			if ($cart_item) {
				$this->db->where('id', $item_id);
				if ($this->db->delete('tb_career_service_cart')) {
					echo json_encode(['success' => true, 'message' => 'Item removed from cart']);
				} else {
					echo json_encode(['success' => false, 'message' => 'Unable to remove item']);
				}
			} else {
				echo json_encode(['success' => false, 'message' => 'Unauthorized or item not found']);
			}
		} 
		// For guest users: Remove candidate item from the session cart using feature_id and duration_id
		else {
			if (!isset($this->cart)) {
				$this->load->library('cart');
			}

			// Ensure feature_id and duration_id are valid
			if (!is_numeric($feature_id) || !is_numeric($durationId)) {
				echo json_encode(['success' => false, 'message' => 'Invalid feature or duration ID']);
				return;
			}

			$item_found = false;
			$cart_items = $this->cart->contents();

			foreach ($cart_items as $item) {
				if ($item['id'] == $feature_id &&
					isset($item['options']['duration_id']) &&
					$item['options']['duration_id'] == $durationId &&
					isset($item['options']['user_type']) &&
					$item['options']['user_type'] === 'candidate') {
					
					// Remove the candidate cart item by setting its quantity to 0
					$this->cart->update(['rowid' => $item['rowid'], 'qty' => 0]);
					$item_found = true;
					break;
				}
			}

			if ($item_found) {
				echo json_encode(['success' => true, 'message' => 'Item removed from cart']);
			} else {
				echo json_encode(['success' => false, 'message' => 'Item not found or not authorized']);
			}
		}
	}

}
