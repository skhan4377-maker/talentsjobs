<?php defined('BASEPATH') or exit('No direct script access allowed');

class Career_services extends CI_Controller
{

	public function __construct() {
		parent::__construct();
	    $this->load->model('admin/services/CareerService_model');
	}

	public function index() {
		$services['services'] = $this->CareerService_model->get_all_services();
		$this->load->view('career-services/career_services', $services);
	}


    public function fetch_service() {
        $services = $this->CareerService_model->get_services();
        
        if (!empty($services)) {
            // Generate HTML to return for each service
            $html = '';
            foreach ($services as $service) {
                $html .= $this->build_service_html($service);
            }
            echo $html;
        } else {
            echo '<p>No service found.</p>';
        }
    }

    private function build_service_html($service) {
		// Service header with improved design
		$html = '<div class="bg-white p-6 rounded-md shadow mb-6">
					<div class="flex items-center space-x-4">
						<img src="' . $service['service_icon'] . '" alt="' . $service['service_name'] . '" class="w-16 h-16">
						<div>
							<h3 class="text-2xl font-bold text-gray-900">' . ucfirst($service['service_name']) . '</h3>
							<p class="text-gray-600 mt-1">' . $service['service_description'] . '</p>
						</div>
					</div>
				</div>
				<div class="flex space-x-6 overflow-x-auto pb-6 scrollbar-hide">';

		// Loop through each feature for the service
		foreach ($service['features'] as $feature) {
			// Retrieve the price details for the current feature
			$price_data = $this->CareerService_model->featurePrice($feature['feature_id']);
			
			if ($price_data !== null) {
				// Extract the values from the returned array
				$plan_duration = $price_data['plan_duration'];
				$plan_mrp = $price_data['plan_mrp'];
				$plan_discount = $price_data['plan_discount'];
				$plan_total = $price_data['plan_total'];
				$plan_taxes = $price_data['plan_taxes'];
			} else {
				$plan_duration = 'N/A';
				$plan_mrp = 'N/A';
				$plan_discount = 'N/A';
				$plan_total = 'N/A';
				$plan_taxes = 'N/A';
			}

			// Remove trailing ".00" if the value is an integer for prices and discount
			$plan_mrp = intval($plan_mrp) == $plan_mrp ? intval($plan_mrp) : number_format($plan_mrp, 2);
			$plan_total = intval($plan_total) == $plan_total ? intval($plan_total) : number_format($plan_total, 2);
			$plan_discount = intval($plan_discount) == $plan_discount ? intval($plan_discount) : number_format($plan_discount, 2);

			// Build the new service item markup
			$html .= '<div class="min-w-[300px] bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow">';
			$html .= '<a href="' . base_url('career-services/' . $feature['slug']) . '" class="block p-6">';
			$html .= '<div class="flex items-center gap-4 mb-4">';
			$html .= '<img src="' . $feature['feature_logo'] . '" alt="' . $feature['feature_name'] . '" class="h-12 w-12">';
			$html .= '<h4 class="text-xl font-semibold text-gray-900">' . $feature['feature_name'] . '</h4>';
			$html .= '</div>';
			$html .= '<p class="text-gray-600 mb-4">' . $feature['feature_short_description'] . '</p>';

			// Display price details based on discount availability
			if (!empty($plan_discount) && $plan_discount > 0) {
				$html .= '<div class="flex items-baseline gap-2">';
				$html .= '<span class="text-gray-400 line-through text-sm">₹' . $plan_mrp . '</span>';
				$html .= '<span class="text-emerald-600 text-sm font-medium">' . $plan_discount . '% off</span>';
				$html .= '</div>';
				$html .= '<p class="text-2xl font-bold text-gray-900">₹' . $plan_total . '</p>';
			} else {
				$html .= '<p class="text-2xl font-bold text-gray-900">₹' . $plan_mrp . '</p>';
			}

			$html .= '</a>';
			$html .= '</div>';
		}

		$html .= '</div>'; // Close the flex container

		// Append slider controls for larger screens
		$html .= '<button class="hidden lg:block absolute -left-6 top-1/2 -translate-y-1/2 bg-white p-3 rounded-full shadow-lg hover:shadow-xl transition-opacity opacity-0 group-hover:opacity-100">';
		$html .= '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">';
		$html .= '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>';
		$html .= '</svg>';
		$html .= '</button>';
		$html .= '<button class="hidden lg:block absolute -right-6 top-1/2 -translate-y-1/2 bg-white p-3 rounded-full shadow-lg hover:shadow-xl transition-opacity opacity-0 group-hover:opacity-100">';
		$html .= '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">';
		$html .= '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>';
		$html .= '</svg>';
		$html .= '</button>';

		return $html;
	}



