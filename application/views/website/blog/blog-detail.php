<?php $title = $read_blog['blogs_title']; $blog_title = preg_replace('/[^a-zA-Z0-9_ -+]/s',' ',$title);?>         

<section class="bg-gradient-to-b from-blue-50 to-white pt-20 pb-24 md:pb-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-8">
                <article class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    <figure class="relative">
                        <img  src="<?= !empty($read_blog['blogs_banner']) 
						  ? base_url('uploads/blogs/' . $read_blog['blogs_banner']) 
						  : base_url('uploads/blogs/noimage.png') ?>" 
                             class="w-full h-96 object-cover"
                             alt="<?=htmlspecialchars($read_blog['blogs_title'])?>">
                        <div class="absolute bottom-4 right-4 bg-white/90 px-4 py-2 rounded-full text-sm font-medium shadow-sm">
                            <?=date('M d, Y',strtotime($read_blog['created_at']))?>
                        </div>
                    </figure>

                    <div class="p-8">
                        <div class="flex flex-wrap gap-4 text-sm text-gray-500 mb-6">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <?=SITE_NAME?>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <?=date('M d, Y')?>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <?=rand(10000,99999)?> Views
                            </div>
                        </div>

                        <h1 class="text-4xl font-bold text-gray-900 mb-6"><?=ucfirst($read_blog['blogs_title'])?></h1>
                        
                        <div class="prose max-w-none text-gray-600 mb-8">
                            <?=ucfirst($read_blog['blogs_content'])?>
                        </div>

                        <div class="flex flex-wrap gap-2 mb-8">
                            <?php $tags = explode(',', $read_blog['blogs_tags']); ?>
                            <?php foreach($tags as $tag): ?>
                                <span class="px-3 py-1 bg-blue-100 text-blue-600 rounded-full text-sm"><?=trim($tag)?></span>
                            <?php endforeach; ?>
                        </div>

                        <div class="border-t pt-6">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Share this post:</span>
                                <div class="flex space-x-4">
                                    <?php $share_link = base_url($_SERVER['REQUEST_URI']); ?>
                                    <a href="https://api.whatsapp.com/send?text=<?=urlencode($share_link)?>" 
                                       class="text-green-600 hover:text-green-700 transition-colors">
                                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-4 space-y-8">
                <!-- Popular Categories -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Popular Categories</h3>
                    <ul class="space-y-3">
                        <?php foreach($popular_blogs as $category): ?>
                        <li>
                            <a href="#" class="flex justify-between items-center p-3 rounded-lg hover:bg-gray-50 transition-colors">
                                <span class="text-gray-700"><?=$category['category_name']?></span>
                                <span class="px-2 py-1 bg-blue-100 text-blue-600 rounded-full text-sm">
                                    <?=$category['polular_category']?>
                                </span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Recent Posts -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Recent Posts</h3>
                    <div class="space-y-4">
                        <?php foreach($recent_blogs as $post): ?>
                        <article class="flex gap-4">
                            <a href="<?=base_url('blog-detail/'.$post['slug'])?>" class="shrink-0">
                                <img src="<?= !empty($post['blogs_banner']) 
						  ? base_url('uploads/blogs/' . $post['blogs_banner']) 
						  : base_url('uploads/blogs/noimage.png') ?>"
                                     class="w-20 h-20 object-cover rounded-lg"
                                     alt="<?=htmlspecialchars($post['blogs_title'])?>">
                            </a>
                            <div>
                                <a href="<?=base_url('blog-detail/'.$post['slug'])?>" 
                                   class="font-medium text-gray-900 hover:text-blue-600 line-clamp-2">
                                    <?=htmlspecialchars($post['blogs_title'])?>
                                </a>
                                <p class="text-sm text-gray-500 mt-1">
                                    <?=date('M d, Y',strtotime($post['created_at']))?>
                                </p>
                            </div>
                        </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Jobs You May Like -->
        <!--<div class="mt-12">
            <h3 class="text-2xl font-bold text-gray-900 mb-6">Jobs You May Like</h3>
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-1">
                <?php //foreach ($mightBeLike as $job): ?>
				<?php //$cities = !empty($job['job_locations']) ?  explode(', ', $job['job_locations']) : []; ?>
				<?php
					// Assume $job is your post_details for this row

					// 1. Extract raw values
					//$salaryTypeRaw  = $job['salary_type'];    // e.g. "per-month"
				//	$salaryRangeRaw = $job['salary_range'];   // e.g. "9500.00 - 60000.00"

					// 2. Split, trim, and format numbers
				//	list($minRaw, $maxRaw) = array_map('trim', explode('-', $salaryRangeRaw));
				//	$minFormatted = '₹ ' . number_format((float)$minRaw, 0);
				//	$maxFormatted = '₹ ' . number_format((float)$maxRaw, 0);

					// 3. Clean up salary type
				//	$salaryTypeClean = str_replace('-', ' ', strtolower($salaryTypeRaw));
				//	$salaryTypeClean = ucwords($salaryTypeClean);   // "Per Month"

					// 4. Build final string
				//	$job['formatted_salary'] = "{$minFormatted} – {$maxFormatted} {$salaryTypeClean}";
					?>
								
                <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow p-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="flex-1">
                            <a href="<?//= base_url('job-detail?job-id=' . $job['job_id'])?>" 
                               class="text-xl font-semibold text-gray-900 hover:text-blue-600">
                                <?//= ucfirst(preg_replace('/[^A-Za-z0-9\s]/', '', $job['job_title'])) ?>
                            </a>
                            <div class="mt-2 text-sm text-gray-500">
                                <span class="mr-4"><?//= ucfirst($job['company_name']) ?></span>
                                <?php //foreach($cities as $city): ?>
									<span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">
										<?//= ucfirst(trim($city)) ?>
									</span>
								<?php //endforeach; ?>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                          <div class="text-right">
								<p class="text-sm text-gray-600"><?//= htmlspecialchars($job['experience_range']) ?> years exp</p>
								<p class="text-sm font-medium text-blue-600"><?//= htmlspecialchars($job['formatted_salary']) ?></p>
							</div>

                            <a href="<?//= base_url('job-detail?job-id=' . $job['job_id'])?>" 
                               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                Apply
                            </a>
                        </div>
                    </div>
                </div>
                <?php //endforeach; ?>
            </div>
        </div>-->
    
	</div>

    <!-- Floating Job Widget -->
    <a href="<?=base_url('browse-jobs')?>" target="_blank" rel="noopener" 
       class="fixed bottom-8 right-8 flex items-center bg-blue-600 text-white px-6 py-3 rounded-full shadow-xl hover:shadow-2xl transition-all duration-300">
        <img src="<?=base_url('assets/frontend/favicon.ico')?>" 
             class="w-10 h-10 bg-white p-1 rounded-full shadow-sm">
        <span class="ml-3 font-medium">Looking for a job?</span>
    </a>
</section>


<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-153460368-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-153460368-1');
</script>
