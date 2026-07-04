<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ServicesFeatures extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('api/ServicesFeatures_model');
        $this->load->helper('url'); // for base_url()
    }

    /**
     * GET /api/services
     * Fetch all active services and load view
     */
    /*public function services_get() {
        // SEO meta data
        $meta['title'] = 'All Services - Career & Professional Services';
        $meta['description'] = 'Browse all our professional services including career coaching, resume writing, interview preparation, and more.';
        $meta['meta_keywords'] = 'services, career services, professional services, job help';
        $meta['canonical'] = current_url();

        $limit = (int) ($this->input->get('limit') ?? 20);
        $offset = (int) ($this->input->get('offset') ?? 0);

        $services = $this->ServicesFeatures_model->get_all_services($limit, $offset);

        $data = array_map(function ($s) {
            $icon = !empty($s['service_icon']) ? $s['service_icon'] : 'icons/default.png';
            return [
                'service_id'          => (int) $s['service_id'],
                'service_name'        => $s['service_name'] ?? '',
                'service_description' => $s['service_description'] ?? '',
                'service_icon'        => base_url($icon),
                'is_active'           => $s['is_active'] ?? 'yes',
                'create_dt'           => $s['create_dt'] ?? null,
                'update_dt'           => $s['update_dt'] ?? null,
            ];
        }, $services);

        $view_data = [
            'status' => true,
            'limit'  => $limit,
            'offset' => $offset,
            'count'  => count($data),
            'data'   => $data
        ];

        $this->load->view('particles/header', $meta);
        $this->load->view('particles/nav');
        $this->load->view('website/services/CareerServices/index', $view_data);
        $this->load->view('particles/footer');
    }*/

    /**
     * GET /api/services/{id}
     * Fetch single service details
     */
    /*public function service_get($id = null) {
        if (!$id) {
            $meta['title'] = 'Error - Service ID Required';
            $meta['description'] = 'Invalid request. Service ID is missing.';
            $meta['canonical'] = current_url();
            $this->load->view('particles/header', $meta);
            $this->load->view('particles/nav');
            $this->load->view('errors/error_404', ['message' => 'Service ID is required']);
            $this->load->view('particles/footer');
            $this->output->set_status_header(400);
            return;
        }

        $service = $this->ServicesFeatures_model->get_service_by_id($id);
        if ($service) {
            $meta['title'] = htmlspecialchars($service['service_name'] ?? 'Service Details') . ' - Talents Services';
            $meta['description'] = strip_tags($service['service_description'] ?? 'Detailed information about this service.');
            $meta['canonical'] = current_url();

            $view_data = [
                'status'  => true,
                'message' => 'Service details fetched successfully',
                'data'    => $service
            ];
            $this->load->view('particles/header', $meta);
            $this->load->view('particles/nav');
            $this->load->view('services/detail', $view_data);
            $this->load->view('particles/footer');
        } else {
            $meta['title'] = 'Service Not Found';
            $meta['description'] = 'The requested service does not exist.';
            $meta['canonical'] = current_url();
            $this->load->view('particles/header', $meta);
            $this->load->view('particles/nav');
            $this->load->view('errors/error_404', ['message' => 'Service not found']);
            $this->load->view('particles/footer');
            $this->output->set_status_header(404);
        }
    }*/

    /**
     * GET /api/features?service_id={id}
     * Fetch features for a service with their cheapest plan
     */
    /**
	 * GET /api/features
		* Fetch all features from all services (reuses get_all_services + get_features_by_service)
	 */
	public function features_get() {
		// SEO meta data
		$meta['title'] = 'All Career Features - Boost Your Job Search';
		$meta['description'] = 'Explore all our professional features including resume building, interview preparation, skill assessments, and more.';
		$meta['meta_keywords'] = 'career features, job search tools, resume builder, interview prep';
		$meta['canonical'] = current_url();

		// 1. Get all active services (reusing existing method)
		$services = $this->ServicesFeatures_model->get_all_services(); // no limit = all

		// 2. Collect features from each service
		$all_features = [];
		foreach ($services as $service) {
			$features = $this->ServicesFeatures_model->get_features_by_service($service['service_id']);
			$all_features = array_merge($all_features, $features);
		}

		// 3. Process each feature (cheapest plan, logo, etc.) – same logic as before
		$data = [];
		foreach ($all_features as $f) {
			$plans = $this->ServicesFeatures_model->get_feature_plans($f['feature_id']);
			$lowest_plan = null;
			if (!empty($plans)) {
				usort($plans, function($a, $b) {
					return (float)$a['plan_total'] <=> (float)$b['plan_total'];
				});
				$cheapest = $plans[0];
				$mrp = round((float)$cheapest['plan_mrp'], 0);
				$discountPercent = round((float)$cheapest['plan_discount'], 0);
				$final_price = round((float)$cheapest['plan_total'], 0);

				$lowest_plan = [
					'duration'    => $cheapest['duration'],
					'mrp'         => $mrp,
					'discount'    => $discountPercent,
					'final_price' => $final_price,
					'plan_level'  => $cheapest['plan_level'] ?? null
				];
			}

			$logo = !empty($f['feature_logo']) ? $f['feature_logo'] : 'icons/default.png';
			
			// Also include service name for reference (optional)
			$service_name = '';
			foreach ($services as $s) {
				if ($s['service_id'] == $f['service_id']) {
					$service_name = $s['service_name'];
					break;
				}
			}

			$data[] = [
				'feature_id'                => (int)$f['feature_id'],
				'service_id'                => (int)$f['service_id'],
				'service_name'              => $service_name,   // added for context
				'feature_name'              => $f['feature_name'] ?? '',
				'feature_short_description' => $f['feature_short_description'] ?? '',
				'slug'                      => $f['slug'] ?? '',
				'redirect_url'              => $f['redirect_url'] ?? null,
				'feature_logo'              => base_url($logo),
				'feature_coupon_discount'   => $f['feature_coupon_discount'] ?? null,
				'status'                    => $f['is_active'],
				'plan'                      => $lowest_plan
			];
		}

		$view_data = [
			'status' => true,
			'count'  => count($data),
			'data'   => $data
		];

		$this->load->view('particles/header', $meta);
		$this->load->view('particles/nav');
		$this->load->view('website/services/CareerServices/index', $view_data);
		$this->load->view('particles/footer');
	}

    /**
     * GET /api/features/{id}
     * Fetch single feature with all details (tags, plans, benefits, Q&A)
     */
    // In ServicesFeatures controller

	public function feature_get($slug = null) {
		if (!$slug) {
			show_404();
		}

		$feature = $this->ServicesFeatures_model->get_feature_by_slug($slug);
		if (!$feature) {
			show_404();
		}

		// Fetch related data (tags, plans, benefits, Q&A) – same as before
		$tags = $this->ServicesFeatures_model->get_feature_tags($feature['feature_id']);
		$plans = $this->ServicesFeatures_model->get_feature_plans($feature['feature_id']);
		$benefit_headers = $this->ServicesFeatures_model->get_feature_benefit_headers($feature['feature_id']);
		$benefit_comparisons = $this->ServicesFeatures_model->get_feature_benefit_comparisons($feature['feature_id']);
		$qas = $this->ServicesFeatures_model->get_feature_qas($feature['feature_id']);

		// Process plans with tax calculation – unchanged
		$plans = array_map(function($d) use ($feature) {
			$mrp = round((float)$d['plan_mrp'], 0);
			$discountPercent = round((float)$d['plan_discount'], 0);
			$discountAmount = round($mrp * $discountPercent / 100, 0);
			$priceAfterDiscount = $mrp - $discountAmount;

			$couponDiscount = round((float)($feature['feature_coupon_discount'] ?? 0), 0);
			$taxPercent = isset($d['plan_taxes']) ? (float)$d['plan_taxes'] : 18;
			$taxAmount = round(($priceAfterDiscount - $couponDiscount) * ($taxPercent / 100), 0);
			$total = round(($priceAfterDiscount - $couponDiscount) + $taxAmount, 0);

			return array_merge($d, [
				'mrp' => $mrp,
				'discount' => $discountPercent,
				'plan_discount_amount' => $discountAmount,
				'final_price' => $priceAfterDiscount,
				'coupon_discount' => $couponDiscount,
				'plan_taxes_amount' => $taxAmount . " ({$taxPercent}%)",
				'plan_total' => $total
			]);
		}, $plans);

		$feature_data = [
			'feature_id'                => (int) $feature['feature_id'],
			'service_id'                => (int) $feature['service_id'],
			'feature_name'              => $feature['feature_name'] ?? '',
			'feature_tag'               => $feature['feature_tag'] ?? '',
			'feature_short_description' => $feature['feature_short_description'] ?? '',
			'feature_full_description'  => $feature['feature_full_description'] ?? '',
			'feature_logo'              => !empty($feature['feature_logo']) ? base_url($feature['feature_logo']) : base_url('icons/default.png'),
			'benefit_logo'              => !empty($feature['benefit_logo']) ? base_url($feature['benefit_logo']) : base_url('icons/default.png'),
			'feature_video_gif'         => !empty($feature['feature_video_gif']) ? base_url($feature['feature_video_gif']) : null,
			'feature_coupon_discount'   => $feature['feature_coupon_discount'] ?? null,
			'status'                    => $feature['is_active'],
			'slug'                      => $feature['slug'] ?? '',
			'redirect_url'              => $feature['redirect_url'] ?? null,
			'feature_label'             => $feature['feature_custom_label'] ?? '',
			'tags'                      => $tags,
			'plans'                     => $plans,
			'benefit_headers'           => $benefit_headers ?: null,
			'benefit_comparisons'       => $benefit_comparisons,
			'qas'                       => $qas
		];

		$meta['title'] = htmlspecialchars($feature_data['feature_name']) . ' - Detailed Feature Information';
		$meta['description'] = strip_tags($feature_data['feature_short_description'] . ' ' . ($feature_data['feature_full_description'] ?? ''));
		$meta['canonical'] = current_url();

		$view_data = [
			'status'  => true,
			'message' => 'Feature details fetched successfully',
			'data'    => $feature_data
		];

		$this->load->view('particles/header', $meta);
		$this->load->view('particles/nav');
		$this->load->view('website/services/CareerServices/view', $view_data);
		$this->load->view('particles/footer');
	}

}