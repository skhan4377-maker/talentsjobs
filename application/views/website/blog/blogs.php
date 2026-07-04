<!-- View (application/views/website/blog/blogs.php) -->
<section class="bg-gradient-to-b from-blue-50 to-white pt-20 pb-24 md:pb-16">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-9">
                <?php $this->load->view('common/header_ads_tj');?>
                
                <!-- Search -->
                <div class="mb-8 p-4 bg-white rounded-xl shadow-lg">
                    <script async src="https://cse.google.com/cse.js?cx=804d6a8c4da79baf9"></script>
                    <div class="gcse-search"></div>
                </div>

                <!-- Mobile: 2 Columns, Desktop: 4 Columns -->
                <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                    <?php if(!empty($blogs)) foreach($blogs as $row): ?>
                    <article class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 flex flex-col">
                        <a href="<?=base_url('blog-detail/'.$row['slug'])?>" class="block relative">
                            <img  src="<?= !empty($row['blogs_banner']) 
								  ? base_url('uploads/blogs/' . $row['blogs_banner']) 
								  : base_url('uploads/blogs/noimage.png') ?>" 
                                 class="w-full h-40 object-cover rounded-t-xl"
                                 alt="<?=htmlspecialchars($row['blogs_title'])?>">
                            <div class="absolute top-2 right-2 bg-white/90 px-2 py-1 rounded-full text-xs">
                                <?=date('M d, Y',strtotime($row['created_at']))?>
                            </div>
                        </a>
                        
                        <div class="p-3 flex-1">
                            <div class="flex items-center text-xs text-gray-500 mb-1 space-x-1">
                                <span>By Talents Jobs</span>
                                <span>•</span>
                                <span><?=rand(1000,9999)?> Views</span>
                            </div>
                            
                            <a href="<?=base_url('blog-detail/'.$row['slug'])?>" 
                               class="text-sm font-semibold text-gray-800 hover:text-blue-600 line-clamp-2">
                                <?=htmlspecialchars($row['blogs_title'])?>
                            </a>
                            
                            <p class="mt-1 text-gray-600 text-xs line-clamp-3">
                                <?=strip_tags(ucfirst($row['blogs_content']))?>
                            </p>
                        </div>
                        
                        <div class="px-3 pb-3 mt-auto">
                            <div class="text-xs text-gray-500">
                                <span class="px-2 py-1 bg-gray-100 rounded-full">
                                    <?=htmlspecialchars($row['category_name'])?>
                                </span>
                            </div>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if(!empty($pagination_links)): ?>
                <div class="mt-6">
                    <?=$pagination_links?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-3 space-y-6 mt-4"> <!-- Added top margin -->
                <!-- Popular Categories -->
                <div class="p-3 bg-white rounded-xl shadow-lg">
                    <h3 class="text-base font-bold mb-2">Trending Categories</h3>
                    <ul class="space-y-2">
                        <?php foreach($popular_blogs as $category): ?>
                        <li>
                            <a href="#" class="flex justify-between items-center p-2 hover:bg-gray-50 rounded-lg">
                                <span class="text-sm"><?=htmlspecialchars($category['category_name'])?></span>
                                <span class="text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded-full">
                                    <?=$category['polular_category']?>
                                </span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Recent Posts -->
                <div class="p-3 bg-white rounded-xl shadow-lg">
                    <h3 class="text-base font-bold mb-2">Latest Posts</h3>
                    <div class="space-y-3">
                        <?php foreach($recent_blogs as $post): ?>
                        <article class="flex gap-2">
                            <a href="<?=base_url('blog-detail/'.$post['slug'])?>" class="shrink-0">
                                <img src="<?= !empty($post['blogs_banner']) 
								  ? base_url('uploads/blogs/' . $post['blogs_banner']) 
								  : base_url('uploads/blogs/noimage.png') ?>" 
                                     class="w-14 h-14 object-cover rounded-lg"
                                     alt="<?=htmlspecialchars($post['blogs_title'])?>">
                            </a>
                            <div>
                                <a href="<?=base_url('blog-detail/'.$post['slug'])?>" 
                                   class="text-xs font-medium hover:text-blue-600 line-clamp-2">
                                    <?=htmlspecialchars($post['blogs_title'])?>
                                </a>
                                <p class="text-[0.7rem] text-gray-500 mt-1">
                                    <?=date('M d, Y',strtotime($post['created_at']))?>
                                </p>
                            </div>
                        </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-153460368-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-153460368-1');
</script>
