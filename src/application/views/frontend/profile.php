<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>My Notes - NoteApp</title>
  <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.2/dist/cosmo/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background:#f5f7fa; font-family: Inter, sans-serif; }
    .badge-public { background:#28a745; }
    .badge-private { background:#dc3545; }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?php echo site_url(); ?>">NoteApp</a>
    <div class="ms-auto">
      <a href="<?php echo site_url('public'); ?>" class="btn btn-outline-light btn-sm me-2">🌍 Public</a>
      <a href="<?php echo site_url('logout'); ?>" class="btn btn-danger btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container py-4">
  <div class="row">
    <div class="col-lg-7 mx-auto">
      <div class="card shadow mb-4">
        <div class="card-body">
          <h5 class="mb-3">➕ Add / Edit Note</h5>
          <form id="noteForm">
            <input type="hidden" name="id" id="noteId">
            <div class="mb-2">
              <input type="text" id="noteTitle" name="title" class="form-control" placeholder="Title" required>
            </div>
            <div class="mb-2">
              <textarea id="noteContent" name="content" class="form-control" placeholder="Content" rows="3" required></textarea>
            </div>
            <div class="form-check mb-2">
              <input class="form-check-input" type="checkbox" id="notePublic" name="is_public">
              <label class="form-check-label">Make public</label>
            </div>
            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary" id="saveBtn">Save</button>
              <button type="button" class="btn btn-secondary" id="cancelEditBtn" style="display:none">Cancel</button>
            </div>
          </form>
        </div>
      </div>

      <div id="notesList" class="row g-3"></div>

      <nav class="mt-3">
        <ul id="pagination" class="pagination justify-content-center"></ul>
      </nav>
    </div>
  </div>
</div>

<script>
const limit = 5;
let currentPage = 1;

async function loadNotes(page = 1) {
  currentPage = page;
  const res = await fetch('<?php echo site_url("notes/fetch_user"); ?>?page=' + page + '&limit=' + limit);
  const data = await res.json();
  const notes = data.notes || [];
  const totalPages = data.total_pages || 1;

  const list = document.getElementById('notesList');
  list.innerHTML = '';
  if (!notes.length) {
    list.innerHTML = '<div class="text-muted p-3">No notes yet.</div>';
  } else {
    notes.forEach(note => {
      const col = document.createElement('div');
      col.className = 'col-12';
      col.innerHTML = `
        <div class="card shadow-sm">
          <div class="card-body">
            <h5 class="card-title">${escapeHtml(note.title)} <span class="badge ${note.is_public==1 ? 'badge-public' : 'badge-private'}">${note.is_public==1 ? 'Public' : 'Private'}</span></h5>
            <p class="card-text">${escapeHtml(note.content)}</p>
            <p class="small text-muted">By ${escapeHtml(note.username)} • ${note.created_at}</p>
            <button class="btn btn-sm btn-warning me-2" onclick="openEdit(${note.id},'${escapeHtml(note.title)}','${escapeHtml(note.content)}',${note.is_public})">✏️ Edit</button>
            <button class="btn btn-sm btn-danger" onclick="deleteNoteConfirm(${note.id})">🗑 Delete</button>
          </div>
        </div>`;
      list.appendChild(col);
    });
  }
  renderPagination(totalPages);
}

function renderPagination(totalPages) {
  const container = document.getElementById('pagination');
  container.innerHTML = '';

  const prev = document.createElement('li');
  prev.className = 'page-item ' + (currentPage===1?'disabled':'');
  prev.innerHTML = `<a class="page-link" href="#">« Prev</a>`;
  prev.onclick = e => { e.preventDefault(); if(currentPage>1) loadNotes(currentPage-1); };
  container.appendChild(prev);

  for (let i=1;i<=totalPages;i++) {
    const li = document.createElement('li');
    li.className = 'page-item ' + (i===currentPage ? 'active' : '');
    const a = document.createElement('a');
    a.className = 'page-link';
    a.href = '#';
    a.textContent = i;
    a.onclick = (e)=>{ e.preventDefault(); loadNotes(i); };
    li.appendChild(a);
    container.appendChild(li);
  }

  const next = document.createElement('li');
  next.className = 'page-item ' + (currentPage===totalPages?'disabled':'');
  next.innerHTML = `<a class="page-link" href="#">Next »</a>`;
  next.onclick = e => { e.preventDefault(); if(currentPage<totalPages) loadNotes(currentPage+1); };
  container.appendChild(next);
}

function openEdit(id,title,content,is_public) {
  document.getElementById('noteId').value = id;
  document.getElementById('noteTitle').value = title;
  document.getElementById('noteContent').value = content;
  document.getElementById('notePublic').checked = is_public==1;
  document.getElementById('saveBtn').textContent = 'Update';
  document.getElementById('cancelEditBtn').style.display = 'inline-block';
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

document.getElementById('cancelEditBtn').onclick = ()=>resetForm();

function resetForm() {
  document.getElementById('noteForm').reset();
  document.getElementById('noteId').value = '';
  document.getElementById('saveBtn').textContent = 'Save';
  document.getElementById('cancelEditBtn').style.display = 'none';
}

document.getElementById('noteForm').addEventListener('submit', async function(e){
  e.preventDefault();
  const id = document.getElementById('noteId').value;
  const fd = new FormData(e.target);
  const url = id ? '<?php echo site_url("notes/edit"); ?>/' + id : '<?php echo site_url("notes/add"); ?>';
  const res = await fetch(url, { method: 'POST', body: fd });
  const json = await res.json();
  if (json.status === 'success') {
    resetForm();
    loadNotes(currentPage);
  } else {
    alert(json.message || 'Error');
  }
});

function deleteNoteConfirm(id) {
  if (!confirm('Delete this note?')) return;
  deleteNote(id);
}
async function deleteNote(id) {
  const fd = new FormData(); fd.append('id',id);
  const res = await fetch('<?php echo site_url("notes/delete"); ?>',{method:'POST',body:fd});
  const json = await res.json();
  if(json.status==='success') loadNotes(currentPage); else alert('Delete failed');
}

function escapeHtml(str) {
  return String(str||'').replace(/[&<>"'`=\/]/g,s=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;','/':'&#x2F;','`':'&#x60;','=':'&#x3D;'}[s]));
}

loadNotes(1);
</script>
</body>
</html>
