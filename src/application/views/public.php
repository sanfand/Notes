<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Public Notes - NotesApp</title>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <style>
    body { font-family: 'Inter', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial; background: #fbfbff; }
    .navbar { background: linear-gradient(90deg,#6f42c1,#ff6b6b); }
    .card { border: 0; box-shadow: 0 6px 20px rgba(0,0,0,0.04); }
    .note-title { font-weight: 600; }
    .meta { color: #666; font-size: 0.85rem; }
    .container { padding-top: 28px; padding-bottom: 40px; }
    /* pagination active button */
    .pagination .page-item.active .page-link {
      background: #6f42c1;
      border: none;
      color: #fff;
      font-weight: 600;
      border-radius: 8px;
    }

    /* normal buttons */
    .pagination .page-link {
      color: #6f42c1;
      border-radius: 8px;
      margin: 0 2px;
      border: 1px solid #ddd;
      transition: all 0.2s;
    }

    .pagination .page-link:hover {
      background: #f3e8ff;
      color: #6f42c1;
    }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container">
    <a class="navbar-brand" href="<?= site_url('profile') ?>">NotesApp</a>
    <div class="ms-auto">
      <a href="<?= site_url('profile') ?>" class="btn btn-light btn-sm me-2">My Notes</a>
      <?php if ($this->session->userdata('user_id')): ?>
        <a href="<?= site_url('logout') ?>" class="btn btn-outline-light btn-sm">Logout</a>
      <?php else: ?>
        <a href="<?= site_url('login') ?>" class="btn btn-outline-light btn-sm">Login</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<div class="container">
  <h3 class="mb-3">Public Notes</h3>

  <div id="publicList" class="row g-3"></div>

  <nav><ul class="pagination justify-content-center mt-4" id="publicPagination"></ul></nav>
</div>

<script>
function escapeHtml(s){ return String(s).replace(/[&<>"]/g, c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c])); }
const ajaxUrl = "<?= site_url('notes/ajax') ?>";
let publicPage = 1;

function loadPublic(page = 1) {
  publicPage = page;
  $('#publicList').html('<div class="text-center w-100 py-5">Loading…</div>');
  $.get(ajaxUrl, { action: 'fetch_public', page: page }, function(res){
    if (res.status !== 'success' || !res.notes || res.notes.length === 0) {
      $('#publicList').html('<div class="alert alert-info w-100">No public notes yet.</div>');
      $('#publicPagination').html('');
      return;
    }
    let html = '';
    res.notes.forEach(n=>{
      html += `<div class="col-md-6">
                <div class="card p-3">
                  <div>
                    <div class="note-title">${escapeHtml(n.title)}</div>
                    <div class="note-content mt-2">${escapeHtml(n.content)}</div>
                  </div>
                  <div class="mt-3 meta">${n.created_at} • by ${escapeHtml(n.username || 'Anonymous')}</div>
                </div>
              </div>`;
    });
    $('#publicList').html(html);

    // pagination
    let pagHtml = '';
    const totalPages = res.total_pages || 1;

    // Previous button
    pagHtml += `<li class="page-item ${publicPage === 1 ? 'disabled' : ''}">
              <a class="page-link" href="#" data-page="${publicPage - 1}" aria-label="Previous">
                &laquo;
              </a>
            </li>`;


        
    // Numbered pages
    for (let i = 1; i <= totalPages; i++) {
          pagHtml += `<li class="page-item ${i === publicPage ? 'active' : ''}">
                <a class="page-link" href="#" data-page="${i}">${i}</a>
              </li>`;
        }

    // Next button
    pagHtml += `<li class="page-item ${publicPage === totalPages ? 'disabled' : ''}">
              <a class="page-link" href="#" data-page="${publicPage + 1}" aria-label="Next">
                &raquo;
              </a>
            </li>`;

    $('#publicPagination').html(pagHtml);

  }, 'json').fail(function(xhr){
    console.error(xhr.responseText);
    $('#publicList').html('<div class="alert alert-danger w-100">Server error</div>');
  });
}

$(function(){
  loadPublic();

  $(document).on('click', '#publicPagination .page-link', function(e){
    e.preventDefault();
    const p = parseInt($(this).data('page')) || 1;
    loadPublic(p);
  });
});
</script>
</body>
</html>
