<!-- Include Swiper's CSS in your header -->
<link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />

<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=AW-610384381"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'AW-610384381');
</script>

<section class="bg-white">
	
<!-- Hero Section -->
<div class="relative">
  <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-purple-700 opacity-95"></div>
  <div class="container mx-auto px-4 pt-20 pb-8 relative z-10">
    <div class="max-w-4xl mx-auto text-center">
      <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-white mb-4 sm:mb-6">
        One Search, Millions of Jobs<span class="text-yellow-400">!</span>
      </h1>
      <p class="text-base sm:text-lg md:text-xl text-blue-100 mb-6 sm:mb-10">
        Discover 1M+ opportunities across 50+ industries
      </p>

      <!-- Search Bar -->
      <div class="bg-white rounded-2xl p-3 sm:p-4 shadow-2xl flex flex-col md:flex-row gap-2 sm:gap-3">
        <!-- Job Title -->
        <div class="flex-1 relative">
          <div class="relative">
            <input type="text" id="job_title_home"
              class="w-full p-3 sm:p-4 text-sm sm:text-base border-0 rounded-xl bg-gray-50 focus:ring-2 focus:ring-blue-500 pr-10"
              placeholder="💼 Job title, skills, or company" autocomplete="off">
            <button type="button"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 clear-input clear-input-btn hidden"
                    data-input="job_title_home"
                    data-list="job_profile_list_home"
                    data-hidden="job_profile_id_home">
              <i class="fas fa-times-circle"></i>
            </button>
          </div>
          <input type="hidden" name="key_word" id="job_profile_id_home">
          <ul id="job_profile_list_home"
            class="absolute z-50 w-full bg-white shadow-lg rounded-b-lg mt-1 max-h-60 overflow-y-auto hidden"
            style="top:100%; left:0;"></ul>
        </div>

        <!-- Location -->
        <div class="flex-1 relative">
          <div class="relative">
            <input type="text" id="city_input_home"
              class="w-full p-3 sm:p-4 text-sm sm:text-base border-0 rounded-xl bg-gray-50 focus:ring-2 focus:ring-blue-500 pr-10"
              placeholder="📍 Location" autocomplete="off">
            <button type="button"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 clear-input clear-input-btn hidden"
                    data-input="city_input_home"
                    data-list="city_list_home"
                    data-hidden="city_id_home">
              <i class="fas fa-times-circle"></i>
            </button>
          </div>
          <input type="hidden" name="locations" id="city_id_home">
          <ul id="city_list_home"
            class="absolute z-50 w-full bg-white shadow-lg rounded-b-lg mt-1 max-h-60 overflow-y-auto hidden"
            style="top:100%; left:0;"></ul>
        </div>

        <button id="searchJobsBtn"
          class="w-full md:w-auto p-3 sm:p-4 bg-blue-600 text-white rounded-xl text-sm sm:text-base font-semibold hover:bg-blue-700 transition-all">
          🔍 Search
        </button>
      </div>

      <!-- Quick Stats -->
      <div class="mt-8 sm:mt-10 flex justify-center items-center gap-3 sm:gap-8 text-white text-center flex-nowrap">
        <div class="flex-1"><div class="text-lg sm:text-2xl font-bold">150K+</div><div class="text-blue-200 text-xs sm:text-sm">Jobs Posted</div></div>
        <div class="flex-1"><div class="text-lg sm:text-2xl font-bold">50K+</div><div class="text-blue-200 text-xs sm:text-sm">Companies</div></div>
        <div class="flex-1"><div class="text-lg sm:text-2xl font-bold">2M+</div><div class="text-blue-200 text-xs sm:text-sm">Candidates</div></div>
      </div>
    </div>
  </div>
