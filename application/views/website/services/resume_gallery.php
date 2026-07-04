<style>
/* Floating Animation */
@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-8px); }
}
.hover\:float:hover {
    animation: float 3s ease-in-out infinite;
}

/* Scrollbar */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}
::-webkit-scrollbar-track {
    background: #f1f5f9;
}
::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}
::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Filter Button */
.filter-btn:hover {
    background-color: #2563eb;
    color: #ffffff;
    border-color: #2563eb;
}
</style>

<!-- Section -->
<section class="bg-gradient-to-b from-blue-50 to-white pt-16 pb-20 md:pb-14">
  <div class="container mx-auto px-4 max-w-7xl">    
	
	<!-- Header -->
    <div class="text-center mb-12 space-y-3">
      <h1 class="text-3xl md:text-5xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
        Professional Resume Templates
      </h1>
      <p class="text-base md:text-lg text-slate-600">Choose from our curated collection of ATS-friendly designs</p>
    </div>

    <!-- Filters -->
    <div class="flex flex-wrap justify-center gap-3 mb-10">
      <button data-filter="all" class="filter-btn px-4 py-2 rounded-full border border-slate-300 text-slate-600 text-sm font-medium transition">
        <i class="fas fa-th-list mr-1 text-xs"></i> All Templates
      </button>
      <?php foreach ($template_category as $category): ?>
        <button data-filter="<?= htmlspecialchars($category['category']) ?>"
                class="filter-btn px-4 py-2 rounded-full border border-slate-300 text-slate-600 text-sm font-medium hover:border-blue-500 hover:text-blue-600 transition flex items-center gap-2">
          <i class="fas fa-<?= htmlspecialchars($category['icon'] ?? 'file') ?> text-xs"></i>
          <?= ucfirst(htmlspecialchars($category['category'])) ?>
        </button>
      <?php endforeach; ?>
    </div>
   
	<!-- Templates Grid -->
	<div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-5" id="templates-container">
	  <?php if (!empty($templates)): ?>
		<?php foreach ($templates as $template): ?>
		  <div class="group relative bg-white rounded-xl shadow-md hover:shadow-xl border border-slate-100 overflow-hidden transition"
			   data-category="<?= htmlspecialchars($template['category']) ?>">
			<!-- Image with Standard Balanced Height -->
			<div class="relative h-[370px] w-full overflow-hidden flex items-center justify-center bg-white">
			  <img src="<?= base_url($template['preview_image']) ?>" 
				   alt="<?= htmlspecialchars($template['name']) ?>"
				   class="max-h-full max-w-full object-contain group-hover:scale-105 transition-transform duration-300">

			  <!-- Hover Overlay -->
			  <div class="absolute inset-0 bg-gradient-to-t from-blue-600/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4">
				<div class="w-full space-y-2">
				  <button onclick="selectTemplate('<?= htmlspecialchars($template['template_id']) ?>')" 
						  class="w-full bg-white/90 backdrop-blur-sm text-blue-600 px-3 py-2 rounded-md font-semibold hover:bg-white transition">
					Use Template <i class="fas fa-arrow-right ml-1 text-xs"></i>
				  </button>
				  <div class="flex flex-wrap gap-1 text-xs">
					<span class="bg-white/90 backdrop-blur-sm text-slate-700 px-2 py-1 rounded-full">
					  <?= ucfirst(htmlspecialchars($template['category'])) ?>
					</span>
					<span class="bg-blue-100 text-blue-600 px-2 py-1 rounded-full flex items-center gap-1">
					  <i class="fas fa-star text-yellow-400"></i> 4.8
					</span>
				  </div>
				</div>
			  </div>
			</div>

		  </div>
		<?php endforeach; ?>
	  <?php else: ?>
		<p class="text-center text-slate-600 col-span-full">No templates available at this time.</p>
	  <?php endif; ?>
	</div>


  </div>
</section>
<script>
document.addEventListener('DOMContentLoaded', () => {
  // Set "All Templates" as active initially
  const allBtn = document.querySelector('[data-filter="all"]');
  if (allBtn) allBtn.classList.add('bg-blue-600', 'text-white');

  // Filter Buttons Logic
  document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function () {
      // Reset all buttons
      document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('bg-blue-600', 'text-white'));
      this.classList.add('bg-blue-600', 'text-white');

      // Filter templates
      const filter = this.dataset.filter;
      document.querySelectorAll('#templates-container > div').forEach(card => {
        card.style.display = (filter === 'all' || card.dataset.category === filter) ? 'block' : 'none';
      });
    });
  });
});

// Template Selection
function selectTemplate(templateId) {
  const isLoggedIn = <?= $this->session->userdata('role') === 'candidate' ? 'true' : 'false' ?>;
  const redirectUrl = isLoggedIn
    ? `<?= site_url('resume/') ?>${templateId}/edit`
    : `<?= base_url('app/create-resume?step=introduction') ?>`;

  document.cookie = `template_id=${encodeURIComponent(templateId)}; path=/`;
  window.location.href = redirectUrl;
}
</script>

