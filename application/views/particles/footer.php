<!-- Firebase CDN -->
<script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-messaging-compat.js"></script>

<!-- External JS -->
<script src="<?= base_url('assets/frontend/js/firebase-init.js') ?>"></script>

<footer class="bg-gray-800 text-gray-200 py-12">
  <div class="container mx-auto px-4">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
      <!-- Company Info -->
      <div>
        <img src="<?=base_url('assets/frontend/logo.png')?>" alt="TalentsJobs Logo" class="h-10 mb-4">
        <p class="text-gray-400 text-sm">
          TalentsJobs connects job seekers with their dream careers across Asia &amp; the Gulf.
          We bring top companies and skilled professionals together.
        </p>
      </div>
      <!-- Company Links -->
      <div>
        <h4 class="text-white font-semibold mb-4">Company</h4>
        <ul class="space-y-2 text-sm">
          <li><a href="<?= base_url('about-us') ?>" class="hover:text-white">About Us</a></li>
          <li><a href="#" class="hover:text-white">Careers</a></li>
          <li><a href="<?=base_url('blogs')?>" class="hover:text-white">Blog</a></li>
          <li><a href="#" class="hover:text-white">Press</a></li>
        </ul>
      </div>
      <!-- Support Links -->
      <div>
        <h4 class="text-white font-semibold mb-4">Support</h4>
        <ul class="space-y-2 text-sm">
            <li><a href="<?= base_url('help-center'); ?>" class="hover:text-white">Help Center</a></li>
            <li><a href="<?= base_url('contact-us'); ?>" class="hover:text-white">Contact Us</a></li>
            <li><a href="<?= base_url('privacy-policy'); ?>" class="hover:text-white">Privacy Policy</a></li>
            <li><a href="<?= base_url('terms-of-service'); ?>" class="hover:text-white">Terms of Service</a></li>
        </ul>
      </div>

      <!-- Social & Newsletter -->
      <div>
        <h4 class="text-white font-semibold mb-4">Follow Us</h4>
        <div class="flex space-x-4 text-lg">
          <a href="#" class="hover:text-white"><i class="fab fa-facebook-f"></i></a>
          <a href="#" class="hover:text-white"><i class="fab fa-twitter"></i></a>
          <a href="#" class="hover:text-white"><i class="fab fa-linkedin-in"></i></a>
          <a href="#" class="hover:text-white"><i class="fab fa-instagram"></i></a>
        </div>
        <div class="mt-6">
          <h4 class="text-white font-semibold mb-2 text-sm">Subscribe to our Newsletter</h4>
          <form class="flex">
            <input type="email" placeholder="Enter your email" class="w-full px-3 py-2 rounded-l bg-gray-700 text-gray-200 text-sm focus:outline-none" />
            <button type="submit" class="px-4 py-2 bg-orange-500 text-white rounded-r text-sm hover:bg-orange-600">Subscribe</button>
          </form>
        </div>
      </div>
    </div>
   <div class="mt-12 border-t border-gray-700 pt-4 text-center">
      <p class="text-gray-500 text-xs">
        &copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.
      </p>
    </div>
  </div>
</footer>
</body>
</html>