</div>
 <!-- Resume Steps -->
  <div class="container mx-auto px-2 md:px-4 py-6 md:py-12">
    <h2 class="text-xl md:text-3xl font-bold text-center mb-4 md:mb-8">Create Perfect Resume in 3 Steps</h2>
    <div class="flex md:grid md:grid-cols-3 gap-3 md:gap-6 overflow-x-auto pb-3 md:pb-0 max-w-4xl mx-auto">
      <div class="min-w-[240px] md:min-w-0 text-center p-3 md:p-4 hover:bg-gray-50 rounded-lg transition-all flex-shrink-0">
        <div class="relative w-12 h-12 md:w-16 md:h-16 bg-blue-100/50 rounded-full flex items-center justify-center mx-auto mb-3 md:mb-4">
          <span class="text-2xl md:text-3xl">📄</span>
          <div class="absolute top-0 -mt-4 md:-mt-6 left-1/2 -translate-x-1/2 text-blue-600 font-bold text-base md:text-lg">1</div>
        </div>
        <h3 class="text-base md:text-lg font-semibold mb-1">Choose Template</h3>
        <p class="text-gray-600 text-xs md:text-sm leading-snug">Professional templates for all industries</p>
      </div>
      <div class="min-w-[240px] md:min-w-0 text-center p-3 md:p-4 hover:bg-gray-50 rounded-lg transition-all flex-shrink-0">
        <div class="relative w-12 h-12 md:w-16 md:h-16 bg-green-100/50 rounded-full flex items-center justify-center mx-auto mb-3 md:mb-4">
          <span class="text-2xl md:text-3xl">✍️</span>
          <div class="absolute top-0 -mt-4 md:-mt-6 left-1/2 -translate-x-1/2 text-green-600 font-bold text-base md:text-lg">2</div>
        </div>
        <h3 class="text-base md:text-lg font-semibold mb-1">Add Details</h3>
        <p class="text-gray-600 text-xs md:text-sm leading-snug">Easy-to-fill form with live preview</p>
      </div>
      <div class="min-w-[240px] md:min-w-0 text-center p-3 md:p-4 hover:bg-gray-50 rounded-lg transition-all flex-shrink-0">
        <div class="relative w-12 h-12 md:w-16 md:h-16 bg-purple-100/50 rounded-full flex items-center justify-center mx-auto mb-3 md:mb-4">
          <span class="text-2xl md:text-3xl">🚀</span>
          <div class="absolute top-0 -mt-4 md:-mt-6 left-1/2 -translate-x-1/2 text-purple-600 font-bold text-base md:text-lg">3</div>
        </div>
        <h3 class="text-base md:text-lg font-semibold mb-1">Download PDF</h3>
        <p class="text-gray-600 text-xs md:text-sm leading-snug">Instant download & share options</p>
      </div>
    </div>
    <?php 
		$resumeToken = $this->session->userdata('resume_token'); 

		$url = base_url('resume-templates');

		$params = [
			'utm_source' => 'talentsjobs',
			'utm_medium' => 'referral',
			'utm_campaign' => 'free_resume_cta',
			'utm_content' => 'homepage_button'
		];

		if ($resumeToken) {
			$params['token'] = $resumeToken;
		}

		$finalUrl = $url . '?' . http_build_query($params);
		?>

		<div class="mt-6 md:mt-10 text-center">
			<a href="<?= $finalUrl ?>"
				class="inline-block bg-blue-600 text-white px-4 py-2 md:px-6 md:py-2.5 rounded-md hover:bg-blue-700 transition-all font-semibold text-sm md:text-base shadow-md hover:shadow-blue-200">
				Build Free Resume Now →
			</a>
		</div>
  </div>

  <?php
    $popularSearches = [
      ['title' => 'Freshers',               'icon' => '✨', 'count' => '10.2K'],
      ['title' => 'IT',                     'icon' => '💼', 'count' => '9.8K'],
      ['title' => 'JAVA',                   'icon' => '☕', 'count' => '7.5K'],
      ['title' => 'HR Executive',           'icon' => '👩‍💼', 'count' => '5.3K'],
      ['title' => 'Manual Testing',         'icon' => '🧪', 'count' => '6.1K'],
      ['title' => 'Work from Home',         'icon' => '🏠', 'count' => '11.7K'],
      ['title' => 'Software Engineer',      'icon' => '💻', 'count' => '8.9K'],
      ['title' => 'Internship Accounting',  'icon' => '📚', 'count' => '4.6K'],
      ['title' => 'Part Time',              'icon' => '⏰', 'count' => '6.9K'],
      ['title' => 'Data Entry / Copy Paste','icon' => '🖊️', 'count' => '7.4K'],
      ['title' => 'Sales',                  'icon' => '💰', 'count' => '6.3K'],
      ['title' => 'Banking',                'icon' => '🏦', 'count' => '9.1K'],
      ['title' => 'Digital Marketing',      'icon' => '📈', 'count' => '8.0K'],
      ['title' => 'Near By',                'icon' => '📍', 'count' => '8.0K'],
      ['title' => 'Graphic Designer',       'icon' => '🎨', 'count' => '5.8K'],
      ['title' => 'Customer Support',       'icon' => '📞', 'count' => '6.4K'],
      ['title' => 'Finance Executive',      'icon' => '💹', 'count' => '5.2K'],
    ];
  ?>

  <!-- Trending Searches -->
  <div class="bg-gradient-to-b from-blue-50 via-indigo-50 to-white py-12 md:py-16">
    <div class="container mx-auto px-4 max-w-7xl">
      <div class="text-center mb-10">
        <h2 class="text-2xl md:text-3xl font-bold text-gray-900">
          Trending Job Searches <span class="text-blue-600">🔥</span>
        </h2>
        <p class="text-gray-600 text-sm md:text-base mt-2">
          Most searched terms by job seekers this week
        </p>
      </div>

      <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
        <?php foreach($popularSearches as $search):
          $slug = make_slug($search['title']) . "-jobs";
          $queryValue = strtolower($search['title']) . " jobs";
          $query = http_build_query(['key_word' => $queryValue]);
        ?>
        <a href="<?= base_url('browse-jobs/' . $slug . '?' . $query) ?>"
          class="group flex items-center gap-3 px-4 py-3 bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-lg hover:border-blue-200 transition-all duration-300 hover:-translate-y-1">
          <span class="text-xl md:text-2xl text-blue-600 bg-blue-50 rounded-lg p-2 group-hover:bg-blue-100 transition">
            <?= $search['icon'] ?>
          </span>
          <div class="flex-1 min-w-0">
            <h3 class="text-sm md:text-base font-semibold text-gray-800 truncate group-hover:text-blue-700 transition-colors">
              <?= htmlspecialchars($search['title']) ?>
            </h3>
            <p class="text-xs text-gray-500"><?= htmlspecialchars($search['count']) ?> searches</p>
          </div>
          <svg class="w-4 h-4 text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
          </svg>
        </a>
        <?php endforeach; ?>
      </div>

      <div class="mt-12 text-center">
        <a href="#" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-full font-medium hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 gap-2 text-sm md:text-base shadow-md hover:shadow-lg">
          Explore All Categories
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
          </svg>
        </a>
      </div>
    </div>
  </div>

  <?= $this->load->view('common/header_ads_tj', '', TRUE) ?>

  <?php
    $jobTypes = [
      ['id' => 'Contract', 'icon' => '🏢', 'title' => 'Contract', 'jobs' => '12K+ Roles', 'color' => 'bg-blue-100', 'example_jobs' => ['Software Engineer', 'Project Manager', 'IT Consultant']],
      ['id' => 'Freelancer', 'icon' => '🏠', 'title' => 'Freelancer', 'jobs' => '8K+ Roles', 'color' => 'bg-green-100', 'example_jobs' => ['Graphic Designer', 'Content Writer', 'Web Developer']],
      ['id' => 'Full-time', 'icon' => '🌍', 'title' => 'Full-time', 'jobs' => '5K+ Roles', 'color' => 'bg-purple-100', 'example_jobs' => ['Marketing Manager', 'Sales Executive', 'HR Specialist']],
      ['id' => 'Government', 'icon' => '🏛️', 'title' => 'Government', 'jobs' => '3K+ Roles', 'color' => 'bg-orange-100', 'example_jobs' => ['Civil Servant', 'Public Administrator', 'Policy Analyst']],
      ['id' => 'Hybrid', 'icon' => '🎓', 'title' => 'Hybrid', 'jobs' => '7K+ Roles', 'color' => 'bg-pink-100', 'example_jobs' => ['UX Designer', 'Data Scientist', 'Cloud Architect']],
      ['id' => 'International', 'icon' => '✈️', 'title' => 'International', 'jobs' => '4K+ Roles', 'color' => 'bg-yellow-100', 'example_jobs' => ['Diplomat', 'Export Manager', 'Translator']],
      ['id' => 'Internship', 'icon' => '💻', 'title' => 'Internship', 'jobs' => '6K+ Roles', 'color' => 'bg-cyan-100', 'example_jobs' => ['Junior Developer', 'Marketing Intern', 'Research Assistant']],
      ['id' => 'Part-time', 'icon' => '⏰', 'title' => 'Part-time', 'jobs' => '2K+ Roles', 'color' => 'bg-red-100', 'example_jobs' => ['Customer Support', 'Tutor', 'Event Staff']],
      ['id' => 'Remote', 'icon' => '🌐', 'title' => 'Remote', 'jobs' => '9K+ Roles', 'color' => 'bg-indigo-100', 'example_jobs' => ['Virtual Assistant', 'Remote Developer', 'Customer Success']],
      ['id' => 'Walk-in', 'icon' => '🚪', 'title' => 'Walk-in', 'jobs' => '1.5K+ Roles', 'color' => 'bg-teal-100', 'example_jobs' => ['Sales Executive', 'Store Manager', 'Receptionist']],
      ['id' => 'Night-shift', 'icon' => '🌙', 'title' => 'Night Shift', 'jobs' => '3.2K+ Roles', 'color' => 'bg-gray-100', 'example_jobs' => ['BPO Executive', 'Security Officer', 'Support Engineer']],
      ['id' => 'Temporary', 'icon' => '📅', 'title' => 'Temporary', 'jobs' => '2.4K+ Roles', 'color' => 'bg-lime-100', 'example_jobs' => ['Event Coordinator', 'Seasonal Worker', 'Office Assistant']],
      ['id' => 'Apprenticeship', 'icon' => '🔧', 'title' => 'Apprenticeship', 'jobs' => '1.8K+ Roles', 'color' => 'bg-amber-100', 'example_jobs' => ['Electrician Apprentice', 'Mechanical Trainee', 'Plumber Assistant']],
      ['id' => 'Startups', 'icon' => '🚀', 'title' => 'Startups', 'jobs' => '4.5K+ Roles', 'color' => 'bg-sky-100', 'example_jobs' => ['Product Manager', 'Backend Developer', 'Growth Hacker']],
      ['id' => 'BPO', 'icon' => '📞', 'title' => 'BPO / Call Center', 'jobs' => '5.6K+ Roles', 'color' => 'bg-violet-100', 'example_jobs' => ['Customer Care Executive', 'Technical Support', 'Team Leader']],
    ];
  ?>

  <!-- Job Types -->
  <div class="bg-gradient-to-r from-blue-50 via-indigo-50 to-purple-50 py-10 md:py-14">
    <div class="container mx-auto px-4 max-w-7xl">
      <div class="text-center mb-8 md:mb-10">
        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">
          Explore Job Types <span class="text-blue-600 ml-1">💼</span>
        </h2>
        <p class="text-gray-600 text-sm md:text-base">Find opportunities that match your work preference</p>
      </div>

      <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 md:gap-4">
        <?php foreach($jobTypes as $type):
          $cleanSlug = make_slug($type['title']);
          $slug = $cleanSlug . "-jobs";
          $queryValue = strtolower($type['title']) . " jobs";
          $query = "?key_word=" . urlencode($queryValue);
        ?>
        <a href="<?= base_url('browse-jobs/' . $slug . $query) ?>"
          class="group bg-white rounded-lg border border-gray-200 hover:border-blue-300 hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 p-4 flex flex-col justify-between">
          <div class="flex items-center gap-2 mb-2">
            <div class="<?= $type['color'] ?> p-2 rounded-md">
              <span class="text-xl"><?= $type['icon'] ?></span>
            </div>
            <h3 class="text-sm md:text-base font-semibold text-gray-900 group-hover:text-blue-600">
              <?= htmlspecialchars($type['title']) ?>
            </h3>
          </div>
          <p class="text-gray-500 text-xs mb-2"><?= htmlspecialchars($type['jobs']) ?> Available</p>
          <ul class="text-xs text-gray-600 space-y-0.5 mb-2">
            <?php foreach($type['example_jobs'] as $job): ?>
              <li>• <?= htmlspecialchars($job) ?></li>
            <?php endforeach; ?>
          </ul>
          <span class="mt-auto text-center py-1.5 bg-blue-50 text-blue-600 rounded-md font-medium text-xs md:text-sm group-hover:bg-blue-100 transition">
            View Jobs →
          </span>
        </a>
        <?php endforeach; ?>
      </div>

      <div class="mt-8 text-center">
        <a href="#" class="inline-flex items-center px-5 py-2.5 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition gap-2 text-sm shadow-md hover:shadow-lg">
          Explore All Job Types
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
          </svg>
        </a>
      </div>
    </div>
  </div>

  <!-- Top Companies Section (no custom scrollbar) -->
  <div class="bg-white py-10 md:py-12">
    <div class="container mx-auto px-4 max-w-7xl">
      <div class="text-center mb-8">
        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">
          Top Hiring Companies <span class="text-blue-600 ml-1">🏆</span>
        </h2>
        <p class="text-gray-600 text-sm sm:text-base">Join leading companies across industries</p>
      </div>

      <div class="relative">
        <!-- Navigation -->
        <div class="hidden md:flex gap-3 absolute top-1/2 w-full -translate-y-1/2 z-10">
          <button class="company-prev-btn absolute -left-10 bg-white shadow p-2 rounded-full hover:bg-blue-50 transition">
            ←
          </button>
          <button class="company-next-btn absolute -right-10 bg-white shadow p-2 rounded-full hover:bg-blue-50 transition">
            →
          </button>
        </div>

        <!-- Companies Slider (horizontal scroll, no custom scrollbar) -->
        <div class="flex overflow-x-auto snap-x snap-mandatory pb-6 gap-4 scroll-smooth">
          <?php $companies = [
            ['name' => 'Tech Innovators', 'jobs' => '245', 'logo' => '💻', 'tags' => ['AI', 'Cloud', 'IoT']],
            ['name' => 'Global Finance Corp', 'jobs' => '189', 'logo' => '🏦', 'tags' => ['Banking', 'Investments']],
            ['name' => 'HealthCare Plus', 'jobs' => '167', 'logo' => '🏥', 'tags' => ['Medical', 'Research']],
            ['name' => 'EcoSolutions', 'jobs' => '132', 'logo' => '🌱', 'tags' => ['Sustainability', 'Energy']],
            ['name' => 'Creative Studio', 'jobs' => '98', 'logo' => '🎨', 'tags' => ['Design', 'UX/UI']],
          ]; ?>
          <?php foreach($companies as $company): ?>
          <div class="flex-shrink-0 w-72 snap-start bg-white rounded-xl border border-gray-200 p-4 hover:shadow-md transition group">
            <div class="flex items-center gap-3 mb-4">
              <div class="text-3xl p-3 bg-blue-50 rounded-lg"><?= $company['logo'] ?></div>
              <div>
                <h3 class="text-base font-bold"><?= $company['name'] ?></h3>
                <p class="text-blue-600 text-sm"><?= $company['jobs'] ?> Open Positions</p>
              </div>
            </div>
            <div class="flex flex-wrap gap-2 mb-4">
              <?php foreach($company['tags'] as $tag): ?>
                <span class="px-2 py-0.5 bg-gray-100 rounded-full text-xs"><?= $tag ?></span>
              <?php endforeach; ?>
            </div>
            <div class="flex justify-between text-xs text-gray-600">
              <p>⭐ 4.8 Rating</p>
              <p>📍 Multiple Locations</p>
            </div>
            <a href="<?= base_url('companies/hiring') ?>"
              class="mt-4 w-full block text-center py-2 bg-blue-600 text-white text-sm rounded-md opacity-0 group-hover:opacity-100 transition-opacity duration-300">
              View Jobs
            </a>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>

  <?php
    $industries = [
      ['id'=>20,'icon'=>'💻','name'=>'IT & Software','jobs'=>'25K+','growth'=>'15%','tags'=>['Full Stack','Cloud','Cybersecurity']],
      ['id'=>4,'icon'=>'🏦','name'=>'Banking & Finance','jobs'=>'18K+','growth'=>'12%','tags'=>['Accounting','FinTech','Investment']],
      ['id'=>17,'icon'=>'🏥','name'=>'Healthcare','jobs'=>'32K+','growth'=>'22%','tags'=>['Pharma','Medical','Research']],
      ['id'=>7,'icon'=>'🏗️','name'=>'Construction','jobs'=>'14K+','growth'=>'8%','tags'=>['Civil','Design','Project Mgmt']],
      ['id'=>9,'icon'=>'🛍️','name'=>'Retail & E-Commerce','jobs'=>'21K+','growth'=>'10%','tags'=>['Sales','Merchandising','Digital']],
      ['id'=>11,'icon'=>'🎓','name'=>'Education & Training','jobs'=>'9K+','growth'=>'6%','tags'=>['Teaching','E-Learning','Admin']],
      ['id'=>13,'icon'=>'🚗','name'=>'Automobile','jobs'=>'11K+','growth'=>'9%','tags'=>['EV Tech','Design','Manufacturing']],
      ['id'=>6,'icon'=>'📱','name'=>'Telecom','jobs'=>'8K+','growth'=>'7%','tags'=>['5G','IoT','Networking']],
      ['id'=>15,'icon'=>'🎬','name'=>'Media & Entertainment','jobs'=>'6K+','growth'=>'5%','tags'=>['Production','Editing','Marketing']],
      ['id'=>19,'icon'=>'⚡','name'=>'Energy & Utilities','jobs'=>'10K+','growth'=>'13%','tags'=>['Renewables','Oil & Gas','Power']],
    ];
  ?>

  <!-- Industries Section -->
  <div class="bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4">
      <div class="text-center mb-10">
        <h2 class="text-3xl font-semibold text-gray-900">
          Explore Industries <span class="text-blue-600">🏭</span>
        </h2>
        <p class="text-gray-600 mt-2 text-sm md:text-base">
          Find the best opportunities across top sectors
        </p>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <?php foreach ($industries as $industry):
          $slug = make_slug($industry['name']) . "-jobs";
          $queryValue = strtolower($industry['name']) . " jobs";
          $query = http_build_query(['key_word' => $queryValue]);
        ?>
        <a href="<?= base_url('browse-jobs/' . $slug . '?' . $query) ?>"
          class="group bg-white rounded-xl border border-gray-200 hover:shadow-lg hover:border-blue-500 transition-all duration-300 p-5 flex flex-col justify-between">
          <div class="flex items-center mb-3">
            <div class="text-3xl bg-blue-50 text-blue-600 p-2 rounded-lg mr-3"><?= $industry['icon'] ?></div>
            <div>
              <h3 class="text-base font-semibold text-gray-900 group-hover:text-blue-600">
                <?= htmlspecialchars($industry['name']) ?>
              </h3>
              <p class="text-xs text-gray-500"><?= htmlspecialchars($industry['jobs']) ?> Jobs Available</p>
            </div>
          </div>
          <div class="flex flex-wrap gap-2 mb-4">
            <?php foreach ($industry['tags'] as $tag): ?>
              <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded-full">
                <?= htmlspecialchars($tag) ?>
              </span>
            <?php endforeach; ?>
          </div>
          <div class="mt-auto flex items-center justify-between text-sm text-gray-600 border-t pt-3">
            <span class="text-blue-600 font-medium"><?= htmlspecialchars($industry['growth']) ?> Growth</span>
            <span class="flex items-center group-hover:text-blue-600 font-medium">
              View Jobs
              <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
              </svg>
            </span>
          </div>
        </a>
        <?php endforeach; ?>
      </div>

      <div class="text-center mt-10">
        <a href="<?= base_url('browse-jobs') ?>" class="inline-flex items-center px-6 py-2.5 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition">
          Browse All Industries
          <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
          </svg>
        </a>
      </div>
    </div>
  </div>

  <!-- Talent Showcase with Swiper -->
  <div class="bg-white py-16">
    <div class="container mx-auto px-4 max-w-7xl">
      <div class="text-center mb-12">
        <h2 class="text-4xl font-bold text-gray-900 mb-4">
          Fresh Talent Pool
          <span class="text-blue-600 ml-2">🌟</span>
        </h2>
        <p class="text-gray-600 text-lg">Recently registered professionals ready to hire</p>
      </div>

      <div class="swiper talent-slider">
        <div class="swiper-wrapper">
          <?php
          $baseLogoPath = 'assets/frontend/company/';
          $candidates = [
            ['name'=>'Aarav Singh','edu'=>'B.Tech','logo'=> $baseLogoPath . 'aarav-singh.png','role'=>'Software Engineer'],
            ['name'=>'Sneha Patel','edu'=>'B.Des','logo'=> $baseLogoPath . 'sneha-patel.png','role'=>'UI/UX Designer'],
            ['name'=>'Rahul Sharma','edu'=>'M.Sc','logo'=> $baseLogoPath . 'rahul-sharma.png','role'=>'Data Scientist'],
            ['name'=>'Priya Reddy','edu'=>'M.Tech','logo'=> $baseLogoPath . 'priya-reddy.jpeg','role'=>'DevOps Engineer'],
            ['name'=>'Vikram Kumar','edu'=>'MBA','logo'=> $baseLogoPath . 'vikram-kumar.jpeg','role'=>'Product Manager'],
            ['name'=>'Ananya Gupta','edu'=>'BCA','logo'=> $baseLogoPath . 'ananya-gupta.jpeg','role'=>'Frontend Developer'],
            ['name'=>'Karan Mehta','edu'=>'B.Tech','logo'=> $baseLogoPath . 'karan-mehta.jpeg','role'=>'Backend Developer'],
            ['name'=>'Riya Desai','edu'=>'MCA','logo'=> $baseLogoPath . 'riya-desai.jpeg','role'=>'Full Stack Developer'],
            ['name'=>'Aditya Singh','edu'=>'B.Sc','logo'=> $baseLogoPath . 'aditya-singh.jpeg','role'=>'Data Analyst'],
            ['name'=>'Neha Sharma','edu'=>'B.Com','logo'=> $baseLogoPath . 'neha-sharma.jpeg','role'=>'Finance Analyst'],
            ['name'=>'Manish Rao','edu'=>'B.E.','logo'=> $baseLogoPath . 'manish-rao.jpeg','role'=>'Network Engineer'],
            ['name'=>'Pooja Jain','edu'=>'BBA','logo'=> $baseLogoPath . 'pooja-jain.jpeg','role'=>'HR Specialist'],
            ['name'=>'Suresh Patel','edu'=>'M.Tech','logo'=> $baseLogoPath . 'suresh-patel.jpeg','role'=>'Cloud Architect'],
            ['name'=>'Divya Nair','edu'=>'Ph.D','logo'=> $baseLogoPath . 'divya-nair.jpeg','role'=>'Research Scientist'],
            ['name'=>'Rohit Verma','edu'=>'BCA','logo'=> $baseLogoPath . 'rohit-verma.jpeg','role'=>'QA Engineer'],
          ];

          foreach ($candidates as $candidate): ?>
            <div class="swiper-slide group relative bg-white rounded-2xl border border-gray-200 p-6 hover:border-blue-200 transition-all hover:shadow-xl hover:-translate-y-2">
              <div class="relative mb-6">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-100 to-purple-100 rounded-full w-32 h-32 mx-auto blur-lg opacity-50"></div>
                <img src="<?= base_url($candidate['logo']) ?>" alt="<?= html_escape($candidate['name']) ?>" class="w-32 h-32 rounded-full mx-auto border-4 border-white shadow-lg relative z-10">
              </div>
              <div class="text-center mb-4">
                <h3 class="text-xl font-bold text-gray-900 mb-1"><?= html_escape($candidate['name']) ?></h3>
                <p class="text-blue-600 font-medium"><?= html_escape($candidate['role']) ?></p>
              </div>
              <div class="flex justify-center gap-4 mb-6">
                <div class="text-center">
                  <div class="text-sm text-gray-600 flex items-center gap-1">🎓 <?= html_escape($candidate['edu']) ?></div>
                  <div class="text-xs text-gray-400">Education</div>
                </div>
              </div>
              <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 absolute right-4 top-4 flex gap-2">
                <button class="p-2 bg-white shadow-sm rounded-lg hover:bg-gray-50" title="Message">📩</button>
                <button class="p-2 bg-white shadow-sm rounded-lg hover:bg-gray-50" title="View Profile">💼</button>
              </div>
              <div class="absolute top-4 left-4">
                <span class="px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-full flex items-center gap-1">
                  <span class="w-2 h-2 bg-gray-500 rounded-full"></span> Placed
                </span>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
        <div class="swiper-pagination mt-6"></div>
      </div>

      <div class="mt-12 text-center">
        <a href="#" class="inline-flex items-center px-8 py-3 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 transition-colors gap-2">
          Explore All Talents
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
          </svg>
        </a>
      </div>
    </div>
  </div>

  <!-- Blog Section with Swiper -->
  <div class="py-10 bg-gray-50">
    <div class="container mx-auto px-4 max-w-7xl">
      <div class="text-center mb-8">
        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">
          Career Insights <span class="text-blue-600 ml-1">📚</span>
        </h2>
        <p class="text-gray-600 text-sm sm:text-base">Latest industry trends and professional advice</p>
      </div>

      <div class="swiper blog-slider">
        <div class="swiper-wrapper">
          <?php if (!empty($blogs)) : ?>
            <?php foreach($blogs as $blog): ?>
              <article class="swiper-slide group relative bg-white rounded-lg border border-gray-200 hover:shadow-md transition-all h-full flex flex-col">
                <a href="<?= base_url('blog-detail/' . $blog['slug']) ?>" class="flex flex-col h-full">
                  <div class="relative overflow-hidden aspect-video">
                    <img src="<?= !empty($blog['blogs_banner']) ? base_url('uploads/blogs/' . $blog['blogs_banner']) : base_url('uploads/blogs/noimage.png') ?>"
                         alt="<?= html_escape($blog['blogs_title']) ?>"
                         class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                    <span class="absolute top-2 left-2 px-2 py-0.5 bg-white/95 backdrop-blur-sm rounded text-xs font-medium text-gray-700 shadow-xs flex items-center gap-1.5">
                      <span class="w-1.5 h-1.5 bg-blue-600 rounded-full"></span>
                      <?= htmlspecialchars($blog['category_name']) ?>
                    </span>
                  </div>
                  <div class="p-4 flex flex-col flex-grow">
                    <time class="text-xs text-gray-500 mb-2"><?= date('M j, Y', strtotime($blog['created_at'])) ?></time>
                    <h3 class="text-base font-semibold text-gray-900 mb-1 leading-tight"><?= html_escape($blog['blogs_title']) ?></h3>
                    <p class="text-gray-600 text-xs mb-3 line-clamp-3 leading-snug flex-grow"><?= strip_tags($blog['blogs_content']) ?></p>
                    <div class="flex items-center gap-2 pt-3 border-t border-gray-100">
                      <img src="<?= base_url('assets/frontend/favicon.ico') ?>" alt="Admin" class="w-7 h-7 rounded-full border shadow-sm">
                      <div class="min-w-0">
                        <p class="text-xs font-medium text-gray-700 truncate">Admin</p>
                        <p class="text-xs text-gray-500 truncate"><?= htmlspecialchars($blog['category_name']) ?> Blog</p>
                      </div>
                    </div>
                  </div>
                </a>
                <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex gap-1 z-10">
                  <button class="p-1 bg-white/95 backdrop-blur-sm rounded hover:bg-gray-50 text-gray-600 hover:text-gray-900">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                    </svg>
                  </button>
                  <button class="p-1 bg-white/95 backdrop-blur-sm rounded hover:bg-gray-50 text-gray-600 hover:text-gray-900">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                    </svg>
                  </button>
                </div>
              </article>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="swiper-slide">
              <p class="text-center text-gray-500">No blogs available.</p>
            </div>
          <?php endif; ?>
        </div>
        <div class="swiper-pagination mt-4"></div>
      </div>

      <div class="mt-8 text-center">
        <a href="<?=base_url('blogs')?>" class="inline-flex items-center px-5 py-2.5 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700 transition gap-1.5">
          View All Articles
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
          </svg>
        </a>
      </div>
    </div>
  </div>

  <!-- Newsletter Section -->
  <div class="bg-blue-600 py-16">
    <div class="container mx-auto px-4 text-center">
      <h2 class="text-3xl font-bold text-white mb-4">Get Job Alerts</h2>
      <p class="text-blue-100 mb-8">Sign up for personalized job recommendations</p>
      <div class="max-w-md mx-auto flex gap-2">
        <input type="email" class="w-full px-6 py-4 rounded-xl border-0 focus:ring-2 focus:ring-blue-300" placeholder="Enter your email">
        <button class="px-8 py-4 bg-white text-blue-600 rounded-xl font-semibold hover:bg-gray-100">Subscribe</button>
      </div>
    </div>
  </div>
