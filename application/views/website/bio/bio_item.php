<!-- AdSense Script (ONLY ONCE in page, preferably in <head>) -->
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9268075008862469"
     crossorigin="anonymous"></script>

<div class="w-full">
    <div class="flex">
        
        <div class="w-full">

            <?php 
            if (!empty($bio->external_url)) {
                $link = htmlspecialchars($bio->external_url);
                $target = 'target="_blank" rel="noopener noreferrer"';
            } else {
                $link = site_url('bio/' . $bio->slug);
                $target = '';
            }
            ?>

           <div style="margin-bottom:20px; clear:both;">
                <ins class="adsbygoogle"
                     style="display:block"
                     data-ad-client="ca-pub-9268075008862469"
                     data-ad-slot="6220165203"
                     data-ad-format="auto"
                     data-full-width-responsive="true"></ins>
            </div>
            
            <script>
                (adsbygoogle = window.adsbygoogle || []).push({});
            </script>


            <!-- APPLY BUTTON -->
            <a href="<?= $link ?>" <?= $target ?>
               class="block w-full text-center font-semibold bg-blue-600 hover:bg-blue-700 text-white py-4 px-6 rounded-[15px] transition duration-300 shadow">
                <strong>
                    <?= htmlspecialchars($bio->title ?? 'Untitled') ?> – Apply Now
                </strong>
            </a>


        </div>
    </div>
</div>