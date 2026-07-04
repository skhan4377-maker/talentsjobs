<div class="min-h-screen bg-gray-50">
  <div class="max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="mb-8">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl md:text-3xl font-bold text-gray-900"></h1>
          <p class="text-gray-600 mt-1"></p>
        </div>
        <div class="flex items-center gap-3">
          <form method="GET" action="<?= base_url('admin/support') ?>" class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
            <input type="text" name="search" placeholder="Search enquiries..." value="<?= html_escape($search) ?>" 
                   class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full sm:w-64">
          </form>
        </div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
            </svg>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-600">Total Enquiries</p>
            <p class="text-2xl font-bold text-gray-900"><?= $total_rows ?></p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-600">Replied</p>
            <p class="text-2xl font-bold text-gray-900" id="repliedCount">0</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-orange-100 text-orange-600 mr-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-600">Pending Reply</p>
            <p class="text-2xl font-bold text-gray-900" id="pendingCount">0</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <h2 class="text-lg font-semibold text-gray-800">All Support Requests</h2>
        <div class="text-sm text-gray-600">
          Showing <?= ($current_offset + 1) ?> to <?= min($current_offset + $per_page, $total_rows) ?> of <?= $total_rows ?> entries
        </div>
      </div>

      <!-- Compact Table -->
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
            <tr>
              <th class="px-3 py-2 text-left">User</th>
              <th class="px-3 py-2 text-left">Role</th>
              <th class="px-3 py-2 text-left">Subject</th>
              <th class="px-3 py-2 text-left">Date</th>
              <th class="px-3 py-2 text-left">Status</th>
              <th class="px-3 py-2 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <?php if (empty($enquiries)): ?>
              <tr><td colspan="6" class="text-center py-4 text-gray-500">No enquiries found.<?= $search ? ' Try a different search term.' : '' ?></td></tr>
            <?php else: ?>
              <?php foreach ($enquiries as $enquiry): ?>
                <tr class="hover:bg-gray-50">
                  <td class="px-3 py-2">
                    <div class="flex items-center gap-2">
                      <div class="h-8 w-8 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center text-white text-xs font-semibold">
                        <?= strtoupper(substr($enquiry->name, 0, 1)) ?>
                      </div>
                      <div>
                        <div class="font-medium text-gray-800 text-sm"><?= html_escape($enquiry->name) ?></div>
                        <div class="text-xs text-gray-500"><?= html_escape($enquiry->email) ?></div>
                      </div>
                    </div>
                  </td>
                  <td class="px-3 py-2">
                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium <?= $enquiry->role == 'candidate' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' ?>">
                      <?= $enquiry->role ?>
                    </span>
                  </td>
                  <td class="px-3 py-2">
                    <div class="text-sm text-gray-800 truncate max-w-xs" title="<?= html_escape($enquiry->subject) ?>">
                      <?= html_escape(substr($enquiry->subject, 0, 50)) ?><?= strlen($enquiry->subject) > 50 ? '…' : '' ?>
                    </div>
                  </td>
                  <td class="px-3 py-2 text-xs text-gray-600">
                    <?= date('d M Y', strtotime($enquiry->submitted_at)) ?><br>
                    <?= date('h:i A', strtotime($enquiry->submitted_at)) ?>
                  </td>
                  <td class="px-3 py-2">
                    <?php if ($enquiry->has_reply): ?>
                      <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        Replied
                      </span>
                    <?php else: ?>
                      <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                        Pending
                      </span>
                    <?php endif; ?>
                  </td>
                  <td class="px-3 py-2 text-right">
                    <div class="flex items-center justify-end gap-1">
                      <button onclick="viewMessage(<?= $enquiry->id ?>)" class="text-gray-400 hover:text-blue-600 p-1 rounded" title="View">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                      </button>
                      <button onclick="openReplyModal(<?= $enquiry->id ?>)" class="text-gray-400 hover:text-green-600 p-1 rounded" title="Reply">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                      </button>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Pagination Links -->
      <div class="px-6 py-4 border-t border-gray-200 flex justify-center">
        <?= $links ?>
      </div>
    </div>
  </div>
</div>

