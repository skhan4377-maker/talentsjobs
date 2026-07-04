
	<div class="bg-white p-4 sm:p-6 mb-4 rounded-2xl shadow border">

    <!-- Header and Add Button -->
	<div class="flex justify-between items-center mb-6">
		<h2 class="text-lg font-semibold text-gray-800">Blogs</h2>
            <button onclick="openModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow transition-all">
				+ Add New Blog
			</button>
	
	</div>


    <!-- Blog List -->
    <div id="blogList" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <?php if (!empty($blogs)) : ?>
            <?php foreach ($blogs as $blog) : ?>
                <div id="blog-<?= $blog['id'] ?>" class="bg-white border rounded-xl shadow hover:shadow-lg transition p-4 flex flex-col">
                    <img src="<?= base_url('uploads/blogs/' . (!empty($blog['blogs_banner']) ? $blog['blogs_banner'] : 'noimage.png')) ?>" 
					 alt="Blog Banner" 
					 class="rounded-lg h-40 w-full object-cover mb-3">

                    <h3 class="text-lg font-bold text-gray-800 mb-1"><?= htmlspecialchars($blog['blogs_title']); ?></h3>
					<p class="text-xs text-gray-500 mb-2">
						Posted on <?= date('d M Y', strtotime($blog['created_at'])) ?>
					</p>

                    <p class="text-sm text-gray-600 mb-3"><?= strip_tags(substr($blog['blogs_content'], 0, 100)); ?>...</p>
                    <div class="flex flex-wrap gap-2 mb-3">
                        <span class="text-xs bg-blue-100 text-blue-800 px-3 py-1 rounded-full"><?= htmlspecialchars($blog['blogs_category_name']); ?></span>
                        <?php foreach (explode(',', $blog['blogs_tags']) as $tag) : ?>
                            <span class="text-xs bg-gray-100 text-gray-700 px-3 py-1 rounded-full"><?= htmlspecialchars(trim($tag)); ?></span>
                        <?php endforeach; ?>
                    </div>
					
                    <div class="flex justify-between items-center mt-auto pt-2 border-t">
						<span class="text-sm font-medium <?= $blog['blogs_status'] ? 'text-green-600' : 'text-red-600' ?>">
							<?= $blog['blogs_status'] ? 'Published' : 'Draft' ?>
						</span>
						<div class="flex gap-3">
							
								<button onclick="openModalForEdit(<?= $blog['id'] ?>)" class="text-blue-500 hover:text-blue-700" title="Edit">
									<i class="fas fa-edit"></i>
								</button>
								<button onclick="deleteBlog(<?= $blog['id'] ?>)" class="text-red-500 hover:text-red-700" title="Delete">
									<i class="fas fa-trash"></i>
								</button>
						
						</div>
					</div>

					
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="col-span-full text-center py-10 text-gray-500 text-lg">No blogs found. Start by creating a new blog!</div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <div class="mt-8 flex justify-center">
        <?= $pagination_links ?>
    </div>

	</div>

