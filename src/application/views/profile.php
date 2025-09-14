<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>My Notes - NotesApp</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand" href="<?= site_url('profile') ?>">NotesApp</a>
    <div class="ms-auto">
      <a href="<?= site_url('public') ?>" class="btn btn-light btn-sm me-2">Public</a>
      <a href="<?= site_url('logout') ?>" class="btn btn-danger btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container mt-4">
  <div id="alertBox"></div>

  <div class="card mb-4 shadow-sm">
    <div class="card-body">
      <h5>Add Note</h5>
      <form id="addForm">
        <div class="mb-2"><input name="title" id="title" class="form-control" placeholder="Title" required></div>
        <div class="mb-2"><textarea name="content" id="content" class="form-control" rows="3" placeholder="Content" required></textarea></div>
        <div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="is_public" id="is_public" value="1"><label class="form-check-label">Make Public</label></div>
        <button class="btn btn-success" type="submit">Save</button>
      </form>
    </div>
  </div>

  <div id="notesList"></div>

  <nav><ul class="pagination justify-content-center mt-3" id="pagination"></ul></nav>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="editForm">
        <div class="modal-header bg-warning"><h5 class="modal-title">Edit Note</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <input type="hidden" id="editId" name="id">
          <div class="mb-2"><input id="editTitle" name="title" class="form-control" required></div>
          <div class="mb-2"><textarea id="editContent" name="content" class="form-control" rows="3" required></textarea></div>
          <div class="form-check mb-2"><input id="editPublic" name="is_public" class="form-check-input" type="checkbox" value="1"><label class="form-check-label">Make Public</label></div>
        </div>
        <div class="modal-footer"><button class="btn btn-warning" type="submit">Save changes</button></div>
      </form>
    </div>
  </div>
</div>

<script>
// small helpers
function escapeHtml(s) { return String(s).replace(/[&<>"]/g, c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c])); }
function decodeDataAttr(v){ return decodeURIComponent(v || ''); }

let currentPage = 1;

function loadNotes(page=1){
  $.get("<?= site_url('notes/fetch_user') ?>", {page: page}, function(res){
    if (res.status !== 'success') { $('#notesList').html('<div class="alert alert-info">No notes</div>'); return; }
    currentPage = res.current_page || page;
    let html = '';
    if (res.notes.length === 0) html = '<div class="alert alert-info">No notes yet.</div>';
    res.notes.forEach(n=>{
      html += `<div class="card mb-3"><div class="card-body">
        <h5>${escapeHtml(n.title)}</h5>
        <p>${escapeHtml(n.content)}</p>
        <div><small class="text-muted">🕒 ${n.created_at}</small></div>
        <div class="mt-2">
          <button class="btn btn-sm btn-warning me-1 editBtn" 
                  data-id="${n.id}" data-title="${encodeURIComponent(n.title)}" data-content="${encodeURIComponent(n.content)}" data-public="${n.is_public}">Edit</button>
          <button class="btn btn-sm btn-danger deleteBtn" data-id="${n.id}">Delete</button>
          <span class="badge ${n.is_public==1?'bg-success':'bg-secondary'} ms-2">${n.is_public==1?'Public':'Private'}</span>
        </div>
      </div></div>`;
    });
    $('#notesList').html(html);

    // pagination
    let pagHtml = '';
    for (let i=1;i<=res.total_pages;i++){
      pagHtml += `<li class="page-item ${i===currentPage?'active':''}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
    }
    $('#pagination').html(pagHtml);
  }, 'json').fail(function(xhr){ console.error(xhr.responseText); $('#notesList').html('<div class="alert alert-danger">Server error</div>'); });
}

$(function(){
  loadNotes();

  $('#addForm').on('submit', function(e){
    e.preventDefault();
    $.post("<?= site_url('notes/add') ?>", $(this).serialize(), function(res){
      if(res.status==='success'){ $('#alertBox').html('<div class="alert alert-success">'+res.message+'</div>'); $('#addForm')[0].reset(); loadNotes(currentPage); }
      else $('#alertBox').html('<div class="alert alert-danger">'+res.message+'</div>');
    }, 'json').fail(function(xhr){ console.error(xhr.responseText); $('#alertBox').html('<div class="alert alert-danger">Server error</div>'); });
  });

  $(document).on('click', '#pagination .page-link', function(e){
    e.preventDefault();
    let p = parseInt($(this).data('page')) || 1;
    loadNotes(p);
  });

  $(document).on('click', '.deleteBtn', function(){
    if (!confirm('Delete this note?')) return;
    let id = $(this).data('id');
    $.post("<?= site_url('notes/delete') ?>/"+id, function(res){
      if(res.status==='success') loadNotes(currentPage);
      else alert(res.message);
    }, 'json');
  });

  $(document).on('click', '.editBtn', function(){
    let btn = $(this);
    $('#editId').val(btn.data('id'));
    $('#editTitle').val(decodeDataAttr(btn.attr('data-title')));
    $('#editContent').val(decodeDataAttr(btn.attr('data-content')));
    $('#editPublic').prop('checked', btn.data('public')==1);
    new bootstrap.Modal(document.getElementById('editModal')).show();
  });

  $('#editForm').on('submit', function(e){
    e.preventDefault();
    let id = $('#editId').val();
    $.post("<?= site_url('notes/edit') ?>/"+id, $(this).serialize(), function(res){
      if(res.status==='success'){ $('#alertBox').html('<div class="alert alert-success">'+res.message+'</div>'); $('#editModal').modal('hide'); loadNotes(currentPage); }
      else alert(res.message);
    }, 'json');
  });
});
</script>
</body>
</html>