<!-- Reply Modal -->
<div id="replyModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
  <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col">
    <!-- Modal Header -->
    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
      <div>
        <h3 class="text-xl font-bold text-gray-800">Reply to Support Request</h3>
        <p class="text-gray-600 text-sm mt-1">Send a response to <span id="replyName" class="font-medium text-blue-600"></span></p>
      </div>
      <button class="text-gray-500 hover:text-red-600 text-xl p-1" onclick="closeReplyModal()">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

    <!-- Modal Body -->
    <div class="flex-1 overflow-y-auto p-6">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- User Info & Original Message -->
        <div class="lg:col-span-1 space-y-6">
          <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
            <h4 class="font-medium text-gray-800 mb-3">User Information</h4>
            <div class="space-y-3">
              <div><p class="text-sm text-gray-600">Name</p><p class="font-medium" id="userName"></p></div>
              <div><p class="text-sm text-gray-600">Email</p><p class="font-medium" id="userEmail"></p></div>
              <div><p class="text-sm text-gray-600">Role</p><p class="font-medium capitalize" id="userRole"></p></div>
              <div><p class="text-sm text-gray-600">Submitted</p><p class="font-medium" id="submittedDate"></p></div>
            </div>
          </div>
          <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
            <h4 class="font-medium text-gray-800 mb-3">Original Message</h4>
            <div class="bg-white rounded border border-gray-200 p-3 max-h-60 overflow-y-auto">
              <p id="userMessage" class="text-sm text-gray-700 whitespace-pre-line"></p>
              <div id="attachmentContainer" class="mt-3"></div>
            </div>
          </div>
        </div>

        <!-- Reply Form -->
        <div class="lg:col-span-2">
          <form id="replyForm" novalidate>
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
            <input type="hidden" name="id" id="replyId">
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Subject *</label>
                <input type="text" name="subject" id="replySubject" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Reply Message *</label>
                <textarea name="reply_message" id="replyMessage" rows="12" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Type your response here..."></textarea>
              </div>
             
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal Footer -->
    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex items-center justify-between">
      <div id="replyMsg" class="text-sm"></div>
      <div class="flex gap-3">
        <button type="button" onclick="closeReplyModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">Cancel</button>
        <button type="button" id="sendReplyBtn" onclick="submitReply()" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium flex items-center gap-2">
          <span class="btn-text">Send Reply</span>
          <span class="btn-loader hidden animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4"></span>
        </button>
      </div>
    </div>
  </div>
</div>

<!-- View Message Modal -->
<div id="viewModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
  <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden flex flex-col">
    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
      <h3 class="text-xl font-bold text-gray-800">Support Request Details</h3>
      <button class="text-gray-500 hover:text-red-600 text-xl p-1" onclick="closeViewModal()">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>
    <div class="flex-1 overflow-y-auto p-6" id="viewContent"></div>
    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end">
      <button type="button" onclick="closeViewModal()" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium">Close</button>
    </div>
  </div>
</div>


<script>
let editorInitialized = false;

function initTinyMCE() {
  if (tinymce.get('replyMessage')) {
    tinymce.get('replyMessage').remove();
    editorInitialized = false;
  }
  tinymce.init({
    selector: '#replyMessage',
    height: 300,
    menubar: false,
    plugins: 'link lists code',
    toolbar: 'undo redo | bold italic underline | bullist numlist | link code',
    branding: false,
    promotion: false,
    statusbar: false,
    setup: function(editor) {
      editor.on('init', function() {
        editorInitialized = true;
      });
    }
  });
}

function destroyTinyMCE() {
  if (tinymce.get('replyMessage')) {
    tinymce.get('replyMessage').remove();
    editorInitialized = false;
  }
}

function updateStats() {
  let replied = 0, pending = 0;
  <?php foreach ($enquiries as $e): ?>
    <?= $e->has_reply ? 'replied++;' : 'pending++;' ?>
  <?php endforeach; ?>
  document.getElementById('repliedCount').innerText = replied;
  document.getElementById('pendingCount').innerText = pending;
}

let searchTimeout;
document.querySelector('input[name="search"]')?.addEventListener('input', function() {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => this.form.submit(), 500);
});

