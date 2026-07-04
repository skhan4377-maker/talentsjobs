<section class="bg-white pt-20 pb-24 md:pb-16">
  <div class="container mx-auto px-4 max-w-7xl">
    <div class="flex flex-col lg:flex-row gap-8 items-start">

      <!-- Job Unavailable Banner -->
      <div class="w-full lg:w-2/3 bg-red-50 border border-red-400 rounded-lg p-8 text-center lg:text-left flex-shrink-0">
        <h2 class="text-2xl font-bold text-red-600 mb-4"><?= $error_banner['heading']; ?></h2>
        <p class="text-gray-700 mb-6"><?= $error_banner['message']; ?></p>
        <a href="<?= $error_banner['cta_url']; ?>" class="inline-block bg-red-600 text-white font-semibold px-6 py-3 rounded hover:bg-red-700 transition">
          <?= $error_banner['cta_text']; ?>
        </a>
      </div>

      <!-- Similar Jobs / Might Be Like -->
      <?php if (!empty($mightBeLike)) : ?>
      <div class="w-full lg:w-1/3 flex-shrink-0">
        <h3 class="text-xl font-semibold mb-4">Similar Jobs You May Like</h3>
        <div class="space-y-4">
          <?php foreach ($mightBeLike as $job) : 
            $jobSlug = generateJobSlug($job); 
            
            // Logo or initials
            $logo = !empty($job['logo']) ? base_url($job['logo']) : '';
            $initials = strtoupper(substr($job['company_name'],0,2)); 
          ?>
            <a href="<?= site_url('job-detail/'.$jobSlug); ?>" class="block p-4 border rounded-lg hover:shadow-lg transition bg-white flex items-center gap-4">
              
              <!-- Logo / Initial -->
              <?php if($logo): ?>
                <img src="<?= $logo; ?>" alt="<?= $job['company_name']; ?>" class="w-12 h-12 rounded-full object-cover flex-shrink-0">
              <?php else: ?>
                <div class="w-12 h-12 rounded-full bg-gray-200 text-gray-700 flex items-center justify-center font-bold text-lg flex-shrink-0">
                  <?= $initials; ?>
                </div>
              <?php endif; ?>

              <!-- Job Info -->
              <div class="flex-1">
                <h4 class="text-lg font-medium text-gray-800"><?= $job['job_title']; ?></h4>
                <p class="text-gray-600 text-sm"><?= !empty($job['job_locations']) ? $job['job_locations'] : 'Multiple Locations'; ?> | <?= strip_tags($job['company_name']); ?></p>
                <p class="text-gray-500 text-sm mt-1"><?= strip_tags($job['experience_range']); ?> years experience</p>
              </div>

            </a>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>

    </div>
  </div>
</section>
