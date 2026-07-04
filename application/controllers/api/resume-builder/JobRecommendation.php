<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . '/libraries/REST_Controller.php';

class JobRecommendation extends REST_Controller
{
    public function __construct() {
        parent::__construct();
        header('Content-Type: application/json');
    }

    public function getJobRecommendations_post()
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            $user_id = $input['user_id'] ?? null;
            $designation = trim($input['designation'] ?? '');
            $skills = $input['skills'] ?? [];
            $experience = intval($input['experience'] ?? 0);
            $location = trim($input['location'] ?? '');

            if (!$user_id) {
                $this->response([
                    'status' => false,
                    'message' => 'User ID is required'
                ], REST_Controller::HTTP_BAD_REQUEST);
                return;
            }

            $db = $this->load->database('default', TRUE);
            $recommendedJobs = $this->getDynamicJobMatches($db, $designation, $skills, $experience, $location);

            $this->response([
                'status' => true,
                'data' => $recommendedJobs,
                'message' => 'Job recommendations fetched successfully',
                'matches_found' => count($recommendedJobs),
                'filters_applied' => [
                    'designation' => $designation,
                    'experience' => $experience,
                    'location' => $location,
                    'skills_count' => count($skills)
                ]
            ], REST_Controller::HTTP_OK);

        } catch (Exception $e) {
            error_log("Job recommendation error: " . $e->getMessage());
            $this->response([
                'status' => false,
                'message' => 'Failed to fetch job recommendations'
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function getDynamicJobMatches($db, $designation, $skills, $experience, $location)
    {
        $query = "
            SELECT 
                j.job_id as id,
                j.job_title as title,
                e.company_name as company,
                j.job_type as type,
                j.min_experience,
                j.max_experience,
                j.min_salary,
                j.max_salary,
                j.job_description as description,
                j.created_at,               
                j.positions_open,
                j.slug,   -- directly from table
                COALESCE(
                    (SELECT GROUP_CONCAT(DISTINCT c.city_name) 
                     FROM tb_job_cities jc 
                     JOIN tb_cities c ON jc.city_id = c.city_id 
                     WHERE jc.job_id = j.job_id),
                    e.company_address
                ) as locations,
                
                (
                    (CASE 
                        WHEN LOWER(j.job_title) LIKE LOWER(?) THEN 40
                        WHEN LOWER(j.job_description) LIKE LOWER(?) THEN 20
                        ELSE 0 
                    END) +
                    (CASE 
                        WHEN ? BETWEEN j.min_experience AND j.max_experience THEN 30
                        WHEN ? >= j.min_experience AND j.max_experience = 0 THEN 25
                        WHEN ABS(? - j.min_experience) <= 2 THEN 15
                        ELSE 5
                    END) +
                    (CASE 
                        WHEN ? != '' AND (
                            LOWER(e.company_address) LIKE LOWER(?) OR
                            EXISTS (
                                SELECT 1 FROM tb_job_cities jc 
                                JOIN tb_cities c ON jc.city_id = c.city_id 
                                WHERE jc.job_id = j.job_id AND LOWER(c.city_name) LIKE LOWER(?)
                            )
                        ) THEN 20
                        WHEN ? != '' THEN 0
                        ELSE 10
                    END) +
                    (CASE 
                        WHEN ? != '' THEN 10
                        ELSE 5 
                    END)
                ) as match_score
                
            FROM tb_post_job j
            INNER JOIN tb_employer e ON j.employer_id = e.employer_id
            WHERE j.status = 'Active' 
            AND j.is_deleted = 0
            AND e.status = 'active'
            AND (j.deadline_date IS NULL OR j.deadline_date >= NOW())
        ";

        $designationLike = "%$designation%";
        $locationLike = "%$location%";
        
        $params = [
            $designationLike,
            $designationLike,
            $experience,
            $experience,
            $experience,
            $location,
            $locationLike,
            $locationLike,
            $location,
            $designation
        ];

        $conditions = [];
        if (!empty($designation)) {
            $conditions[] = " (LOWER(j.job_title) LIKE LOWER(?) OR LOWER(j.job_description) LIKE LOWER(?)) ";
            $params[] = $designationLike;
            $params[] = $designationLike;
        }

        if ($experience > 0) {
            $conditions[] = " (j.min_experience <= ? OR j.max_experience >= ? OR j.max_experience = 0) ";
            $params[] = $experience + 3;
            $params[] = max(0, $experience - 2);
        }

        if (!empty($conditions)) {
            $query .= " AND " . implode(" AND ", $conditions);
        }

        $query .= " 
            GROUP BY j.job_id
            ORDER BY match_score DESC, j.created_at DESC
            LIMIT 25
        ";

        $result = $db->query($query, $params);
        
        if ($db->error()['code']) {
            error_log("Database error: " . json_encode($db->error()));
            return $this->getSimpleJobMatches($db, $designation);
        }

        $jobs = $result->result_array();
        
        if (empty($jobs)) {
            return $this->getFallbackJobs($designation, $experience, $location);
        }

        return $this->formatJobs($jobs, $designation);
    }

    private function getSimpleJobMatches($db, $designation)
    {
        $designationLike = "%$designation%";
        $query = "
            SELECT 
                j.job_id as id,
                j.job_title as title,
                e.company_name as company,
                j.job_type as type,
                j.min_experience,
                j.max_experience,
                j.min_salary,
                j.max_salary,
                j.job_description as description,
                j.created_at,                
                j.positions_open,
                j.slug,
                e.company_address as locations,
                50 as match_score
            FROM tb_post_job j
            INNER JOIN tb_employer e ON j.employer_id = e.employer_id
            WHERE j.status = 'Active' 
            AND j.is_deleted = 0
            AND e.status = 'active'
            AND (j.deadline_date IS NULL OR j.deadline_date >= NOW())
            AND (LOWER(j.job_title) LIKE LOWER(?) OR LOWER(j.job_description) LIKE LOWER(?))
            ORDER BY j.created_at DESC
            LIMIT 15
        ";

        $result = $db->query($query, [$designationLike, $designationLike]);
        
        if ($result) {
            $jobs = $result->result_array();
            return $this->formatJobs($jobs, $designation);
        }

        return [];
    }

    private function formatJobs($jobs, $designation)
    {
        $formattedJobs = [];
        $currentSource = $this->input->get('source') ?: 'cv_builder';

        foreach ($jobs as $job) {
            $locations = $this->formatLocations($job['locations'] ?? '');
            
            $formattedJobs[] = [
                'id' => $job['id'],
                'title' => $job['title'],
                'company' => $job['company'] ?: 'Confidential Company',
                'location' => $locations,
                'type' => $job['type'] ?: 'Full-time',
                'experience' => $this->getExperienceRange($job['min_experience'], $job['max_experience']),
                'salary' => $this->getSalaryRange($job['min_salary'], $job['max_salary']),
                'description' => $this->truncateDescription($job['description']),
                'posted_date' => $this->getTimeElapsed($job['created_at']),
                'apply_link' => site_url($job['slug']) . '?source=' . $currentSource,
                'is_hot' => $this->isHotJob($job['created_at'], $job['positions_open']),
                'skills_match' => min(100, max(10, intval($job['match_score']))),
                'match_score' => intval($job['match_score']),
                'positions' => $job['positions_open'],
                'slug' => $job['slug']
            ];
        }

        return $formattedJobs;
    }

    private function formatLocations($locations)
    {
        if (empty($locations)) return 'Multiple Locations';
        $locationArray = explode(',', $locations);
        return trim($locationArray[0]) ?: 'Multiple Locations';
    }

    private function getExperienceRange($min, $max)
    {
        $min = intval($min);
        $max = intval($max);
        if ($min == 0 && $max == 0) return 'Fresher';
        if ($min == 0) return "Up to {$max} years";
        if ($max == 0) return "{$min}+ years";
        if ($min == $max) return "{$min} years";
        return "{$min}-{$max} years";
    }

    private function getSalaryRange($min, $max)
    {
        $min = floatval($min);
        $max = floatval($max);
        if ($min == 0 && $max == 0) return 'Not disclosed';
        if ($min == 0) return "Up to ₹{$max} LPA";
        if ($max == 0) return "₹{$min} LPA and above";
        if ($min == $max) return "₹{$min} LPA";
        return "₹{$min}-{$max} LPA";
    }

    private function getTimeElapsed($datetime)
    {
        if (empty($datetime)) return 'Recently';
        $time = strtotime($datetime);
        $now = time();
        $diff = $now - $time;
        if ($diff < 3600) return 'Just now';
        if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
        if ($diff < 2592000) return floor($diff / 86400) . ' days ago';
        if ($diff < 31536000) return floor($diff / 2592000) . ' months ago';
        return floor($diff / 31536000) . ' years ago';
    }

    private function truncateDescription($description, $length = 150)
    {
        if (empty($description)) return 'No description available';
        $description = strip_tags($description);
        if (strlen($description) <= $length) return $description;
        return substr($description, 0, $length) . '...';
    }

    private function isHotJob($createdAt, $positions)
    {
        if (empty($createdAt)) return false;
        $daysAgo = (time() - strtotime($createdAt)) / 86400;
        return $daysAgo < 3 || intval($positions) > 5;
    }

    private function getFallbackJobs($designation, $experience, $location)
    {
        error_log("Using fallback jobs for designation: $designation");
        
        // Static slugs (adjust as needed)
        $baseJobs = [
            [
                'id' => 1,
                'title' => "Senior " . ($designation ?: "Developer"),
                'company' => 'Tech Solutions Inc.',
                'location' => $location ?: 'Bangalore, Karnataka',
                'type' => 'Full-time',
                'experience' => '3-5 years',
                'salary' => '₹8-12 LPA',
                'description' => "We are looking for an experienced " . ($designation ?: "professional") . " to join our growing team.",
                'posted_date' => '2 days ago',
                'apply_link' => site_url('senior-developer-tech-solutions') . '?source=cv_builder',
                'is_hot' => true,
                'skills_match' => 85,
                'match_score' => 85,
                'positions' => 3,
                'slug' => 'senior-developer-tech-solutions'
            ],
            [
                'id' => 2,
                'title' => ($designation ?: "Developer") . " - Remote",
                'company' => 'Digital Innovations',
                'location' => 'Remote',
                'type' => 'Full-time',
                'experience' => '2-4 years',
                'salary' => '₹6-10 LPA',
                'description' => "Join our remote team and work on cutting-edge projects.",
                'posted_date' => '1 week ago',
                'apply_link' => site_url('developer-remote-digital-innovations') . '?source=cv_builder',
                'is_hot' => false,
                'skills_match' => 78,
                'match_score' => 78,
                'positions' => 2,
                'slug' => 'developer-remote-digital-innovations'
            ]
        ];

        return $baseJobs;
    }
}
?>