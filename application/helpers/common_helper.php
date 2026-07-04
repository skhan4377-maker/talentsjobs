<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	date_default_timezone_set('Asia/Kolkata'); 
	$ci =& get_instance();
	$ci->load->database();

	$const_result = $ci->db->select('option_name, option_value, password_key, password_value')->from('tb_options')->get()->result();

	foreach ($const_result as $result) {
		if (!empty($result->option_name) && !defined(strtoupper($result->option_name))) {
			define(strtoupper($result->option_name), $result->option_value);
		}

		if (!empty($result->password_key) && !defined(strtoupper($result->password_key))) {
			define(strtoupper($result->password_key), $result->password_value);
		}
	}
	
	function formatEducation($education) {
		$formatted = str_replace(['_', '-'], ' ', $education);
		$formatted = ucwords(strtolower($formatted));
		
		// PHP 7 compatible code
		$education_lower = strtolower($education);
		$degree_check = substr($education_lower, -6); // 'degree' 6 characters
		return $formatted . ($degree_check === 'degree' ? '' : ' Degree');
	}

	if (!function_exists('get_json_data')) {
		function get_json_data($file_path, $search_term = '', $search_key = 'value') {
			try {
				if (!file_exists($file_path)) {
					return ['error' => 'File not found', 'status' => 404];
				}

				$json_data = json_decode(file_get_contents($file_path), true);
				
				if (json_last_error() !== JSON_ERROR_NONE) {
					return ['error' => 'Invalid JSON format', 'status' => 500];
				}

				if (!empty($search_term)) {
					$json_data = array_filter($json_data, function($item) use ($search_term, $search_key) {
						return isset($item[$search_key]) && 
							   stripos($item[$search_key], $search_term) !== false;
					});
				}

				return ['data' => array_values($json_data), 'status' => 200];

			} catch (Exception $e) {
				return ['error' => $e->getMessage(), 'status' => 500];
			}
		}
	}

	function formatSalary($amount) {
		if (!is_numeric($amount) || $amount <= 0) {
			return 'Not disclosed';
		}

		if ($amount >= 10000000) {
			return number_format($amount / 10000000, 2) . ' Cr';
		} elseif ($amount >= 100000) {
			return number_format($amount / 100000, 2) . ' Lakh';
		} elseif ($amount >= 1000) {
			return number_format($amount / 1000, 2) . 'k';
		}

		return number_format($amount);
	}

	

	if (!function_exists('get_application_statuses')) {
		function get_application_statuses($currentStatus) {
			// Complete order (all possible statuses)
			$order = [
				'Applied',
				'Viewed',
				'Under Review',
				'Shortlist',
				'Scheduled',          // represents "Schedule Interview"
				'Rescheduled',        // represents "Reschedule Interview"
				'Offer Extended',
				'Hired',
				'Completed',
				'Rejected',
				'Withdraw',
				'Canceled'
			];

			// Terminal statuses – no further actions
			$terminal = ['Rejected', 'Withdraw', 'Canceled', 'Completed', 'Hired'];
			if (in_array($currentStatus, $terminal)) {
				return [];
			}

			// Normalize current: treat any interview status as 'Scheduled' for order
			$normalized = $currentStatus;
			if (in_array($currentStatus, ['Interview Scheduled', 'Scheduled', 'Rescheduled'])) {
				$normalized = 'Scheduled';
			}

			$currentIndex = array_search($normalized, $order);
			if ($currentIndex === false) {
				$currentIndex = 0;
			}

			// Get all statuses after the current
			$forward = array_slice($order, $currentIndex + 1);

			// Always allow Rejected and Withdraw
			$forward = array_merge($forward, ['Rejected', 'Withdraw']);
			$forward = array_unique($forward);
			$forward = array_values($forward);

			// --- Interview action logic ---
			$isInInterview = in_array($currentStatus, ['Scheduled', 'Rescheduled', 'Interview Scheduled']);

			if ($isInInterview) {
				// Already scheduled → show "Reschedule Interview"
				$forward = array_diff($forward, ['Scheduled']);        // remove "Schedule"
				if (!in_array('Rescheduled', $forward)) {
					$forward[] = 'Rescheduled';
				}
			} else {
				// Not yet scheduled → show "Schedule Interview"
				$forward = array_diff($forward, ['Rescheduled']);      // remove "Reschedule"
				// Only add "Schedule" if we are before the scheduling stage
				$scheduledIndex = array_search('Scheduled', $order);
				if ($currentIndex < $scheduledIndex) {
					if (!in_array('Scheduled', $forward)) {
						$forward[] = 'Scheduled';
					}
				}
			}

			// Remove raw 'Interview Scheduled' (not used as action)
			$forward = array_diff($forward, ['Interview Scheduled']);

			// Re-sort by order (with special handling for Scheduled/Rescheduled)
			usort($forward, function($a, $b) use ($order) {
				$posA = array_search($a, $order);
				$posB = array_search($b, $order);
				// Treat 'Scheduled' and 'Rescheduled' as if they are at the 'Scheduled' position
				if ($a == 'Scheduled' || $a == 'Rescheduled') {
					$posA = array_search('Scheduled', $order);
				}
				if ($b == 'Scheduled' || $b == 'Rescheduled') {
					$posB = array_search('Scheduled', $order);
				}
				return $posA <=> $posB;
			});

			return array_values($forward);
		}
	}
	if (!function_exists('get_status_label')) {
		function get_status_label($status) {
			$labels = [
				'Applied'             => 'Applied',
				'Viewed'              => 'Viewed',
				'Under Review'        => 'Under Review',
				'Shortlist'           => 'Shortlist Candidate',
				'Scheduled'           => 'Schedule Interview',
				'Rescheduled'         => 'Reschedule Interview',
				'Offer Extended'      => 'Extend Offer',
				'Hired'               => 'Hire Candidate',
				'Completed'           => 'Mark Completed',
				'Rejected'            => 'Reject Application',
				'Withdraw'            => 'Withdraw Application',
				'Canceled'            => 'Cancel Application'
			];
			return $labels[$status] ?? $status;
		}
	}

	if (!function_exists('get_status_classes')) {

		function get_status_classes($status)
		{
			$map = [

				'Applied'             => 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-200',

				'Viewed'              => 'bg-blue-50 text-blue-700 dark:bg-blue-800 dark:text-blue-200',

				'Under Review'        => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-200',

				'Shortlist'           => 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200',

				'Interview Scheduled' => 'bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-200',

				'Scheduled'           => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-800 dark:text-indigo-200',

				'Rescheduled'         => 'bg-orange-100 text-orange-800 dark:bg-orange-800 dark:text-orange-200',

				'Offer Extended'      => 'bg-pink-100 text-pink-800 dark:bg-pink-800 dark:text-pink-200',

				'Hired'               => 'bg-green-500 text-white dark:bg-green-700',

				'Completed'           => 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200',

				'Missed'              => 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-200',

				'Rejected'            => 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-200',

				'Withdraw'            => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',

				'Canceled'            => 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-200',
			];

			return $map[$status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
		}
	}

	if (!function_exists('get_status_icon_class')) {

		function get_status_icon_class($status)
		{
			$icons = [

				'Applied'             => 'far fa-file',

				'Viewed'              => 'far fa-eye',

				'Under Review'        => 'fas fa-search',

				'Shortlist'           => 'fas fa-user-check',

				'Interview Scheduled' => 'fas fa-video',

				'Scheduled'           => 'fas fa-calendar-check',

				'Rescheduled'         => 'fas fa-calendar-alt',

				'Offer Extended'      => 'fas fa-hand-holding-usd',

				'Hired'               => 'fas fa-trophy',

				'Completed'           => 'fas fa-check-circle',

				'Missed'              => 'fas fa-user-times',

				'Rejected'            => 'fas fa-ban',

				'Withdraw'            => 'fas fa-sign-out-alt',

				'Canceled'            => 'fas fa-times-circle',
			];

			return $icons[$status] ?? 'fas fa-question-circle';
		}
	}

	// Helper function for time formatting
	 if(!function_exists('time_elapsed_string')){
		function time_elapsed_string($datetime, $full = false) {
			$now = new DateTime;
			$ago = new DateTime($datetime);
			$diff = $now->diff($ago);

			$string = array(
				'y' => 'year',
				'm' => 'month',
				'd' => 'day',
				'h' => 'hour',
				'i' => 'minute',
				's' => 'second',
			);
			
			foreach ($string as $k => &$v) {
				if ($diff->$k) {
					$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
				} else {
					unset($string[$k]);
				}
			}

			if (!$full) $string = array_slice($string, 0, 1);
			return $string ? implode(', ', $string) . ' ago' : 'just now';
		}
	}

     function RemoveSpecialChar($str){
		$res = preg_replace('/[^a-zA-Z0-9_ -]/s',' ',$str);
		// Returning the result
		return $res;
     }
    
    function isMobileDevice() {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        return (strpos($userAgent, 'Mobile') !== false || strpos($userAgent, 'Android') !== false);
    }

    if (!function_exists('timeAgo')) {
		function timeAgo($time_ago) {
			if (!is_numeric($time_ago)) {
				$time_ago = strtotime($time_ago);
			}
			
			if (!$time_ago) return "Unknown time";

			$cur_time = time();
			$time_elapsed = $cur_time - $time_ago;

			if ($time_elapsed < 1) {
				return "Just now";
			}

			$seconds = $time_elapsed;
			$minutes = round($time_elapsed / 60);
			$hours   = round($time_elapsed / 3600);
			$days    = round($time_elapsed / 86400);
			$weeks   = round($time_elapsed / 604800);
			$months  = round($time_elapsed / 2600640);
			$years   = round($time_elapsed / 31207680);

			if ($seconds <= 60) {
				return "Just now";
			} else if ($minutes <= 60) {
				return $minutes == 1 ? "1 minute ago" : "$minutes minutes ago";
			} else if ($hours <= 24) {
				return $hours == 1 ? "1 hour ago" : "$hours hours ago";
			} else if ($days <= 7) {
				return $days == 1 ? "1 day ago" : "$days days ago";
			} else if ($weeks <= 4.3) {
				return $weeks == 1 ? "1 week ago" : "$weeks weeks ago";
			} else if ($months <= 12) {
				return $months == 1 ? "1 month ago" : "$months months ago";
			} else {
				return $years == 1 ? "1 year ago" : "$years years ago";
			}
		}
	}     
	
	// use this on index page
	if (!function_exists('make_slug')) {
		function make_slug($string) {
			// Convert to lowercase
			$string = strtolower($string);
			// Replace non-alphanumeric chars with hyphens
			$string = preg_replace('/[^a-z0-9]+/i', '-', $string);
			// Trim extra hyphens
			$string = trim($string, '-');
			return $string;
		}
    }
	
	// Generate SVG initials placeholder (only used if logo is empty) use job details page
	function svg_initials_data_uri($initials, $bg = '#EEF2FF', $fg = '#1A0DAB', $w = 240, $h = 240, $fontSize = 120) {
				$initials = htmlspecialchars($initials, ENT_QUOTES, 'UTF-8');
				$svg = "<svg xmlns='http://www.w3.org/2000/svg' width='{$w}' height='{$h}' viewBox='0 0 {$w} {$h}'>
					<rect width='100%' height='100%' rx='28' ry='28' fill='{$bg}'/>
					<text x='50%' y='54%' font-family='Inter, sans-serif' font-size='{$fontSize}' font-weight='700' fill='{$fg}' text-anchor='middle' dominant-baseline='middle'>{$initials}</text>
				</svg>";
				return 'data:image/svg+xml;charset=UTF-8,' . rawurlencode($svg);
	}
	
	
	function SendEmailTo($to, $subject, $message, $bcc = "") {
		$ci =& get_instance();

		$config = [
			'protocol'      => 'smtp',
			'smtp_host'     => 'smtp.hostinger.com',
			'smtp_user'     => 'info@talentsjobs.in',
			'smtp_pass'     => defined('SECRET_SITE_PASSWORD') ? SECRET_SITE_PASSWORD : 'actual_password',
			'smtp_port'     => 587,
			'smtp_crypto'   => 'tls',
			'mailtype'      => 'html',
			'charset'       => 'utf-8',
			'newline'       => "\r\n",
			'crlf'          => "\r\n",
			'smtp_timeout'  => 60,
			'priority'      => 1,
			'validate'      => TRUE, // ✅ FIX
			'wordwrap'      => TRUE,
		];

		if (!isset($ci->email)) {
			$ci->load->library('email', $config);
		} else {
			$ci->email->initialize($config);
		}

		if (!is_array($to)) {
			$to = [$to];
		}

		$successCount = 0;
		$failedRecipients = [];
		$errorMessages = [];

		foreach ($to as $recipient) {
			if (empty($recipient)) continue;

			$ci->email->clear(TRUE);
			$ci->email->set_newline("\r\n"); // ✅ FIX
			$ci->email->set_crlf("\r\n");    // ✅ FIX

			$ci->email->from(
				$config['smtp_user'],
				defined('SITE_NAME') ? SITE_NAME : 'Talent Jobs'
			);

			$ci->email->to($recipient);

			if (!empty($bcc)) {
				$ci->email->bcc($bcc);
			}

			$ci->email->subject($subject);
			$ci->email->message($message);

			if ($ci->email->send()) {
				$successCount++;
			} else {
				$failedRecipients[] = $recipient;
				$errorMessages[$recipient] = $ci->email->print_debugger(['headers', 'subject', 'body']);
			}
		}

		return ($successCount > 0)
			? [
				'status'  => 'success',
				'message' => "Email sent successfully to {$successCount} recipient(s).",
				'sent'    => $successCount,
				'failed'  => $failedRecipients,
				'errors'  => $errorMessages,
			]
			: [
				'status'  => 'failed',
				'message' => 'Email sending failed.',
				'failed'  => $failedRecipients,
				'errors'  => $errorMessages,
			];
	}
    
    if (!function_exists('send_mailercloud_email')) {
        function send_mailercloud_email(
            $to_email,
            $to_name,
            $subject,
            $html,
            $text = '',
            $cc = [],
            $bcc = [],
            $replyTo = [],
            $metadata = [],
            $debug = false
        ) {
            if (empty($text)) {
                $text = strip_tags($html);
            }
    
            // ✅ Prepare recipients
            $recipients = [
                'to' => [[
                    'name'  => $to_name,
                    'email' => $to_email
                ]]
            ];
    
            if (!empty($cc)) {
                $recipients['cc'] = $cc;
            }
            if (!empty($bcc)) {
                $recipients['bcc'] = $bcc;
            }
    
            // ✅ Prepare metadata
            if (empty($metadata['messageId'])) {
                $metadata['messageId'] = bin2hex(random_bytes(16));
            }
            if (empty($metadata['timestamp'])) {
                $metadata['timestamp'] = date("c");
            }
    
            // ✅ Prepare email data
            $email_data = [
                'email' => [
                    'from' => 'noreply@hiring.talentsjobs.in',
                    'fromName' => 'Talents Jobs',
                    'replyTo' => $replyTo,
                    'subject' => $subject,
                    'text' => $text,
                    'html' => $html,
                    'recipients' => $recipients
                ],
                'metadata' => $metadata,
                'version' => '1.0'
            ];
    
            // 🔥 CURL CALL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://email-api.mailercloud.com/email');
            curl_setopt($ch, CURLOPT_POST, true);
            	curl_setopt($ch, CURLOPT_HTTPHEADER, [
				'Content-Type: application/json',
				'Authorization: Swbxe-7a2b1794559320d8fa945e0ece1f56d7-e55e94e3ee5df4f827b2800ee0dc8714'
			]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_ENCODING, '');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($email_data));
    
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
            if (curl_errno($ch)) {
                $error = curl_error($ch);
                curl_close($ch);
    
                return [
                    'status'  => 'failed',
                    'message' => 'cURL Error: ' . $error,
                    'data'    => [
                        'sent_count' => 0,
                        'failed'     => [$to_email],
                        'errors'     => ['cURL Error: ' . $error]
                    ]
                ];
            }
    
            curl_close($ch);
    
            // 🔥 DEBUG MODE
            if ($debug) {
                return [
                    'status'  => 'debug',
                    'message' => $response,
                    'data'    => json_decode($response, true)
                ];
            }
    
            $result = json_decode($response, true);
    
            // 🔥 STRICT VALIDATION
            $isApiSuccess = (
                $http_code == 200 &&
                !empty($result) &&
                isset($result['status']) &&
                strtoupper($result['status']) === 'SUCCESS'
            );
    
            if ($isApiSuccess) {
    
                // ❗ Extra safety
                if (empty($result)) {
                    return [
                        'status'  => 'failed',
                        'message' => 'Empty API response',
                        'data'    => [
                            'sent_count' => 0,
                            'failed'     => [$to_email],
                            'errors'     => ['Empty API response']
                        ]
                    ];
                }
    
                return [
                    'status'  => 'success',
                    'message' => 'Email accepted by MailerCloud',
                    'data'    => [
                        'sent_count' => 1,
                        'failed'     => [],
                        'errors'     => [],
                        'api_response' => $result
                    ]
                ];
    
            } else {
    
                $errorMsg = 'MailerCloud Error | HTTP: ' . $http_code;
    
                if (isset($result['message'])) {
                    $errorMsg = $result['message'];
                } elseif (isset($result['errors'])) {
                    $errorMsg = is_array($result['errors'])
                        ? implode(', ', $result['errors'])
                        : $result['errors'];
                } elseif (!empty($response)) {
                    $errorMsg = $response;
                }
    
                return [
                    'status'  => 'failed',
                    'message' => $errorMsg,
                    'data'    => [
                        'sent_count' => 0,
                        'failed'     => [$to_email],
                        'errors'     => [$errorMsg],
                        'api_response' => $result
                    ]
                ];
            }
        }
    }

	/*if (!function_exists('send_mailercloud_email')) {
		function send_mailercloud_email(
			$to_email,
			$to_name,
			$subject,
			$html,
			$text = '',
			$cc = [],
			$bcc = [],
			$replyTo = [],
			$metadata = [],
			$debug = false
		) {
			if (empty($text)) {
				$text = strip_tags($html);
			}

			// ✅ Prepare recipients
			$recipients = [
				'to' => [[
					'name'  => $to_name,
					'email' => $to_email
				]]
			];

			if (!empty($cc)) {
				$recipients['cc'] = $cc;
			}
			if (!empty($bcc)) {
				$recipients['bcc'] = $bcc;
			}

			// ✅ Prepare metadata
			if (empty($metadata['messageId'])) {
				$metadata['messageId'] = bin2hex(random_bytes(16));
			}
			if (empty($metadata['timestamp'])) {
				$metadata['timestamp'] = date("c");
			}

			// ✅ Prepare email data
			$email_data = [
				'email' => [
					'from' => 'noreply@hiring.talentsjobs.in',
					'fromName' => 'Talents Jobs',
					'replyTo' => $replyTo,
					'subject' => $subject,
					'text' => $text,
					'html' => $html,
					'recipients' => $recipients
				],
				'metadata' => $metadata,
				'version' => '1.0'
			];

			// Initialize cURL
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'https://email-api.mailercloud.com/email');
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
				'Content-Type: application/json',
				'Authorization: Swbxe-7a2b1794559320d8fa945e0ece1f56d7-e55e94e3ee5df4f827b2800ee0dc8714'
			]);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_ENCODING, '');
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($email_data));

			$response = curl_exec($ch);
			$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

			if (curl_errno($ch)) {
				return [
					'status'  => 'failed',
					'message' => 'cURL Error: ' . curl_error($ch),
					'data'    => [
						'sent_count' => 0,
						'failed'     => [$to_email],
						'errors'     => ['cURL Error: ' . curl_error($ch)]
					]
				];
			}

			curl_close($ch);

			if ($debug) {
				return [
					'status'  => 'debug',
					'message' => $response,
					'data'    => json_decode($response, true)
				];
			}

			$result = json_decode($response, true);

			if ($http_code == 200 && isset($result['status']) && $result['status'] === 'SUCCESS') {
				return [
					'status'  => 'success',
					'message' => 'Email sent successfully',
					'data'    => [
						'sent_count' => 1,
						'failed'     => [],
						'errors'     => [],
						'api_response' => $result
					]
				];
			} else {
				$errorMsg = 'Unknown error. HTTP Code: ' . $http_code . ' | Response: ' . $response;
				if (isset($result['message'])) {
					$errorMsg = $result['message'];
				} elseif (isset($result['errors']) && is_array($result['errors'])) {
					$errorMsg = implode(', ', $result['errors']);
				}

				return [
					'status'  => 'failed',
					'message' => $errorMsg,
					'data'    => [
						'sent_count' => 0,
						'failed'     => [$to_email],
						'errors'     => [$errorMsg],
						'api_response' => $result
					]
				];
			}
		}
	}*/
	
	
	/** // use this function resume-builder forgot password page
	 * // Send OTP Email (Forgot Password) API
	 */
	function send_otp_email($email, $otp, $user_name = 'User') {

		$support_email = defined('CONTACT_EMAIL') ? CONTACT_EMAIL : 'info@talentsjobs.in';
		$support_phone = defined('CONTACT_PHONE') ? CONTACT_PHONE : '';

		$subject = 'Password Reset OTP - Talents Jobs';

		$message = "
		<!DOCTYPE html>
		<html>
		<head>
			<meta charset='UTF-8'>
			<style>
				body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
				.container { max-width: 600px; margin: auto; background: #ffffff; padding: 20px; border-radius: 8px; }
				.header { background: #007bff; color: #ffffff; padding: 15px; text-align: center; border-radius: 6px 6px 0 0; }
				.otp-code { font-size: 32px; font-weight: bold; color: #007bff; text-align: center; margin: 20px 0; }
				.footer { text-align: center; color: #666; font-size: 12px; margin-top: 20px; }
			</style>
		</head>
		<body>
			<div class='container'>
				<div class='header'>
					<h2 style='margin:0;'>Password Reset Request</h2>
				</div>

				<p>Hello <strong>{$user_name}</strong>,</p>
				<p>Use the OTP below to reset your password:</p>

				<div class='otp-code'>{$otp}</div>

				<p>This OTP is valid for <strong>10 minutes</strong>.</p>
				<p>If you didn’t request this, please contact us:</p>

				<!-- Support Block -->
				<div style='margin-top:20px;padding:15px;background:#f8f9fa;border:1px solid #e0e0e0;border-radius:6px;'>
					<p style='margin:0 0 6px;font-size:14px;color:#333;'>
						<strong>Need Help?</strong>
					</p>
					<p style='margin:0;font-size:13px;color:#555;line-height:1.6;'>
						Support Email:
						<a href='mailto:{$support_email}' style='color:#007bff;text-decoration:none;'>
							{$support_email}
						</a><br>
						Support Phone: {$support_phone}
					</p>
				</div>

				<div class='footer'>
					&copy; " . date('Y') . " Talents Jobs. All rights reserved.
				</div>
			</div>
		</body>
		</html>
		";

		return send_mailercloud_email($email, $user_name, $subject, $message);
	}



	/** // use this function resume-builder forgot password page
	 *  // Send Password Reset Success Email API
	 */
	function send_password_reset_success_email($email, $user_name = 'User') {

		$support_email = defined('CONTACT_EMAIL') ? CONTACT_EMAIL : 'info@talentsjobs.in';
		$support_phone = defined('CONTACT_PHONE') ? CONTACT_PHONE : '';

		$subject = 'Password Reset Successful - Talents Jobs';

		$message = "
		<!DOCTYPE html>
		<html>
		<head>
			<meta charset='UTF-8'>
			<style>
				body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
				.container { max-width: 600px; margin: auto; background: #ffffff; padding: 20px; border-radius: 8px; }
				.header { background: #28a745; color: #ffffff; padding: 15px; text-align: center; border-radius: 6px 6px 0 0; }
				.footer { text-align: center; color: #666; font-size: 12px; margin-top: 20px; }
			</style>
		</head>
		<body>
			<div class='container'>
				<div class='header'>
					<h2 style='margin:0;'>Password Reset Successful</h2>
				</div>

				<p>Hello <strong>{$user_name}</strong>,</p>
				<p>Your password has been reset successfully.</p>
				<p>If this was not you, contact support immediately:</p>

				<!-- Support Block -->
				<div style='margin-top:20px;padding:15px;background:#f8f9fa;border:1px solid #e0e0e0;border-radius:6px;'>
					<p style='margin:0 0 6px;font-size:14px;color:#333;'>
						<strong>Support Contact</strong>
					</p>
					<p style='margin:0;font-size:13px;color:#555;line-height:1.6;'>
						Email:
						<a href='mailto:{$support_email}' style='color:#28a745;text-decoration:none;'>
							{$support_email}
						</a><br>
						Phone: {$support_phone}
					</p>
				</div>

				<div class='footer'>
					&copy; " . date('Y') . " Talents Jobs. All rights reserved.
				</div>
			</div>
		</body>
		</html>
		";

		return send_mailercloud_email($email, $user_name, $subject, $message);
	}



	

  



	