// ========== FIXED REPLY MODAL ==========
function openReplyModal(id) {
  // Show modal and loading state
  $('#replyModal').removeClass('hidden');
  $('#replyMsg').text('Loading enquiry details...').removeClass('text-green-600 text-red-600').addClass('text-blue-600');
  
  // Clear old data
  $('#replyId').val('');
  $('#replyName').text('');
  $('#userName').text('');
  $('#userEmail').text('');
  $('#userRole').text('');
  $('#replySubject').val('');
  $('#submittedDate').text('');
  $('#userMessage').text('');
  $('#attachmentContainer').empty();
  
  // Destroy any existing editor (to avoid conflicts)
  destroyTinyMCE();
  
  // Fetch data
  $.ajax({
    url: `<?= site_url('admin/support/get_enquiry/') ?>${id}`,
    type: 'GET',
    dataType: 'json',
    success: function(res) {
      if (res.status === 'success') {
        const e = res.data;
        // Populate fields
        $('#replyId').val(e.id);
        $('#replyName').text(e.name);
        $('#userName').text(e.name);
        $('#userEmail').text(e.email);
        $('#userRole').text(e.role);
        $('#replySubject').val(e.subject);
        $('#submittedDate').text(new Date(e.submitted_at).toLocaleString());
        $('#userMessage').text(e.message);
        
        // Attachment
        if (e.attachment) {
          let url = e.attachment.startsWith('http') ? e.attachment : '<?= base_url() ?>' + e.attachment;
          $('#attachmentContainer').html(`<div class="flex items-center gap-2 p-2 bg-white border rounded"><svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg><a href="${url}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">View Attachment</a></div>`);
        }
        
        // Prepare reply content
        let greeting = `Dear ${e.name},<br><br>`;
        let replyContent = e.reply_message ? e.reply_message.replace(/Dear\s+[^,]+,\s*<br><br>/i, '') : '';
        
        // Initialise TinyMCE AFTER data is loaded, then set content
        initTinyMCE();
        // Wait a short moment for editor to be ready
        setTimeout(() => {
          const editor = tinymce.get('replyMessage');
          if (editor) {
            editor.setContent(greeting + replyContent);
          } else {
            // fallback: set textarea value (if TinyMCE fails)
            $('#replyMessage').val(greeting + replyContent.replace(/<br>/g, '\n'));
          }
        }, 200);
        
        $('#replyMsg').text('').removeClass('text-blue-600 text-red-600 text-green-600');
      } else {
        $('#replyMsg').text(res.message || 'Failed to load enquiry details').addClass('text-red-600');
      }
    },
    error: function(xhr, status, error) {
      console.error('AJAX Error:', error);
      $('#replyMsg').text('Error loading enquiry details. Please try again.').addClass('text-red-600');
    }
  });
}

function closeReplyModal() {
  destroyTinyMCE();
  $('#replyModal').addClass('hidden');
  $('#replyMsg').text('').removeClass('text-blue-600 text-red-600 text-green-600');
}

function submitReply() {
  const $btn = $('#sendReplyBtn'), $msg = $('#replyMsg');
  const subject = $('#replySubject').val().trim();
  let replyContent = '';
  if (tinymce.get('replyMessage')) {
    replyContent = tinymce.get('replyMessage').getContent().trim();
  } else {
    replyContent = $('#replyMessage').val().trim();
  }
  if (!subject) {
    $msg.text('Please enter a subject.').addClass('text-red-600');
    return;
  }
  if (!replyContent) {
    $msg.text('Please enter a reply message.').addClass('text-red-600');
    return;
  }
  
  $btn.prop('disabled', true);
  $btn.find('.btn-text').text('Sending...');
  $btn.find('.btn-loader').removeClass('hidden');
  $msg.text('Sending reply...').removeClass('text-red-600 text-green-600').addClass('text-blue-600');
  
  const formData = new FormData();
  formData.append('id', $('#replyId').val());
  formData.append('email', $('#userEmail').text());
  formData.append('subject', subject);
  formData.append('reply_message', replyContent);
  formData.append('<?= $this->security->get_csrf_token_name(); ?>', $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val());
  
  $.ajax({
    url: '<?= site_url("admin/support/reply") ?>',
    type: 'POST',
    data: formData,
    dataType: 'json',
    processData: false,
    contentType: false,
    success: function(res) {
      if (res.status === 'success') {
        $msg.text(res.message).removeClass('text-blue-600 text-red-600').addClass('text-green-600');
        setTimeout(() => {
          closeReplyModal();
          location.reload();
        }, 1500);
      } else {
        $msg.text(res.message || 'Failed to send reply').removeClass('text-blue-600 text-green-600').addClass('text-red-600');
      }
    },
    error: function() {
      $msg.text('An error occurred while sending the reply.').removeClass('text-blue-600 text-green-600').addClass('text-red-600');
    },
    complete: function() {
      $btn.prop('disabled', false);
      $btn.find('.btn-text').text('Send Reply');
      $btn.find('.btn-loader').addClass('hidden');
    }
  });
}

