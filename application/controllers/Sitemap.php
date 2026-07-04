<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sitemap extends CI_Controller {

    private $sitemap_path = './sitemap/';        // folder to save XML
    private $max_urls_per_file = 15000;         // Google sitemap limit (URLs)

    public function __construct() {
        parent::__construct();
        $this->load->model('Jobs/Jobs_model');
    }

    public function generate() {
        $total_jobs = $this->Jobs_model->count_active_jobs();
        $jobs_per_file = $this->max_urls_per_file; // ✅ ab har job ka sirf 1 URL hoga
        $total_files = ceil($total_jobs / $jobs_per_file);

        $sitemap_files = [];

        for ($i = 0; $i < $total_files; $i++) {
            $offset = $i * $jobs_per_file;
            $jobs = $this->Jobs_model->get_active_jobs($jobs_per_file, $offset);
            $xml = $this->generate_job_sitemap_xml($jobs);

            $filename = "sitemap_jobs_" . ($i + 1) . ".xml";
            file_put_contents($this->sitemap_path . $filename, $xml);
            $sitemap_files[] = $filename;
        }

        // Generate sitemap index
        $this->generate_index_sitemap($sitemap_files);

        echo "Sitemap generation completed!";
    }

    private function generate_job_sitemap_xml($jobs) {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($jobs as $job) {
			// Object ya array safe handling
			$slug = is_array($job) ? $job['slug'] : $job->slug;

			// ❌ No prefix (job-detail/) — direct slug
			$url = base_url($slug);

			$lastmod = !empty($job['updated_at']) 
						? date('Y-m-d', strtotime($job['updated_at'])) 
						: date('Y-m-d');

			$xml .= $this->generate_url_xml($url, $lastmod);
		}

        $xml .= '</urlset>';
        return $xml;
    }

    private function generate_url_xml($loc, $lastmod) {
        return "<url>
                    <loc>{$loc}</loc>
                    <lastmod>{$lastmod}</lastmod>
                    <changefreq>weekly</changefreq>
                    <priority>0.6</priority>
                </url>";
    }

    private function generate_index_sitemap($sitemap_files) {
		$index_xml = '<?xml version="1.0" encoding="UTF-8"?>';
		$index_xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		foreach ($sitemap_files as $file) {
			$index_xml .= "<sitemap>
							<loc>" . base_url("sitemap/" . $file) . "</loc>
							<lastmod>" . date('Y-m-d') . "</lastmod>
						</sitemap>";
		}
		$index_xml .= "</sitemapindex>";

		// Save sitemap index in /sitemap/
		file_put_contents($this->sitemap_path . "sitemap_index.xml", $index_xml);

		// ✅ Create root sitemap.xml pointing to sitemap_index.xml
		$root_sitemap_xml = '<?xml version="1.0" encoding="UTF-8"?>';
		$root_sitemap_xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		$root_sitemap_xml .= "<sitemap>
								<loc>" . base_url("sitemap/sitemap_index.xml") . "</loc>
								<lastmod>" . date('Y-m-d') . "</lastmod>
							  </sitemap>";
		$root_sitemap_xml .= '</sitemapindex>';

		file_put_contents(FCPATH . "sitemap.xml", $root_sitemap_xml); // Save at root
	}

}