    public function view($slug) {
        // Sanitize and validate input slug
        $this->load->helper('security');
        $slug = $this->security->xss_clean($slug);
    
        // Check if the slug corresponds to a service
        $service = $this->CareerService_model->get_service_by_slug($slug);
    
        if (!empty($service)) {
            // Load the service view
            $data['service'] = $service;
            $this->load->view('career-services/list_all_services', $data);
        } else {
            // If no service, check if the slug corresponds to a feature
            $feature = $this->CareerService_model->get_feature_by_slug($slug);
            
            if (!empty($feature)) {
                // Fetch profile feature details using feature_id
                $profile_features = $this->CareerService_model->get_profile_features_by_feature_id($feature['id']);
    
                // Sanitize and pass data to the view with ternary fallback
                $data['feature'] = !empty($feature) ? $this->security->xss_clean($feature) : [];
                $data['profile_features'] = !empty($profile_features['highlightKeys']) ? $this->security->xss_clean($profile_features['highlightKeys']) : [];
                $data['advantage_headings'] = !empty($profile_features['advantageKey']) ? $this->security->xss_clean($profile_features['advantageKey']) : [];
                $data['advantage_headings_title'] = !empty($profile_features['title']) ? $this->security->xss_clean($profile_features['title']) : 'No Title Available';
                $data['advantage_headings_columns'] = !empty($profile_features['columns']) ? $this->security->xss_clean($profile_features['columns']) : [];
                $data['faqs'] = !empty($profile_features['faqKey']) ? $this->security->xss_clean($profile_features['faqKey']) : [];
    
                // Load the feature view with all the necessary data
                $this->load->view('career-services/feature_detail', $data);
            } else {
                // Redirect to 404 if no service or feature is found
                show_404();
            }
        }
    }


