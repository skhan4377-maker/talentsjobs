<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	 <meta name="csrf-token" content="<?= $this->security->get_csrf_hash(); ?>">
     <meta name="csrf-name" content="<?= $this->security->get_csrf_token_name(); ?>">
    <title><?php echo @$title; ?></title>
 
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
	<!-- Include jQuery from a CDN -->
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="<?=base_url('assets/frontend/js/bootstrap-3.4.16.js')?>"></script> 
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
	
	<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script> 
	
	<!-- Include DataTables -->
	<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
	<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
	<script src="https://cdn.tiny.cloud/1/jxhyjhicc4somdh05bjumsfnalcuzz4uej01mbbbizec4fov/tinymce/5/tinymce.min.js"></script>
	<!-- Loader Styles -->
	
	<!-- Keep only these -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
	<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
		

	<!-- master.php के <head> में या <body> के तुरंत बाद -->
	
	<script>
	    const BASE_URL = '<?= base_url() ?>';

		// Helper functions to get/set CSRF token from meta tags
		function getCSRFToken() {
			return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
		}

		function getCSRFName() {
			return document.querySelector('meta[name="csrf-name"]').getAttribute('content');
		}

		function updateCSRFToken(token, name) {
			document.querySelector('meta[name="csrf-token"]').setAttribute('content', token);
			document.querySelector('meta[name="csrf-name"]').setAttribute('content', name);
		}
	</script>

	<script src="<?= base_url('assets/frontend/js/custom.js') ?>"></script>

<style>
  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
  .loader-spinner {
    border-width: 2px;
    border-style: solid;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
  }

  input[type="checkbox"]:checked {
    @apply bg-blue-600 border-transparent;
  }

  tr:hover { background-color: #f9fafb; }


/* ✅ Alpine.js cloaking के लिए */
[x-cloak] { 
    display: none !important; 
}

/* ✅ Smooth transitions */
[class*="submenu"] {
    transition: all 0.3s ease-in-out;
}
</style>

<style>
    /* Smooth transitions */
    #mobileMenu {
        transition: opacity 0.3s ease;
    }
    #mobileMenu div {
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

/* Smooth transitions for submenus */
[x-collapse] {
  overflow: hidden;
  transition: all 0.3s ease-in-out;
}

/* Right slide animation */
.translate-x-full {
  transform: translateX(100%);
}
.translate-x-0 {
  transform: translateX(0);
}

/* Chevron rotation */
.rotate-90 {
  transform: rotate(90deg);
}
</style>


<style>
    .swiper-pagination-bullet { background: #CBD5E1 !important; opacity: 1 !important; }
    .swiper-pagination-bullet-active { background: #3B82F6 !important; }
    .swiper-slide { height: auto; }
    .companies-slider .swiper-slide { width: 300px; }
</style>
</head>

<body class="bg-gray-50" x-data="{ mobileFiltersOpen: false }">
    <!-- Sidebar -->
    <?php $this->load->view('templates/sidebar'); ?>
     
    <!-- Page Content -->
    <main class="lg:ml-64 p-4 lg:p-8">
        <!-- Desktop Header & Mobile Header -->
        <?php $this->load->view('templates/desktop_header'); ?>   
		
			<!-- Eye-catching Resume Builder Ad -->
		<?php if ($this->session->userdata('role') === 'candidate'): ?>
			<?php //$loginToken = $this->session->userdata('login_token'); ?>
			<div id="resumeAd" class="mb-6 transition-all duration-300">
				<div class="relative overflow-hidden bg-gradient-to-r from-blue-500 via-blue-400 to-indigo-500 rounded-xl shadow-lg transform hover:scale-[1.005] transition-transform duration-300">
					<!-- Background pattern -->
					<div class="absolute inset-0 opacity-10">
						<div class="absolute top-0 left-0 w-20 h-20 bg-white rounded-full -translate-x-10 -translate-y-10"></div>
						<div class="absolute bottom-0 right-0 w-32 h-32 bg-white rounded-full translate-x-16 translate-y-16"></div>
					</div>
					
					<div class="relative p-4">
						<div class="flex flex-col md:flex-row items-center justify-between gap-4">
							<!-- Left: Icon and Main Message -->
							<div class="flex items-center gap-4">
								<!-- Animated Icon -->
								<div class="relative">
									<div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center border border-white/30 shadow-lg">
										<i class="fas fa-file-alt text-white text-xl"></i>
									</div>
									<div class="absolute -top-2 -right-2 w-5 h-5 bg-gradient-to-r from-green-400 to-emerald-500 rounded-full flex items-center justify-center shadow-lg animate-bounce">
										<i class="fas fa-star text-white text-xs"></i>
									</div>
								</div>
								
								<!-- Text Content -->
								<div class="text-white">
									<h4 class="text-base font-bold mb-1 flex items-center gap-2">
										<span>Your Resume Needs an Upgrade</span>
									</h4>
									<p class="text-xs opacity-90">Professional resumes get 3x more interview calls</p>
								</div>
							</div>
							
							<!-- Right: Stats and CTA -->
							<div class="flex items-center gap-4">
								<!-- CTA Button -->
								<div class="flex items-center gap-3">
									<a href="<?=base_url('resume-templates')?>"								
									   class="group bg-white hover:bg-gray-100 text-blue-600 font-semibold px-4 py-2 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-0.5 flex items-center gap-2 text-sm">
										<i class="fas fa-play-circle"></i>
										<span>Start Building</span>
									</a>                                
									<button onclick="hideAd()" class="text-white/70 hover:text-white w-7 h-7 rounded-full hover:bg-white/20 flex items-center justify-center transition-colors duration-200">
										<i class="fas fa-times text-sm"></i>
									</button>
								</div>
							</div>
						</div>
						
						<!-- Bottom Bar -->
						<div class="mt-3 pt-3 border-t border-white/20">
							<div class="flex items-center justify-between text-white/80 text-xs">
								<div class="flex items-center gap-3">
									<div class="flex items-center gap-1">
										<i class="fas fa-check-circle text-green-300"></i>
										<span>Free forever</span>
									</div>
									<div class="flex items-center gap-1">
										<i class="fas fa-check-circle text-green-300"></i>
										<span>No credit card</span>
									</div>
									<div class="flex items-center gap-1">
										<i class="fas fa-check-circle text-green-300"></i>
										<span>50+ templates</span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>
		
        <?php echo $content; ?>		
    </main>    
    <!-- Mobile Bottom Navigation -->
    <?php $this->load->view('templates/mobile_navigation'); ?>
    
    <!-- Footer -->
    <?php $this->load->view('templates/footer'); ?>
    
   
</body>
</html>