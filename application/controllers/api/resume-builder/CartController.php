<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CartController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('api/Cart_model');
        $this->load->library('cart');
        $this->load->library('session');
    }

    /**
     * Add to Cart
     */
    public function add() {
		$user_id   = $this->session->userdata('user_id');
		$plan_id   = $this->input->post('plan_id');
		$feature_id= $this->input->post('feature_id');
		$price     = $this->input->post('price');

		if (!$plan_id || !$feature_id || !$price) {
			echo json_encode(['status' => false, 'message' => 'Missing required fields']);
			return;
		}

		// ================= LOGIN USER =================
		if ($user_id) {
			// ❗ Remove any existing cart entry for this user+feature
			$this->db->where(['user_id' => $user_id, 'feature_id' => $feature_id])
					 ->delete('tb_ft_cart');

			// Insert the new choice
			$this->db->insert('tb_ft_cart', [
				'user_id'    => $user_id,
				'plan_id'    => $plan_id,
				'feature_id' => $feature_id,
				'price'      => $price,
				'quantity'   => 1,
				'created_at' => date('Y-m-d H:i:s')
			]);

			$cartData = $this->Cart_model->getUserCart($user_id);
		}
		// ================= GUEST USER =================
		else {
			// Remove all items that have the same feature_id in options
			foreach ($this->cart->contents() as $item) {
				if (isset($item['options']['feature_id']) &&
					$item['options']['feature_id'] == $feature_id) {
					$this->cart->remove($item['rowid']);
				}
			}

			// Insert new item
			$this->cart->insert([
				'id'    => $plan_id,
				'qty'   => 1,
				'price' => $price,
				'name'  => 'Plan '.$plan_id,
				'options' => ['feature_id' => $feature_id]
			]);

			$cartData = [
				'items' => $this->cart->contents(),
				'summary' => [
					'total_mrp'      => $this->cart->total(),
					'total_discount' => 0,
					'total_taxes'    => 0,
					'grand_total'    => $this->cart->total()
				]
			];
		}

		echo json_encode([
			'status' => true,
			'message' => 'Cart updated',
			'cart' => $cartData['items'],
			'summary' => $cartData['summary'],
			'csrf_token' => $this->security->get_csrf_hash(),
			'csrf_name'  => $this->security->get_csrf_token_name()
		]);
	}

    /**
     * Cart Count (Navbar)
     */
    public function count() {
        $user_id = $this->session->userdata('user_id');

        if ($user_id) {
            $count = $this->Cart_model->getCartCount($user_id);
        } else {
            $count = $this->cart->total_items();
        }

        echo json_encode([
            'status' => true,
            'count' => $count,
            'csrf_token' => $this->security->get_csrf_hash(),
            'csrf_name'  => $this->security->get_csrf_token_name()
        ]);
    }

    /**
     * Remove Item
     */
    public function remove() {

        $user_id = $this->session->userdata('user_id');
        $plan_id = $this->input->post('plan_id');

        if (!$plan_id) {
            echo json_encode([
                'status' => false,
                'message' => 'Plan ID required'
            ]);
            return;
        }

        // ================= LOGIN USER =================
        if ($user_id) {
            $this->Cart_model->removeItem($user_id, $plan_id);
            $cartData = $this->Cart_model->getUserCart($user_id);
        } 
        // ================= GUEST USER =================
        else {
            foreach ($this->cart->contents() as $item) {
                if ($item['id'] == $plan_id) {
                    $this->cart->remove($item['rowid']);
                }
            }

            $cartData = [
                'items' => $this->cart->contents(),
                'summary' => [
                    'total_mrp'      => $this->cart->total(),
                    'total_discount' => 0,
                    'total_taxes'    => 0,
                    'grand_total'    => $this->cart->total()
                ]
            ];
        }

        echo json_encode([
            'status' => true,
            'message' => 'Removed from cart',
            'cart' => $cartData['items'],
            'summary' => $cartData['summary'],
            'csrf_token' => $this->security->get_csrf_hash(),
            'csrf_name'  => $this->security->get_csrf_token_name()
        ]);
    }

    /**
     * Change the duration/plan of a cart item
     */
    public function update_plan() {
        $user_id    = $this->session->userdata('user_id');
        $plan_id    = $this->input->post('plan_id');
        $feature_id = $this->input->post('feature_id');

        if (!$plan_id || !$feature_id) {
            echo json_encode(['status' => false, 'message' => 'Missing parameters']);
            return;
        }

        // Get plan details
        $plan = $this->db->get_where('tb_ft_plans', ['duration_id' => $plan_id])->row();
        if (!$plan) {
            echo json_encode(['status' => false, 'message' => 'Plan not found']);
            return;
        }

        // ================= LOGIN USER =================
        if ($user_id) {
            $this->Cart_model->addOrUpdateCart([
                'user_id'    => $user_id,
                'plan_id'    => $plan_id,
                'feature_id' => $feature_id,
                'price'      => $plan->plan_total,
                'quantity'   => 1,
            ]);
            $cartData = $this->Cart_model->getUserCart($user_id);
        }
        // ================= GUEST USER =================
        else {
            // remove old
            foreach ($this->cart->contents() as $item) {
                if (isset($item['options']['feature_id']) && $item['options']['feature_id'] == $feature_id) {
                    $this->cart->remove($item['rowid']);
                }
            }
            // insert new
            $this->cart->insert([
                'id'      => $plan_id,
                'qty'     => 1,
                'price'   => $plan->plan_total,
                'name'    => 'Plan ' . $plan_id,
                'options' => ['feature_id' => $feature_id]
            ]);

            $cartData = [
                'items' => $this->cart->contents(),
                'summary' => [
                    'total_mrp'      => $this->cart->total(),
                    'total_discount' => 0,
                    'total_taxes'    => 0,
                    'grand_total'    => $this->cart->total()
                ]
            ];
        }

        echo json_encode([
            'status'  => true,
            'message' => 'Plan updated',
            'cart'    => $cartData['items'],
            'summary' => $cartData['summary'],
            'csrf_token' => $this->security->get_csrf_hash(),
            'csrf_name'  => $this->security->get_csrf_token_name()
        ]);
    }

    /**
     * Get Cart (Page + API)
     */
     public function index(){
        $user_id = $this->session->userdata('user_id');

        // 1. सभी प्लान्स एक बार लोड करें (calculation और dropdown के लिए)
        $allPlans = $this->db
            ->order_by('feature_id, duration_id')
            ->get('tb_ft_plans')
            ->result_array();

        // duration_id => plan details mapping (guest cart के लिए)
        $planById = [];
        foreach ($allPlans as $p) {
            $planById[$p['duration_id']] = $p;
        }

        // 2. कार्ट डेटा प्राप्त करें
        if ($user_id) {
            $cartData = $this->Cart_model->getUserCart($user_id);
        } else {
            // Guest cart – पूरा calculation करें
            $guestItems = $this->cart->contents();
            $totalMrp = 0;
            $totalDiscount = 0;
            $totalTaxes = 0;
            $grandTotal = 0;
            $items = [];

            foreach ($guestItems as $item) {
                $planId    = $item['id'];                 // guest cart में id = duration_id
                $featureId = $item['options']['feature_id'] ?? null;
                $plan      = $planById[$planId] ?? null;

                if ($plan) {
                    $mrp = (float)$plan['plan_mrp'];
                    $discPct = (float)$plan['plan_discount'];
                    $taxPct  = (float)$plan['plan_taxes'];

                    $discAmt = ($mrp * $discPct) / 100;
                    $afterDiscount = $mrp - $discAmt;
                    $taxAmt = ($afterDiscount * $taxPct) / 100;
                    $finalPrice = $afterDiscount + $taxAmt;

                    $item['plan_mrp']       = $mrp;
                    $item['plan_discount']  = $discPct;
                    $item['plan_taxes']     = $taxPct;
                    $item['discount_amount'] = round($discAmt, 2);
                    $item['tax_amount']      = round($taxAmt, 2);
                    $item['final_price']     = round($finalPrice, 2);
                    $item['plan_label']      = $plan['duration'];

                    $totalMrp      += $mrp;
                    $totalDiscount += $discAmt;
                    $totalTaxes    += $taxAmt;
                    $grandTotal    += $finalPrice;
                } else {
                    $finalPrice = (float)$item['price'];
                    $item['final_price'] = $finalPrice;
                    $totalMrp   += $finalPrice;
                    $grandTotal += $finalPrice;
                }
                $items[] = $item;
            }

            $cartData = [
                'items'   => $items,
                'summary' => [
                    'total_mrp'      => round($totalMrp, 2),
                    'total_discount' => round($totalDiscount, 2),
                    'total_taxes'    => round($totalTaxes, 2),
                    'grand_total'    => round($grandTotal, 2),
                ]
            ];
        }

        // 3. फीचर डेटा (नाम, लोगो, और coupon कोड के लिए)
        $features = $this->db->get('tb_ft_features')->result_array();
        $featuresMap = [];       // feature_id => feature row
        $couponCodes = [];       // array of unique coupon codes (visual display ke liye)
        foreach ($features as $f) {
            $featuresMap[(int)$f['feature_id']] = $f;
            if (!empty($f['feature_coupon_discount'])) {
                $couponCodes[] = $f['feature_coupon_discount'];
            }
        }
        $couponCodes = array_unique($couponCodes);

        // 4. हर कार्ट आइटम में फीचर का नाम, लोगो और coupon code जोड़ें
        foreach ($cartData['items'] as &$item) {
            $feature_id = null;
            if (!empty($item['options']['feature_id'])) {
                $feature_id = (int)$item['options']['feature_id'];
            } elseif (!empty($item['feature_id'])) {
                $feature_id = (int)$item['feature_id'];
            }

            if ($feature_id && isset($featuresMap[$feature_id])) {
                $fRow = $featuresMap[$feature_id];
                if (empty($item['feature_name'])) {
                    $item['feature_name'] = $fRow['feature_name'];
                }
                if (empty($item['feature_logo'])) {
                    $item['feature_logo'] = base_url($fRow['feature_logo']);
                }
                // Coupon code हर आइटम में सेट करें (visual display के लिए)
                $item['coupon_code'] = $fRow['feature_coupon_discount'];
            }
        }
        unset($item);

        // 5. ड्रॉपडाउन के लिए plans_by_feature
        $plans_by_feature = [];
        foreach ($allPlans as $p) {
            $plans_by_feature[$p['feature_id']][] = $p;
        }

        // 6. AJAX request (optional, but include coupon info)
        if ($this->input->is_ajax_request()) {
            echo json_encode([
                'status'       => true,
                'cart'         => $cartData['items'],
                'summary'      => $cartData['summary'],
                'coupon_codes' => $couponCodes
            ]);
            return;
        }

        // 7. Normal page load
        $meta = ['title' => 'Your Cart'];

        $data['cart_items']       = $cartData['items'];
        $data['cart_summary']     = $cartData['summary'];
        $data['plans_by_feature'] = $plans_by_feature;
        $data['coupon_codes']     = $couponCodes;   // सारे उपलब्ध coupon codes

        $this->load->view('particles/header', $meta);
        $this->load->view('particles/nav');
        $this->load->view('website/services/CareerServices/cart_view', $data);
        $this->load->view('particles/footer');
    }
}