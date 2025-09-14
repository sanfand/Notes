<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Public - NotesApp</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="<?= site_url('profile') ?>">NotesApp</a>
    <div class="ms-auto">
      <a href="<?= site_url('profile') ?>" class="btn btn-light btn-sm me-2">My Notes</a>
      <a href="<?= site_url('logout') ?>" class="btn btn-danger btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container mt-4">
  <h3 class="mb-3">Public Notes</h3>
  <div id="publicList"></div>
  <nav><ul class="pagination justify-content-center mt-3" id="publicPagination"></ul></nav>
</div>

<script>
function escapeHtml(s){ return String(s).replace(/[&<>"]/g, c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c])); }

function loadPublic(page=1){
  $.get("<?= site_url('notes/fetch_public') ?>", {page: page}, function(res){
    if (res.status !== 'success') { $('#publicList').html('<div class="alert alert-info">No public notes</div>'); return; }
    let html = '';
    if(res.notes.length === 0) html = '<div class="alert alert-info">No public notes yet</div>';
    res.notes.forEach(n=>{
      html += `<div class="card mb-3"><div class="card-body">
        <h5>${escapeHtml(n.title)}</h5>
        <p>${escapeHtml(n.content)}</p>
        <div><small class="text-muted">🕒 ${n.created_at} | 👤 ${escapeHtml(n.username || 'Unknown')}</small></div>
      </div></div>`;
    });
    $('#publicList').html(html);

    let pag = '';
    for(let i=1;i<=res.total_pages;i++){
      pag += `<li class="page-item ${i===res.current_page?'active':''}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
    }
    $('#publicPagination').html(pag);
  }, 'json').fail(function(xhr){ console.error(xhr.responseText); $('#publicList').html('<div class="alert alert-danger">Server error</div>'); });
}

$(function(){
  loadPublic();
  $(document).on('click', '#publicPagination .page-link', function(e){ e.preventDefault(); loadPublic(parseInt($(this).data('page'))||1); });
});
</script>
</body>
</html>
