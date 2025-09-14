<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Public Notes - NoteApp</title>
  <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.2/dist/flatly/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background:#f7f9fc; }
    .card { border-radius: 14px; box-shadow:0 3px 8px rgba(0,0,0,0.1); }
    .badge-public { background:#28a745; }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?php echo site_url(); ?>">NoteApp</a>
    <div class="ms-auto">
      <a href="<?php echo site_url('login'); ?>" class="btn btn-outline-light btn-sm">🔑 Login</a>
    </div>
  </div>
</nav>

<div class="container py-4">
  <h3 class="mb-3 text-primary">🌍 Public Notes</h3>
  <div id="publicList" class="row g-3"></div>

  <nav class="mt-3">
    <ul id="pubPagination" class="pagination justify-content-center"></ul>
  </nav>
</div>

<script>
const pubLimit = 5; let pubPage = 1;

async function loadPublic(page=1){
  pubPage=page;
  const res=await fetch('<?php echo site_url("notes/fetch_public"); ?>?page='+page+'&limit='+pubLimit);
  const data=await res.json();
  const notes=data.notes||[], total=data.total_pages||1;

  const container=document.getElementById('publicList'); container.innerHTML='';
  if(!notes.length){
    container.innerHTML='<div class="text-muted">No public notes yet.</div>'; return;
  }

  notes.forEach(note=>{
    const col=document.createElement('div'); col.className='col-md-6';
    col.innerHTML=`
      <div class="card h-100">
        <div class="card-body">
          <h5 class="card-title">${escapeHtml(note.title)} <span class="badge badge-public">Public</span></h5>
          <p class="card-text">${escapeHtml(note.content)}</p>
          <p class="small text-muted">By ${escapeHtml(note.username||'Unknown')} • ${note.created_at}</p>
        </div>
      </div>`;
    container.appendChild(col);
  });

  renderPubPagination(total);
}

function renderPubPagination(totalPages){
  const list=document.getElementById('pubPagination'); list.innerHTML='';

  const prev=document.createElement('li'); prev.className='page-item '+(pubPage===1?'disabled':'');
  prev.innerHTML='<a class="page-link" href="#">« Prev</a>';
  prev.onclick=e=>{e.preventDefault();if(pubPage>1)loadPublic(pubPage-1);};
  list.appendChild(prev);

  for(let i=1;i<=totalPages;i++){
    const li=document.createElement('li'); li.className='page-item '+(i===pubPage?'active':'');
    li.innerHTML='<a class="page-link" href="#">'+i+'</a>';
    li.onclick=e=>{e.preventDefault();loadPublic(i);};
    list.appendChild(li);
  }

  const next=document.createElement('li'); next.className='page-item '+(pubPage===totalPages?'disabled':'');
  next.innerHTML='<a class="page-link" href="#">Next »</a>';
  next.onclick=e=>{e.preventDefault();if(pubPage<totalPages)loadPublic(pubPage+1);};
  list.appendChild(next);
}

function escapeHtml(str){
  return String(str||'').replace(/[&<>"'`=\/]/g,s=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;','/':'&#x2F;','`':'&#x60;','=':'&#x3D;'}[s]));
}

loadPublic(1);
</script>
</body>
</html>