</section>

<!-- Animated Background Elements (kept as is, no CSS required) -->
<div class="absolute inset-0 overflow-hidden pointer-events-none">
  <div class="absolute w-96 h-96 bg-purple-100 rounded-full blur-3xl opacity-20 -top-48 -right-48"></div>
  <div class="absolute w-96 h-96 bg-blue-100 rounded-full blur-3xl opacity-20 -bottom-48 -left-48"></div>
</div>

<!-- Include Swiper JS just before closing body tag -->
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const jobInput  = document.getElementById('job_title_home');
  const jobHidden = document.getElementById('job_profile_id_home');
  const cityInput = document.getElementById('city_input_home');
  const cityHidden = document.getElementById('city_id_home');

  function slugify(text) {
    return text.toString().toLowerCase().trim()
      .replace(/[^a-z0-9]+/g, '-')
      .replace(/-+/g, '-')
      .replace(/^-+|-+$/g, '');
  }

  document.getElementById('searchJobsBtn').addEventListener('click', performSearch);

  [jobInput, cityInput].forEach(input => {
    input.addEventListener('keypress', function (e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        performSearch();
      }
    });
  });

  function performSearch() {
    const jobTyped  = jobInput.value.trim();
    const cityTyped = cityInput.value.trim();

    jobInput.classList.remove('ring-2', 'ring-red-500');
    cityInput.classList.remove('ring-2', 'ring-red-500');

    if (!jobTyped && !cityTyped) {
      jobInput.classList.add('ring-2', 'ring-red-500');
      cityInput.classList.add('ring-2', 'ring-red-500');
      return;
    }

    let jobSlugPart = '';
    if (jobTyped) {
      const jobsArray = jobTyped.split(',').map(j => slugify(j.trim())).filter(Boolean);
      jobSlugPart = jobsArray.join('-') + '-';
    }

    let citySlug = 'india';
    if (cityTyped) {
      const cityArray = cityTyped.split(',').map(c => slugify(c.trim())).filter(Boolean);
      if (cityArray.length) citySlug = cityArray.join('-');
    }

    const query = new URLSearchParams({
      key_word: jobTyped || "",
      locations: cityTyped || ""
    }).toString();

    window.location.href = `<?= base_url('browse-jobs/') ?>${jobSlugPart}jobs-in-${citySlug}?${query}`;
  }

  // Companies Slider Navigation
  const slider = document.querySelector('.company-slider');
  const prevBtn = document.querySelector('.company-prev-btn');
  const nextBtn = document.querySelector('.company-next-btn');

  if (prevBtn && nextBtn && slider) {
    nextBtn.addEventListener('click', () => {
      slider.scrollBy({ left: 320, behavior: 'smooth' });
    });

    prevBtn.addEventListener('click', () => {
      slider.scrollBy({ left: -320, behavior: 'smooth' });
    });

    // Hide navigation on mobile
    const mq = window.matchMedia('(max-width: 768px)');
    function toggleNav(e) {
      prevBtn.style.display = e.matches ? 'none' : 'block';
      nextBtn.style.display = e.matches ? 'none' : 'block';
    }
    mq.addListener(toggleNav);
    toggleNav(mq);
  }

  // Swiper instances
  const blogSwiper = new Swiper('.blog-slider', {
    slidesPerView: 1,
    spaceBetween: 24,
    loop: false,
    pagination: { el: '.swiper-pagination', clickable: true },
    breakpoints: { 768: { slidesPerView: 2 }, 1024: { slidesPerView: 3 } },
  });

  const talentSwiper = new Swiper('.talent-slider', {
    slidesPerView: 1,
    spaceBetween: 24,
    pagination: { el: '.swiper-pagination', clickable: true },
    breakpoints: { 768: { slidesPerView: 2 }, 1024: { slidesPerView: 4 } },
  });
});
</script>