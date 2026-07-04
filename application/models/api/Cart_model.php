<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cart_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Add or update cart item (based on feature_id)
     * If same feature already in cart, update plan & quantity
     */
    public function addOrUpdateCart($item) {
        $existing = $this->db->get_where('tb_ft_cart', [
            'user_id'    => $item['user_id'],
            'feature_id' => $item['feature_id']
        ])->row();

        if ($existing) {
            $this->db->where('id', $existing->id);
            return $this->db->update('tb_ft_cart', [
                'plan_id'    => $item['plan_id'],
                'price'      => $item['price'],
                'quantity'   => $item['quantity'],
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            return $this->db->insert('tb_ft_cart', $item);
        }
    }
	
	public function getCartCount($user_id) {
		return $this->db
			->where('user_id', $user_id)
			->from('tb_ft_cart')
			->count_all_results();
	}

    /**
     * Remove a specific cart item (by plan_id)
     */
    public function removeItem($userId, $planId) {
        $this->db->where(['user_id' => $userId, 'plan_id' => $planId]);
        return $this->db->delete('tb_ft_cart');
    }

    /**
     * Get full cart details for a user, including calculated totals
     */
    public function getUserCart($userId) {
        $items = $this->db->select("
                c.id as cart_id,
                c.plan_id,
                c.feature_id,
                c.quantity,
                f.feature_name,
                f.feature_logo,
                f.feature_tag,
                f.slug,
                p.plan_level,
                p.experience_range,
                p.duration,
                p.plan_mrp,
                p.plan_discount,
                p.plan_taxes,
                p.plan_total,
                p.monthly_cost
            ")
            ->from('tb_ft_cart c')
            ->join('tb_ft_features f', 'f.feature_id = c.feature_id', 'left')
            ->join('tb_ft_plans p', 'p.duration_id = c.plan_id', 'left')
            ->where('c.user_id', $userId)
            ->get()
            ->result_array();

        $totalMrp = 0;
        $totalDiscount = 0;
        $totalTaxes = 0;
        $grandTotal = 0;

        foreach ($items as &$item) {
            $mrp = (float) $item['plan_mrp'];
            $discountPercent = (float) $item['plan_discount']; // e.g., 10 for 10%
            $taxPercent = (float) $item['plan_taxes'];         // e.g., 18 for 18%

            // Discount amount
            $discountAmt = ($mrp * $discountPercent) / 100;
            $afterDiscount = $mrp - $discountAmt;

            // Tax amount
            $taxAmt = ($afterDiscount * $taxPercent) / 100;
            $finalPrice = $afterDiscount + $taxAmt;

            $item['discount_amount'] = round($discountAmt, 2);
            $item['tax_amount'] = round($taxAmt, 2);
            $item['final_price'] = round($finalPrice, 2);

            // Full URL for logo
            if (!empty($item['feature_logo'])) {
                $item['feature_logo'] = base_url($item['feature_logo']);
            }

            $totalMrp += $mrp;
            $totalDiscount += $discountAmt;
            $totalTaxes += $taxAmt;
            $grandTotal += $finalPrice;
        }

        $summary = [
            'total_mrp'      => round($totalMrp, 2),
            'total_discount' => round($totalDiscount, 2),
            'total_taxes'    => round($totalTaxes, 2),
            'grand_total'    => round($grandTotal, 2),
        ];

        return ['items' => $items, 'summary' => $summary];
    }

    /**
     * Clear entire cart for a user (after successful checkout)
     */
    public function clearCartByUser($user_id) {
        $this->db->where('user_id', $user_id);
        return $this->db->delete('tb_ft_cart');
    }
}
?>