<!-- Blog Modal -->
<div id="blogModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-[9999] flex items-center justify-center">
    <div class="bg-white w-full max-w-3xl max-h-screen overflow-hidden flex flex-col">
        <form id="blogForm" class="flex flex-col h-full overflow-y-auto p-6" enctype="multipart/form-data">
            <!-- CSRF Token -->
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
            
            <!-- Header -->
            <div class="flex justify-between items-center mb-4 border-b pb-3 sticky top-0 bg-white z-10">
                <h3 class="text-2xl font-semibold text-gray-800">Create New Blog</h3>
                <button type="button" onclick="closeModal()" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
            </div>
            
            <input type="hidden" id="blog_id" name="blog_id">

            <div class="flex-1 overflow-y-auto space-y-6">
                <!-- Category & Status -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                            id="blogs_category" name="blogs_category" required>
                            <option value="">Select Category</option>
                            <?php foreach ($blogs_category as $row) : ?>
                                <option value="<?= $row['id'] ?>"><?= $row['category_name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            id="blogs_status" name="blogs_status">
                            <option value="1">Published</option>
                            <option value="0">Draft</option>
                        </select>
                    </div>
                </div>

                <!-- Title -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                    <input type="text" id="blogs_title" name="blogs_title" 
                        class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                        placeholder="Enter blog title" required>
                </div>

                <!-- Content -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Content</label>
                    <textarea id="blogs_content" name="blogs_content" 
                        class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                        rows="6" placeholder="Write your blog content..." required></textarea>
                </div>

                <!-- Tags -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tags (comma separated)</label>
                    <input type="text" id="blogs_tags" name="blogs_tags" 
                        class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                        placeholder="e.g., technology, web development, design">
                </div>

                <!-- Banner Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Featured Image</label>
                    <div class="flex items-center justify-center w-full">
                        <label class="flex flex-col w-full h-32 border-4 border-dashed rounded-lg hover:border-gray-400 transition-colors cursor-pointer">
                            <div class="flex flex-col items-center justify-center pt-7" id="uploadContainer">
                                <svg class="w-12 h-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <p class="pt-1 text-sm tracking-wider text-gray-600">Click to upload</p>
                            </div>
                            <input type="file" id="blogs_banner" name="blogs_banner" accept="image/*" class="opacity-0" 
                                onchange="previewImage(event)">
                        </label>
                    </div>
                    <div id="bannerPreview" class="mt-4 hidden">
                        <img id="bannerImage" class="w-full h-48 object-cover rounded-lg border">
                    </div>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="sticky bottom-0 bg-white py-4 border-t flex justify-end space-x-4">
                <button type="button" onclick="closeModal()" 
                    class="px-6 py-2.5 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors">
                    Cancel
                </button>
                <button type="submit" id="submitBtn" 
                    class="px-8 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Publish
                </button>
            </div>
        </form>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.16.2/ckeditor.js"></script>

<script>
/* -----------------------------------------------------
   GLOBAL CSRF PROTECTION (AUTO ATTACH + AUTO REFRESH)
------------------------------------------------------*/
const csrfName = "<?= $this->security->get_csrf_token_name(); ?>";
let csrfHash  = "<?= $this->security->get_csrf_hash(); ?>";

// Auto include CSRF token in every AJAX POST request
$.ajaxSetup({
    beforeSend: function(xhr, settings) {
        if (settings.type === 'POST') {
            if (settings.data instanceof FormData) {
                settings.data.append(csrfName, csrfHash);
            } else {
                settings.data = (settings.data ? settings.data + '&' : '') 
                                + csrfName + "=" + csrfHash;
            }
        }
    }
});

// Function to update CSRF Token (called after every AJAX)
function updateCsrf(res) {
    if (res.csrf_token) {
        csrfHash = res.csrf_token.hash;
        $("input[name='" + res.csrf_token.name + "']").val(res.csrf_token.hash);
    }
}

/* -----------------------------------------------------
   CKEDITOR INITIALIZE
------------------------------------------------------*/
CKEDITOR.replace("blogs_content");

/* -----------------------------------------------------
   OPEN MODAL
------------------------------------------------------*/
function openModal(blog = null) {
    if (blog) {
        $("#blog_id").val(blog.id);
        $("#blogs_category").val(blog.blogs_category);
        $("#blogs_title").val(blog.blogs_title);
        $("#blogs_tags").val(blog.blogs_tags);
        $("#blogs_status").val(blog.blogs_status);
        CKEDITOR.instances.blogs_content.setData(blog.blogs_content);

        if (blog.blogs_banner) {
            $("#bannerPreview").removeClass("hidden");
            $("#bannerImage").attr("src", `<?= base_url('uploads/blogs/') ?>${blog.blogs_banner}`);
        }
    } else {
        $("#blogForm")[0].reset();
        CKEDITOR.instances.blogs_content.setData("");
        $("#bannerPreview").addClass("hidden");
    }

    $("#blogModal").removeClass("hidden").addClass("flex");
}

/* -----------------------------------------------------
   CLOSE MODAL
------------------------------------------------------*/
function closeModal() {
    $("#blogModal").addClass("hidden").removeClass("flex");
    $("#blogForm")[0].reset();
    $("#bannerPreview").addClass("hidden");
}

/* -----------------------------------------------------
   IMAGE PREVIEW
------------------------------------------------------*/
function previewImage(event) {
    const [file] = event.target.files;
    if (file) {
        $("#bannerPreview").removeClass("hidden");
        $("#bannerImage").attr("src", URL.createObjectURL(file));
    }
}

/* -----------------------------------------------------
   LOAD BLOG FOR EDIT
------------------------------------------------------*/
function openModalForEdit(blogId) {
    $.ajax({
        url: "<?= base_url('admin/blogs/BlogController/get_blog/') ?>" + blogId,
        method: "GET",
        success: function(response) {
            const blog = JSON.parse(response);
            openModal(blog);
        }
    });
}

/* -----------------------------------------------------
   DELETE BLOG (AJAX + CSRF AUTO REFRESH)
------------------------------------------------------*/
function deleteBlog(id) {
    if (!confirm('Are you sure you want to delete this blog?')) return;

    $.ajax({
        url: "<?= base_url('admin/blogs/BlogController/delete/') ?>" + id,
        type: "POST",
        data: {
            [csrfName]: csrfHash   // <-- SEND CSRF TOKEN HERE
        },
        success: function(response) {
            const res = JSON.parse(response);

            // Update CSRF Token
            updateCsrf(res);

            if (res.status === "success") {
                $("#blog-" + id).fadeOut(300, function() { $(this).remove(); });
            } else {
                alert(res.message || "Delete error");
            }
        },
        error: function() {
            alert("CSRF Error: Delete request blocked.");
        }
    });
}


/* -----------------------------------------------------
   SAVE / UPDATE BLOG (AJAX + CSRF AUTO REFRESH)
------------------------------------------------------*/
$(document).ready(() => {
    $("#blogForm").validate({
        submitHandler: (form) => {
            const formData = new FormData(form);
            formData.append("blogs_content", CKEDITOR.instances.blogs_content.getData());

            $("#submitBtn").prop("disabled", true).html(`
                <div class="flex items-center justify-center space-x-2">
                    <div class="w-4 h-4 border-2 border-t-transparent border-white rounded-full animate-spin"></div>
                    <span>Saving...</span>
                </div>
            `);

            $.ajax({
                url: "<?= base_url('admin/blogs/BlogController/save_edit_blog') ?>",
                method: "POST",
                data: formData,
                contentType: false,
                processData: false,

                success: (response) => {
                    const res = JSON.parse(response);

                    updateCsrf(res); // Update token here

                    if (res.status === "success") {
                        alert(res.message);
                        location.reload();
                    } else {
                        alert(res.message);
                    }
                },
                error: () => {
                    alert("An error occurred while saving the blog.");
                },
                complete: () => {
                    $("#submitBtn").prop("disabled", false).text("Publish");
                }
            });
        }
    });
});
</script>
