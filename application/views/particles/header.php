<!doctype html>
<html lang="en">
   <head>
    <meta charset="utf-8"/>
		<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>

		<meta name="csrf-token" content="<?= $this->security->get_csrf_hash(); ?>">
		<meta name="csrf-name" content="<?= $this->security->get_csrf_token_name(); ?>">

		<title><?= isset($title) ? html_escape($title) : 'My Website' ?></title>

		<meta name="description" content="<?= isset($description) ? html_escape($description) : 'Find latest jobs and updates here.' ?>" />

		<meta name="keywords" content="<?= isset($meta_keywords) ? html_escape($meta_keywords) : '' ?>">
	  <!-- Canonical Link -->
	  <?php if (!empty($canonical)): ?>
		<link rel="canonical" href="<?= htmlspecialchars($canonical, ENT_QUOTES, 'UTF-8') ?>" />
	  <?php endif; ?>

      <!-- CSS==================================================-->
      <link rel="shortcut icon" type="image/png" href="<?=base_url('assets/frontend/favicon.ico');?>" />
      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.17.0/dist/jquery.validate.min.js"></script>
      <script src="https://cdn.jsdelivr.net/jquery.validation/1.15.0/additional-methods.min.js"></script>
     <!--<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>-->	
      <script src="<?=base_url('assets/frontend/js/jquery-ui.min.js')?>"></script> 
      <script src="<?=base_url('assets/frontend/js/bootstrap-3.4.16.js')?>"></script> 
      
      <!-- Include Alpine.js for handling the mobile toggle -->
      <script src="https://unpkg.com/alpinejs@3.12.0/dist/cdn.min.js" defer></script>
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
        
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"/>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
	 
	 	<!-- Keep only these -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
	<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
	<style>
         [x-cloak] { 
			display: none !important; 
         }
		 
		  /* GLOBAL FIX */
			html, body {
				width: 100%;
				max-width: 100%;
				overflow-x: hidden;
			}

			/* ADS CONTAINER */
			#afscontainer1,
			#afscontainer2 {
				width: 100%;
				max-width: 100%;
				overflow: hidden;
				display: block;
				position: relative;
			}

			/* ADS IFRAME FIX */
			#afscontainer1 iframe,
			#afscontainer2 iframe {
				width: 100% !important;
				max-width: 100% !important;
				min-width: 0 !important; 
				display: block !important;
				border: none;
			}
      </style>
		
	<!-- Global Configuration (must come after auto-complete-widget but before app.js) -->
    <script>
        const BASE_URL = '<?= base_url() ?>';

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

        window.TalentsJobsConfig = {
            baseUrl: BASE_URL,
            isLoggedIn: <?= $this->session->userdata('logged_in') ? 'true' : 'false' ?>,
            urls: {
                browseJobs: BASE_URL + 'browse-jobs/',
                getJobCities: BASE_URL + 'Common/get_job_cities',
                getSearchData: BASE_URL + 'Common/get_search_data',
                notifications: BASE_URL + 'notify/notification/get_notifications',
                markRead: BASE_URL + 'notify/notification/mark_read_ajax'
            }
        };
    </script>

    <!-- Main application script (defer – runs after HTML parsed, but after auto-complete-widget) -->
    <script src="<?= base_url('assets/frontend/js/main.js') ?>" defer></script>
	<script src="<?= base_url('assets/frontend/js/auto-complete-widget.js') ?>" defer></script>

	
	  <!-- Global site tag (gtag.js) - Google Analytics -->
	  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-153460368-1"></script>
	  <script>
		 window.dataLayer = window.dataLayer || [];
		  function gtag(){dataLayer.push(arguments);}
		  gtag('js', new Date());

		  gtag('config', 'UA-153460368-1');
		</script>
		
    <script type="application/ld+json">
	{
	  "@context": "http://schema.org",
	  "@type": "Organization",
	  "name": "Talents Jobs",
	  "url": <?= json_encode(SITE_URL) ?>,
	  "logo": <?= json_encode(base_url('assets/frontend/logo.png')) ?>,
	  "email": <?= json_encode(SITE_EMAIL ?: 'info@talentsjobs.in') ?>,
	  "description": "Talents Jobs is a trusted job portal in India helping employers and job seekers connect across all industries. Discover the best talent or your next career opportunity today.",
	  "location": {
		"@type": "Place",
		"address": {
		  "@type": "PostalAddress",
		  "streetAddress": "Sector - 10",
		  "addressLocality": "Delhi",
		  "addressRegion": "Delhi",
		  "postalCode": "110085",
		  "addressCountry": "IN"
		}
	  },
	  "hasOfferCatalog": {
		"@type": "OfferCatalog",
		"name": "Employer Services",
		"itemListElement": [
		  {
			"@type": "Offer",
			"itemOffered": {
			  "@type": "Service",
			  "name": "Unlimited Free Job Posting",
			  "description": "Employers can post unlimited jobs at no cost on Talents Jobs portal."
			},
			"price": "0.00",
			"priceCurrency": "INR"
		  }
		]
	  }
	}
	</script>
	
	<script>
    window.addEventListener("load", function () {
        const loader = document.getElementById("pageLoader");
    
        setTimeout(() => {
            loader.style.opacity = "0";
            loader.style.transition = "opacity 0.4s ease";
    
            setTimeout(() => {
                loader.style.display = "none";
            }, 400);
    
        }, 300); // thoda delay for smooth feel
    });
    </script>
	
	<script>
	function updateCartCount() {

		fetch('<?= base_url("cart/count") ?>')
		.then(res => res.json())
		.then(res => {
			if (res.csrf_token && res.csrf_name) {
				updateCSRFToken(res.csrf_token, res.csrf_name);
			}
			if (!res.status) return;

			const count = res.count || 0;

			const desktop = document.getElementById('cart-count');
			const mobile = document.getElementById('cart-count-mobile');

			if (desktop) {
				desktop.innerText = count;
				desktop.style.display = count > 0 ? 'flex' : 'none';
			}

			if (mobile) {
				mobile.innerText = count;
				mobile.style.display = count > 0 ? 'inline-flex' : 'none';
			}
		});
	}

	// ✅ page load par auto run
	document.addEventListener('DOMContentLoaded', updateCartCount);
	</script>

</head>
<body class="bg-gray-50">
<!-- 🔥 Page Loader -->
<div id="pageLoader" class="fixed inset-0 bg-white flex items-center justify-center z-[9999]">
    
    <div class="text-center">
        <!-- Logo -->
        <img src="<?=base_url('assets/frontend/logo.png')?>" 
             class="h-16 mx-auto mb-4 animate-pulse">
        <!-- Spinner -->
        <div class="mt-4 w-8 h-8 border-4 border-blue-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
    </div>

</div>