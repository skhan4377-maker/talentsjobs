<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class InvoiceModel extends CI_Model
{
    protected $payments_table = 'tb_ft_payments';
    protected $user_purchases_table = 'tb_ft_user_purchases';
    protected $features_table = 'tb_ft_features';
    protected $plans_table = 'tb_ft_plans';
    protected $candidate_table = 'tb_candidate';
    protected $cities_table = 'tb_cities';

    /* =========================================================
     * PUBLIC METHODS (SAFE FOR CONTROLLERS)
     * ========================================================= */

    /**
     * Get single invoice with ownership check
     */
    public function getInvoiceDataByUser($payment_id, $user_id)
    {
        $payment = $this->getPaymentWithUser($payment_id, $user_id);
        if (!$payment) return null;

        $purchases = $this->getPurchasesForPayment($payment_id);
        if (empty($purchases)) return null;

        return $this->buildInvoiceResponse($payment, $purchases);
    }

    /**
     * Get all invoices for a user (history)
     */
    public function getUserInvoices($user_id)
    {
        $this->db->select('
            p.id AS payment_id,
            p.payment_id AS razorpay_payment_id,
            p.invoice_no,
            p.status,
            p.amount,
            p.currency,
            p.method,
            p.created_at,
            p.updated_at
        ');
        $this->db->from($this->payments_table . ' p');
        $this->db->where('p.user_id', $user_id);
        $this->db->order_by('p.created_at', 'DESC');

        $payments = $this->db->get()->result_array();

        foreach ($payments as &$payment) {
            $payment['plans'] = $this->getPurchasesForPayment($payment['payment_id']);

            $payment['created_at'] = $payment['created_at']
                ? date('d M Y, h:i A', strtotime($payment['created_at']))
                : '-';

            $payment['paid_at'] = ($payment['status'] === 'paid' && $payment['updated_at'])
                ? date('d M Y, h:i A', strtotime($payment['updated_at']))
                : '-';

            $payment['invoiced_at'] = $payment['created_at'];
        }

        return $payments;
    }

    /* =========================================================
     * INTERNAL METHODS (DO NOT CALL FROM CONTROLLER)
     * ========================================================= */

    private function getPaymentWithUser($payment_id, $user_id)
    {
        $this->db->select('
            p.id,
            p.invoice_no,
            p.payment_id AS razorpay_payment_id,
            p.order_id AS razorpay_order_id,
            p.amount AS paid_amount,
            p.currency,
            p.status,
            p.method,
            p.created_at,
            u.name AS user_name,
            u.email AS user_email,
            u.mobile AS user_phone,
            u.address AS user_address,
            u.postal AS user_postal,
            ci.city_name AS user_city,
            u.country_id AS user_country_id
        ');
        $this->db->from($this->payments_table . ' p');
        $this->db->join($this->candidate_table . ' u', 'u.candidate_id = p.user_id', 'left');
        $this->db->join($this->cities_table . ' ci', 'ci.city_id = u.city_id', 'left');
        $this->db->where('p.id', $payment_id);
        $this->db->where('p.user_id', $user_id);

        return $this->db->get()->row();
    }

    private function getPurchasesForPayment($payment_id)
    {
        $this->db->select('
            up.feature_id,
            up.plan_id,
            up.start_date,
            up.end_date,
            f.feature_name,
            p.duration,
            p.plan_level,
            p.plan_mrp,
            p.plan_discount,
            p.plan_taxes
        ');
        $this->db->from($this->user_purchases_table . ' up');
        $this->db->join($this->features_table . ' f', 'f.feature_id = up.feature_id', 'left');
        $this->db->join($this->plans_table . ' p', 'up.plan_id = p.duration_id', 'left');
        $this->db->where('up.payment_id', $payment_id);

        $purchases = $this->db->get()->result_array();

        foreach ($purchases as &$purchase) {
            $calc = $this->calculatePlanTotals($purchase);
            $purchase = array_merge($purchase, $calc);

            if (!empty($purchase['start_date'])) {
                $purchase['start_date_formatted'] = date('d M Y', strtotime($purchase['start_date']));
            }
            if (!empty($purchase['end_date'])) {
                $purchase['end_date_formatted'] = date('d M Y', strtotime($purchase['end_date']));
            }
        }

        return $purchases;
    }

    private function calculatePlanTotals(array $plan)
    {
        $mrp = floatval($plan['plan_mrp'] ?? 0);
        $discount = floatval($plan['plan_discount'] ?? 0);
        $tax_rate = floatval($plan['plan_taxes'] ?? 0); // stored as percentage (e.g., 18 for 18%)

        $discount_amount = $mrp * ($discount / 100);
        $taxable = $mrp - $discount_amount;
        $tax_amount = $taxable * ($tax_rate / 100);

        return [
            'mrp' => round($mrp, 2),
            'discount_amount' => round($discount_amount, 2),
            'tax_amount' => round($tax_amount, 2),
            'plan_total' => round($taxable + $tax_amount, 2)
        ];
    }

    private function buildInvoiceResponse($payment, array $purchases)
    {
        $subtotal = $discount = $tax = 0;
        $items = [];

        foreach ($purchases as $purchase) {
            $subtotal += $purchase['mrp'];
            $discount += $purchase['discount_amount'];
            $tax += $purchase['tax_amount'];

            $items[] = [
                'feature_name' => $purchase['feature_name'],
                'plan_level' => $purchase['plan_level'],
                'duration' => $purchase['duration'],
                'mrp' => $purchase['mrp'],
                'discount' => $purchase['discount_amount'],
                'tax' => $purchase['tax_amount'],
                'total' => $purchase['plan_total'],
                'start_date' => $purchase['start_date_formatted'] ?? 'N/A',
                'end_date' => $purchase['end_date_formatted'] ?? 'N/A'
            ];
        }

        return [
            'invoice_no' => $payment->invoice_no,
            'payment_id' => $payment->razorpay_payment_id,
            'order_id'   => $payment->razorpay_order_id,
            'status'     => $payment->status,
            'method'     => $payment->method,
            'currency'   => $payment->currency ?? 'INR',
            'created_at' => date('d M Y, h:i A', strtotime($payment->created_at)),

            /* Company details (from constants) */
            'company' => [
                'name'        => defined('SITE_NAME') && SITE_NAME !== '' ? SITE_NAME : null,
                'legal_name'  => defined('SITE_LEGAL_NAME') && SITE_LEGAL_NAME !== '' ? SITE_LEGAL_NAME : null,
                'email'       => defined('CONTACT_EMAIL') && CONTACT_EMAIL !== '' ? CONTACT_EMAIL : null,
                'phone'       => defined('CONTACT_PHONE') && CONTACT_PHONE !== '' ? CONTACT_PHONE : null,
                'address'     => defined('SITE_ADDRESS') && SITE_ADDRESS !== '' ? SITE_ADDRESS : null,
                'city'        => defined('SITE_CITY') && SITE_CITY !== '' ? SITE_CITY : null,
                'state'       => defined('SITE_STATE') && SITE_STATE !== '' ? SITE_STATE : null,
                'country'     => defined('SITE_COUNTRY') && SITE_COUNTRY !== '' ? SITE_COUNTRY : 'India',
                'pincode'     => defined('SITE_PINCODE') && SITE_PINCODE !== '' ? SITE_PINCODE : null,
                'gst'         => defined('SITE_GST') && SITE_GST !== '' ? SITE_GST : null,
                'pan'         => defined('SITE_PAN') && SITE_PAN !== '' ? SITE_PAN : null,
                'website'     => defined('SITE_URL') && SITE_URL !== '' ? SITE_URL : null
            ],

            /* Customer (bill to) */
            'customer' => [
                'name'  => $payment->user_name,
                'email' => $payment->user_email,
                'phone' => $payment->user_phone,
                'city'  => $payment->user_city
            ],

            'items' => $items,

            'summary' => [
                'subtotal'    => round($subtotal, 2),
                'discount'    => round($discount, 2),
                'tax'         => round($tax, 2),
                'grand_total' => round($subtotal - $discount + $tax, 2)
            ]
        ];
    }
}