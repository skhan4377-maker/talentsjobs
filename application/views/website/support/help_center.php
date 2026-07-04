<section class="bg-gray-50 pt-24 pb-16">
  <div class="max-w-5xl mx-auto px-4">
    <!-- Title -->
    <h1 class="text-4xl font-extrabold text-center text-gray-800 mb-4">Help Center</h1>
    <p class="text-lg text-gray-600 text-center mb-10">
      Find answers to common questions and get the most out of Talents Jobs.
    </p>

    <!-- Accordion -->
    <div id="accordionContainer" class="space-y-5">

      <!-- Accordion Block -->
      <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <button class="accordion-header flex items-center justify-between w-full px-6 py-4 text-left text-lg font-medium text-gray-800 hover:bg-gray-100 transition"
          onclick="toggleExclusiveAccordion(this)">
          Job Seekers
          <svg class="w-5 h-5 transform transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2"
            viewBox="0 0 24 24">
            <path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </button>
        <div class="accordion-body px-6 pb-5 pt-0 text-gray-600 hidden space-y-2">
          <p>• How to create and update your resume on Talents Jobs.</p>
          <p>• Steps to apply for jobs and track applications.</p>
          <p>• Setting up job alerts and preferences.</p>
          <p>• Understanding job recommendations.</p>
        </div>
      </div>

      <!-- Employers -->
      <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <button class="accordion-header flex items-center justify-between w-full px-6 py-4 text-left text-lg font-medium text-gray-800 hover:bg-gray-100 transition"
          onclick="toggleExclusiveAccordion(this)">
          Employers
          <svg class="w-5 h-5 transform transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2"
            viewBox="0 0 24 24">
            <path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </button>
        <div class="accordion-body px-6 pb-5 pt-0 text-gray-600 hidden space-y-2">
          <p>• How to post jobs and manage listings.</p>
          <p>• Viewing and shortlisting applications.</p>
          <p>• Accessing premium employer features.</p>
          <p>• Tips for writing effective job descriptions.</p>
        </div>
      </div>

      <!-- Account & Support -->
      <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <button class="accordion-header flex items-center justify-between w-full px-6 py-4 text-left text-lg font-medium text-gray-800 hover:bg-gray-100 transition"
          onclick="toggleExclusiveAccordion(this)">
          Account & Support
          <svg class="w-5 h-5 transform transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2"
            viewBox="0 0 24 24">
            <path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </button>
        <div class="accordion-body px-6 pb-5 pt-0 text-gray-600 hidden space-y-2">
          <p>• How to reset password and update email or phone.</p>
          <p>• Dealing with login or access issues.</p>
          <p>• Contacting Talents Jobs support team.</p>
          <p>• Frequently asked questions and solutions.</p>
        </div>
      </div>
    </div>

    <!-- CTA -->
    <div class="text-center mt-10">
      <a href="<?= base_url('contact-us') ?>" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-3 rounded-lg transition">
        Still need help? Contact Support
      </a>
    </div>
  </div>
</section>

<script>
  function toggleExclusiveAccordion(clickedButton) {
    const allHeaders = document.querySelectorAll('.accordion-header');
    allHeaders.forEach(header => {
      const icon = header.querySelector('svg');
      const body = header.nextElementSibling;
      if (header === clickedButton) {
        body.classList.toggle('hidden');
        icon.classList.toggle('rotate-180');
      } else {
        body.classList.add('hidden');
        icon.classList.remove('rotate-180');
      }
    });
  }
</script>