    // Public function to fetch service data by slug
    public function fetch_service_with_features($slug) {
        // Fetch service details and features using the slug
        $services = $this->CareerService_model->get_service_with_features($slug);
    
        // Check if services exist
        if ($services) {
            // Generate HTML for the categories dynamically
            $categoryHTML = $this->generate_category_html($services);
            // Generate HTML for service cards dynamically
            $serviceCardsHTML = $this->generate_service_cards_html($services);
            
            // Return the service details, category HTML, and service cards HTML as a JSON response
            echo json_encode([
                'success' => true,
                'categories' => $categoryHTML,
                'serviceCards' => $serviceCardsHTML
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No services found.']);
        }
    }
    
    // Private function to generate HTML for categories dynamically
    private function generate_category_html($services) {
		$html = '';

		// Check if there are services available
		if (!empty($services)) {
			// Get the first service for the title and subtitle
			$service = $services[0]; // Assuming you want the first service's details for the title

			$html .= '<div class="flex flex-col lg:flex-row lg:items-center gap-8 mb-8">
						<div class="space-y-4 flex-1">
							<h1 class="text-4xl font-bold text-gray-900">' . htmlspecialchars(ucfirst($service['service_name'])) . '</h1>
							<p class="text-lg text-gray-600">' . htmlspecialchars($service['service_description']) . '</p>
						</div>
						
						<div class="grid grid-cols-2 sm:grid-cols-3 lg:flex gap-4">';
			
			// Loop through the services to create category items
			foreach ($services as $service) {
				$html .= '<a href="#" class="p-4 bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow">
							<div class="flex flex-col items-center">
								<img src="' . htmlspecialchars(base_url($service['service_icon'])) . '" alt="' . htmlspecialchars($service['feature_name']) . '" class="w-16 h-16 mb-2">
								<span class="font-medium text-gray-900">' . htmlspecialchars($service['feature_name']) . '</span>
							</div>
						  </a>';
			}
			
			$html .= '    </div>
					</div>';
		}

		return $html;
	}

    
	   private function generate_service_cards_html($services) {
		$html = ''; // Initialize the HTML variable

		// Loop through the services to create card items
		foreach ($services as $service) {

			// Retrieve the price details for the current feature
			$price_data = $this->CareerService_model->featurePrice($service['feature_id']);

			if ($price_data !== null) {
				// Extract the values from the returned array
				$plan_duration = $price_data['plan_duration'];
				$plan_mrp = $price_data['plan_mrp'];
				$plan_discount = $price_data['plan_discount'];
				$plan_total = $price_data['plan_total'];
				$plan_taxes = $price_data['plan_taxes'];
			} else {
				$plan_duration = 'N/A';
				$plan_mrp = 'N/A';
				$plan_discount = 'N/A';
				$plan_total = 'N/A';
				$plan_taxes = 'N/A';
			}

			// Remove trailing ".00" if the value is an integer for prices and discount
			$plan_mrp = intval($plan_mrp) == $plan_mrp ? intval($plan_mrp) : number_format($plan_mrp, 2);
			$plan_total = intval($plan_total) == $plan_total ? intval($plan_total) : number_format($plan_total, 2);
			$plan_discount = intval($plan_discount) == $plan_discount ? intval($plan_discount) : number_format($plan_discount, 2);

			// Build the new service card markup using the new design
			$html .= '<div class="group relative bg-white rounded-2xl shadow-lg hover:shadow-xl transition-shadow">
						<a href="' . base_url('career-services/' . $service['slug']) . '" class="block p-6 lg:p-8">
							<div class="flex flex-col lg:flex-row lg:items-start gap-6">
								<div class="flex-shrink-0">
									<img src="' . htmlspecialchars(base_url($service['feature_logo'])) . '" alt="' . htmlspecialchars($service['feature_name']) . '" class="w-24 h-24 lg:w-32 lg:h-32">
								</div>
								<div class="flex-1">
									<h2 class="text-2xl font-bold text-gray-900 mb-3">' . htmlspecialchars(ucfirst($service['feature_name'])) . '</h2>
									<p class="text-gray-600 mb-4">' . htmlspecialchars($service['feature_short_description']) . '</p>';

			// Display the advantages list if available
			if (!empty($service['feature_list_advantages'])) {
				$html .= '<ul class="space-y-2 mb-6">' . htmlspecialchars_decode($service['feature_list_advantages']) . '</ul>';
			}

			// Price section with discount handling
			$html .= '<div class="flex flex-wrap items-baseline gap-3">';
			if (!empty($plan_discount) && $plan_discount > 0) {
				$html .= '<span class="text-gray-400 line-through">₹' . $plan_mrp . '</span>';
				$html .= '<span class="text-green-600 font-medium">' . $plan_discount . '% off</span>';
				$html .= '<span class="text-2xl font-bold text-gray-900">₹' . $plan_total . '</span>';
			} else {
				$html .= '<span class="text-2xl font-bold text-gray-900">₹' . $plan_mrp . '</span>';
			}
			$html .= '</div>
								</div>
							</div>
						</a>
					  </div>';
		}

		return $html; // Return the generated HTML
	}

    
   // Controller function to handle AJAX request
    public function get_plan_durations($feature_slug) {
        // Fetch the feature using the provided slug
        $feature = $this->CareerService_model->get_feature_by_slug($feature_slug);
    
        // Check if the feature exists
        if (!empty($feature)) {
            // Fetch plan durations grouped by experience level for the given feature
            $plan_durations = $this->CareerService_model->get_plan_durations_by_feature_id($feature['id']);
            $feature_custom_label = $feature['feature_custom_label'];
    
            // Check if plan durations are available
            if (!empty($plan_durations)) {
                // Generate HTML for experience levels
                $experience_html = $this->generate_experience_html($plan_durations);
    
                // Generate HTML for plan details for the first experience level
                $first_experience_level = $plan_durations[0]['experience_level'];
                $first_feature_id = $plan_durations[0]['feature_id'];
                $plan_details = $this->CareerService_model->get_plan_details_by_experience($first_experience_level, $first_feature_id);
    
                $duration_html = $this->generate_pricing_html($plan_details, $feature_custom_label);
                
                $priceDetails =$plan_details[0];
                $pricing_html = $this->generate_plan_summary($priceDetails);
                
                // Return the result as JSON for AJAX
                echo json_encode([
                    'experience_html' => $experience_html,
                    'duration_html' => $duration_html,
                    'pricing_html' => $pricing_html,
                ]);
            } else {
                echo json_encode(['error' => 'No plan durations available.']);
            }
        } else {
            echo json_encode(['error' => 'Feature not found.']);
        }
    }
    
    // Generate HTML for Experience Levels
    private function generate_experience_html($plan_durations) {
        $html = '<div class="grid"><div class="flex">';
    
        foreach ($plan_durations as $plan) {
            $experienceId = strtolower(str_replace(' ', '-', $plan['experience_level']));
            $flag_active = $plan['flag_active'] === '1' ? 'bg-sec' : '';
            
            // Check if experience level is not 0, if it's 0, skip rendering
            if ($plan['experience_level_visibility'] == '1') {
                $html .= '<div class="items-center flex flex-grow cursor-pointer justify-center bd-b p-14 ' . $flag_active . ' text-pri bd-pri" 
                    id="exp-' . $experienceId . '" data-experience="' . $plan['experience_level'] . '" data-featureId="' . $plan['feature_id'] .'">
                        <span class="font-medium">Exp: ' . htmlspecialchars($plan['experience_level']) . '</span>
                      </div>';
            }
        }
    
        $html .= '</div></div>';
        return $html;
    }

   // Generate HTML for Pricing Section
   
    private function generate_pricing_html($plan_details, $feature_custom_label) {
        $html = '<div class="flex w-full flex-col items-center justify-center px-6 pb-4 pt-6">
                    <div class="mb-2 flex flex-col">
                        <div class="flex flex-col items-center">
                            <h4 class="font-medium text-black">' . htmlspecialchars($feature_custom_label) . '</h4>
                        </div>
                    </div>';
    
        // Start Plan Options Section
        $html .= '<div class="flex rounded-full bd bg-surface">';
    
        foreach ($plan_details as $plan) {
            $plan_duration = intval($plan['plan_duration']);
            $duration_label = $plan_duration . ' months';
            $plan_id = strtolower(str_replace(' ', '-', $duration_label));
            $flag_active = ($plan['flag_active'] === '1') ? 'bg-sec active' : ''; // Add active class if flag_active is set
    
            // Check if plan_duration_visibility is set to '1' (visible)
            if ($plan['plan_duration_visibility'] == '1') {
                // Visible plan
                $html .= '<span class="m-1 flex grow cursor-pointer gap-1 px-3 py-2 capitalize bd bd-strong ' . $flag_active . ' rounded-full plan-option" 
                          id="plan-' . $plan_id . '" 
                          data-featureid="' . $plan['feature_id'] . '" 
                          data-duration-id="' . $plan['id'] . '" 
                          data-duration="' . $plan_duration . '" 
                          data-price="' . $plan['plan_total'] . '" 
                          data-monthly-price="' . $plan['monthly_cost'] . '">
                          <span>' . $plan['plan_duration'] . '</span>
                          </span>';
            } else {
                // Non-visible plan (add as hidden element with necessary data)
                $html .= '<span class="active" 
                          id="plan-' . $plan_id . '" 
                          data-featureid="' . $plan['feature_id'] . '" 
                          data-duration-id="' . $plan['id'] . '">
                          </span>';
            }
        }
    
        $html .= '</div>'; // Close Plan Options Section
    
        return $html;
    }

    private function generate_plan_summary($plan_details) {
        $html = '';
        if (!empty($plan_details)) {
            $plan = $plan_details;
    
            // Only display plan_mrp if plan_discount has a value greater than 0
            if ($plan['plan_discount'] > 0) {
                $html .= '<div class="flex flex-col items-center gap-0.5 mt-4">
                            <span id="plan-mrp" class="text-xs font-normal text-content-tertiary line-through md:!text-base">₹' . htmlspecialchars(number_format($plan['plan_mrp'],0)) . '</span>';
            } else {
                $html .= '<div class="flex flex-col items-center gap-0.5 mt-4">';
            }
    
            // Only display the discount if it's greater than 0
            if ($plan['plan_discount'] > 0) {
                $html .= '<span class="discount-flag text-sm font-bold text-green-50 md:!relative md:!bg-green-50 md:!p-1 md:!text-xs md:!font-medium md:!text-content-primary-inverse">' . 
                            htmlspecialchars($plan['plan_discount']) . '% OFF
                         </span>';
            }
    
            $html .= '<span id="price-display" class="text-content-primary md:!my-2 md:!text-[32px]" style="font-size: 32px;font-weight: 700;">₹' . htmlspecialchars(number_format($plan['plan_total'],0)) . '</span>
                      <span id="monthly-display" class="font-normal text-content-primary">₹' . htmlspecialchars(number_format($plan['monthly_cost'],0)) . '/month</span>
                    </div>';
    
            // Cart Button & Buy Now Section
            $html .= '<div class="mt-4 flex w-full justify-between gap-2">
                <div class="flex cursor-pointer items-center justify-center rounded bd bd-pri add-to-cart-btn" data-duration-id="' . $plan['plan_duration_id'] . '" data-feature-id="' . $plan['feature_id'] . '">
                    <img src="https://media.foundit.in/trex/public/default/images/career-services/pdp/addCart.svg" alt="addCart" class="size-6">
                </div>
                <button class="rounded bg-pri text-white px-3 py-2 w-full hover:shadow-md">Buy Now</button>
              </div>';
    
            // Price Breakup & Disclaimer Section
            $html .= '<div class="mt-4">
                        <p class="flex gap-1 font-normal" style="margin-left: 60px;font-size: 14px;">
                            <span>Inclusive of all taxes</span>
                            <span class="cursor-pointer underline view-price-breakup">view price breakup</span>
                        </p>
                      </div>';
    
            // Secure Payment Section
            $html .= '<div class="flex items-center justify-center gap-[10px] bd-subtle bd-t px-3 py-2">
                        <img src="https://media.foundit.in/trex/public/default/images/career-services/pdp/secure.svg" alt="Secure Payments" class="size-6">
                        <p class="font-normal text-grey-30">100% Safe and secure payments with '.SITE_NAME.'</p>
                      </div>';
        }
    
        return $html;
    }

    // Fetch plan details based on selected experience level (for AJAX)
    public function get_plan_details_by_experience_ajax() {
        // Get the experience level and feature_id from the AJAX request
        $experience_level = $this->input->post('experience_level');
        $feature_id = $this->input->post('feature_id');
        $feature_slug = $this->input->post('feature_slug');
        // Fetch plan details based on experience level and feature_id
        $plan_details = $this->CareerService_model->get_plan_details_by_experience($experience_level, $feature_id);
    
        $feature = $this->CareerService_model->get_feature_by_slug($feature_slug);
        $feature_custom_label = $feature['feature_custom_label'];
    
        // Generate HTML for pricing section
        $pricing_html = $this->generate_pricing_html($plan_details, $feature_custom_label);
    
        // Return the generated pricing HTML as JSON
        echo json_encode([
            'pricing_html' => $pricing_html
        ]);
    }
    
    // Controller function to handle AJAX request for individual plan details
    public function get_individual_plan_details() {
        // Get plan_duration_id and feature_id from AJAX request
        $plan_duration_id = $this->input->post('plan_duration_id');
        $feature_id = $this->input->post('feature_id');
        
        // Fetch plan details using the model function
        $plan_details = $this->CareerService_model->get_plan_details_by_duration_id($plan_duration_id, $feature_id);
      
        
        // Check if the plan details are available
        if (!empty($plan_details)) {
            // Generate HTML for the pricing section based on the plan details
            $pricing_html = $this->generate_plan_summary($plan_details);
            
            // Return the generated pricing HTML as JSON
            echo json_encode([
                'success' => true,
                'pricing_html' => $pricing_html
            ]);
        } else {
            // Return error message if no plan details are found
            echo json_encode([
                'success' => false,
                'message' => 'No pricing details found for the selected plan.'
            ]);
        }
    }
    
     public function get_price_breakup_details() {
        // Get the post data
        $plan_duration_id = $this->input->post('plan_duration_id');
        $feature_id = $this->input->post('feature_id');
        
        // Fetch the plan details using the model
        $plan_details = $this->CareerService_model->get_plan_details_by_duration_id($plan_duration_id, $feature_id);
        
        if ($plan_details) {
            // Call the private function to render the HTML
            $pricing_html = $this->_render_price_breakup_html($plan_details);
            
            // Return the pricing details in JSON
            echo json_encode(['success' => true, 'pricing_html' => $pricing_html]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No details found for the selected plan.']);
        }
    }

    // Private function to generate the price breakup HTML
    private function _render_price_breakup_html($plan_details) {
        // Ensure all values are numeric, defaulting to 0 if empty or non-numeric
        $plan_mrp = is_numeric($plan_details['plan_mrp']) ? $plan_details['plan_mrp'] : 0;
        $plan_discount = is_numeric($plan_details['plan_discount']) ? $plan_details['plan_discount'] : 0;
        $feature_coupon_discount = is_numeric($plan_details['feature_coupon_discount']) ? $plan_details['feature_coupon_discount'] : 0;
        $plan_taxes = is_numeric($plan_details['plan_taxes']) ? $plan_details['plan_taxes'] : 0;
    
        // Calculate the discount amount
        $discount_amount = $plan_mrp * ($plan_discount / 100);
    
        // Calculate the price after discount
        $price_after_discount = $plan_mrp - $discount_amount;
    
        // Subtract coupon discount (if applicable)
        $price_after_coupon = $price_after_discount - $feature_coupon_discount;
    
        // Calculate the tax amount (assuming tax is applied after coupon discount)
        $tax_amount = $price_after_coupon * $plan_taxes; // No need to multiply by 100
    
        // Calculate the total price after applying taxes
        $total_price = $price_after_coupon + $tax_amount;
    
        // Create the HTML for the price breakup
        $html = '
            <div class="modal-price-breakup-content">
                <h5>' . $plan_details['plan_duration'] . ' Plan</h5>
                <p>based on your experience</p>
    
                <div class="price-details">
                    <div class="price-row">
                        <span>Total MRP</span>
                        <span>₹' . number_format($plan_mrp, 0) . '</span>
                    </div>
                    <div class="price-row">
                        <span>Discount (' . number_format($plan_discount,0) . '%)</span>
                        <span>-₹' . number_format($discount_amount, 0) . '</span>
                    </div>
                    <div class="price-row">
                        <span>Coupon Discount</span>
                        <span>-₹' . number_format($feature_coupon_discount, 0) . '</span>
                    </div>
                    <div class="price-row">
                        <span>Taxes & Fees (' . ($plan_taxes * 100) . '%)</span>
                        <span>₹' . number_format($tax_amount, 0) . '</span>
                    </div>
                    <hr>
                    <div class="price-row total">
                        <span>Total Price</span>
                        <span>₹' . number_format($total_price, 0) . '</span>
                    </div>
                </div>
    
                <button class="close-modal-btn">Got it!</button>
            </div>
        ';
    
        return $html;
    }



}