// View Message (unchanged but ensure loading indicator)
function viewMessage(id) {
  $('#viewModal').removeClass('hidden');
  $('#viewContent').html('<div class="flex justify-center py-8"><div class="animate-spin h-8 w-8 border-b-2 border-blue-600"></div><span class="ml-2">Loading...</span></div>');
  $.ajax({
    url: `<?= site_url('admin/support/get_enquiry/') ?>${id}`,
    type: 'GET',
    dataType: 'json',
    success: function(res) {
      if (res.status === 'success') {
        const e = res.data;
        $('#viewContent').html(`
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4"><div><label class="block text-sm font-medium">User Information</label><div class="bg-gray-50 rounded p-4 space-y-2">${[
              `<div class="flex justify-between"><span>Name:</span><span class="font-medium">${escapeHtml(e.name)}</span></div>`,
              `<div class="flex justify-between"><span>Email:</span><span class="font-medium">${escapeHtml(e.email)}</span></div>`,
              `<div class="flex justify-between"><span>Role:</span><span class="font-medium capitalize">${e.role}</span></div>`,
              `<div class="flex justify-between"><span>Submitted:</span><span class="font-medium">${new Date(e.submitted_at).toLocaleString()}</span></div>`,
              e.ip_address ? `<div class="flex justify-between"><span>IP:</span><span class="font-medium">${e.ip_address}</span></div>` : ''
            ].join('')}</div></div>${e.attachment ? `<div><label class="block text-sm font-medium">Attachment</label><div class="bg-gray-50 rounded p-4"><a href="<?= base_url() ?>${e.attachment}" target="_blank" class="text-blue-600">View Attachment</a></div></div>` : ''}</div>
            <div class="space-y-4"><div><label class="block text-sm font-medium">Subject</label><div class="bg-gray-50 rounded p-4">${escapeHtml(e.subject)}</div></div><div><label class="block text-sm font-medium">Message</label><div class="bg-gray-50 rounded p-4 max-h-60 overflow-y-auto whitespace-pre-line">${escapeHtml(e.message)}</div></div>${e.reply_message ? `<div><label class="block text-sm font-medium">Admin Reply${e.replied_at ? ` <span class="text-xs text-gray-500">(Sent on ${new Date(e.replied_at).toLocaleString()})</span>` : ''}</label><div class="bg-blue-50 rounded p-4">${e.reply_message}</div></div>` : `<div class="bg-yellow-50 border border-yellow-200 rounded p-4"><div class="flex"><div class="flex-shrink-0"><svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg></div><div class="ml-3"><h3 class="text-sm font-medium text-yellow-800">No reply sent yet</h3><p class="text-sm text-yellow-700">Click the reply button to send a response.</p></div></div></div>`}</div>
          </div>
        `);
      } else $('#viewContent').html(`<div class="text-center text-red-600">${res.message || 'Failed to load'}</div>`);
    },
    error: () => $('#viewContent').html('<div class="text-center text-red-600">Error loading details.</div>')
  });
}

function closeViewModal() { $('#viewModal').addClass('hidden'); }

function escapeHtml(text) {
  let div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

$(document).on('keydown', function(e) {
  if (e.key === 'Escape') {
    if (!$('#replyModal').hasClass('hidden')) closeReplyModal();
    if (!$('#viewModal').hasClass('hidden')) closeViewModal();
  }
});

$(document).ready(function() {
  updateStats();
});
</script>