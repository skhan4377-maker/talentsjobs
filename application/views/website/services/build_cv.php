<section class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-20 px-4">
    <div class="max-w-6xl mx-auto">
        <!-- Heading Section -->
        <div class="text-center mb-16">
            <h1 class="text-5xl font-bold text-gray-900 mb-6">
                Create a 
                <span class="bg-gradient-to-r from-blue-600 to-indigo-600 text-transparent bg-clip-text">
                    job-winning
                </span> 
                CV in minutes
            </h1>
            <p class="text-xl text-gray-600">Get hired faster with our AI-powered resume builder</p>
        </div>

        <!-- Steps Container -->
        <div class="grid md:grid-cols-3 gap-8 mb-16">
            <!-- Step 1 -->
            <div class="bg-white p-8 rounded-2xl shadow-lg transition-all duration-300 hover:shadow-xl hover:-translate-y-2">
                <div class="flex items-center mb-6">
                    <div class="relative w-16 h-16 flex items-center justify-center">
                        <div class="absolute inset-0 bg-blue-100 rounded-full transform rotate-45"></div>
                        <span class="text-2xl font-bold text-blue-600 relative z-10">1</span>
                    </div>
                </div>
                <h3 class="text-xl font-semibold mb-4">Choose Professional Template</h3>
                <p class="text-gray-600">Select from 20+ modern, ATS-friendly templates</p>
            </div>

            <!-- Step 2 -->
            <div class="bg-white p-8 rounded-2xl shadow-lg transition-all duration-300 hover:shadow-xl hover:-translate-y-2">
                <div class="flex items-center mb-6">
                    <div class="relative w-16 h-16 flex items-center justify-center">
                        <div class="absolute inset-0 bg-indigo-100 rounded-full transform rotate-45"></div>
                        <span class="text-2xl font-bold text-indigo-600 relative z-10">2</span>
                    </div>
                </div>
                <h3 class="text-xl font-semibold mb-4">Smart Content Suggestions</h3>
                <p class="text-gray-600">AI-powered examples for any job role</p>
            </div>

            <!-- Step 3 -->
            <div class="bg-white p-8 rounded-2xl shadow-lg transition-all duration-300 hover:shadow-xl hover:-translate-y-2">
                <div class="flex items-center mb-6">
                    <div class="relative w-16 h-16 flex items-center justify-center">
                        <div class="absolute inset-0 bg-purple-100 rounded-full transform rotate-45"></div>
                        <span class="text-2xl font-bold text-purple-600 relative z-10">3</span>
                    </div>
                </div>
                <h3 class="text-xl font-semibold mb-4">Download & Apply</h3>
                <p class="text-gray-600">PDF, Word, or direct online applications</p>
            </div>
        </div>

        <!-- CTA Button -->
        <div class="text-center">
            <button class="lets-go-btn bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-12 py-4 rounded-xl text-lg font-semibold hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 hover:shadow-2xl hover:scale-105 transform">
                Start Building Now →
            </button>
        </div>
    </div>
</section>

<script>
document.querySelector('.lets-go-btn').addEventListener('click', function(event) {
    event.preventDefault();
    window.location.href = "<?=base_url('app/choose-template')?>";
});
</script>