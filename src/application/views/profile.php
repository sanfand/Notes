<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>My Notes - NotesApp</title>

  <!-- Bootstrap 5 and Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <style>
    :root {
      --brand-1: #6f42c1;
      /* purple */
      --brand-2: #ff6b6b;
      /* coral */
      --card-grad: linear-gradient(135deg, rgba(111, 66, 193, 0.12), rgba(255, 107, 107, 0.06));
    }

    body {
      font-family: 'Inter', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
      background: #f6f8fb;
    }

    .navbar-brand {
      font-weight: 700;
      letter-spacing: 0.2px;
    }

    .hero {
      background: linear-gradient(90deg, rgba(111, 66, 193, 0.94), rgba(255, 107, 107, 0.9));
      color: white;
      padding: 28px 0;
      border-bottom-left-radius: 16px;
      border-bottom-right-radius: 16px;
      box-shadow: 0 6px 20px rgba(111, 66, 193, 0.08);
    }

    .card.note-card {
      background: var(--card-grad);
      border: 0;
    }

    .note-title {
      font-weight: 600;
      color: #2b2b2b;
    }

    .note-content {
      color: #333;
      white-space: pre-wrap;
    }

    .meta {
      font-size: 0.85rem;
      color: #6b6b6b;
    }

    .btn-primary,
    .btn-success,
    .btn-warning {
      box-shadow: 0 6px 18px rgba(111, 66, 193, 0.06);
    }

    .floating-add {
      position: fixed;
      right: 20px;
      bottom: 20px;
      z-index: 1030;
    }

    .badge-public {
      background: linear-gradient(90deg, #bfad06, #ff6b6b);
      color: #fff;
    }

    .badge-private {
      background: linear-gradient(90deg, #4688f2, #06912b);
      color: #fff;
    }

    .alert-area {
      min-height: 38px;
    }

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
  <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(90deg,var(--brand-1),var(--brand-2));">
    <div class="container">
      <a class="navbar-brand" href="<?= site_url('profile') ?>">NotesApp</a>
      <div class="ms-auto">
        <a href="<?= site_url('public') ?>" class="btn btn-light btn-sm me-2">Public Feed</a>
        <a href="<?= site_url('logout') ?>" class="btn btn-outline-light btn-sm">Logout</a>
      </div>
    </div>
  </nav>

  <div class="hero text-light mb-4" style="justify-content:center;align-items:center;">
    <div class="container d-flex align-items-center justify-content-between">
      <button class="btn btn-lg btn-light" id="openAddBtn" , style="display:flex;justify-content:center;align-items:center;">+ New note</button>
    </div>
  </div>
  </div>

  <div class="container">
    <div id="alertBox" class="alert-area mb-3"></div>

    <div id="notesList" class="row g-3"></div>

    <nav>
      <ul class="pagination pagination-sm justify-content-center mt-4" id="pagination"></ul>
    </nav>
  </div>

  <!-- Add Note Modal -->
  <div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
      <div class="modal-content">
        <form id="addForm">
          <div class="modal-header" style="background: linear-gradient(90deg,#fff,#fff);">
            <h5 class="modal-title">Add New Note</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-2">
              <input name="title" id="addTitle" class="form-control form-control-lg" placeholder="Title" required>
            </div>
            <div class="mb-2">
              <textarea name="content" id="addContent" class="form-control" rows="6" placeholder="Write your note..." required></textarea>
            </div>
            <div class="form-check mb-0">
              <input class="form-check-input" type="checkbox" value="1" id="addPublic" name="is_public">
              <label class="form-check-label" for="addPublic">Make this note public</label>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-light" type="button" data-bs-dismiss="modal">Cancel</button>
            <button class="btn btn-success" type="submit">Save note</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Edit Modal (existing style preserved) -->
  <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
      <div class="modal-content">
        <form id="editForm">
          <div class="modal-header" style="background: linear-gradient(90deg,#fff,#fff);">
            <h5 class="modal-title">Edit Note</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="editId" name="id">
            <div class="mb-2">
              <input id="editTitle" name="title" class="form-control form-control-lg" required>
            </div>
            <div class="mb-2">
              <textarea id="editContent" name="content" class="form-control" rows="6" required></textarea>
            </div>
            <div class="form-check mb-0">
              <input class="form-check-input" id="editPublic" name="is_public" type="checkbox" value="1">
              <label class="form-check-label" for="editPublic">Make this note public</label>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-light" type="button" data-bs-dismiss="modal">Cancel</button>
            <button class="btn btn-warning" type="submit">Save changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    // Utilities
    function escapeHtml(s) {
      return String(s).replace(/[&<>"]/g, c => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;'
      } [c]));
    }

    let currentPage = 1;
    const ajaxUrl = "<?= site_url('notes/ajax') ?>";

    // Load notes for current user
    function loadNotes(page = 1) {
      currentPage = page;
      $('#notesList').html('<div class="text-center w-100 py-5">Loading…</div>');
      $.get(ajaxUrl, {
        action: 'fetch_user',
        page: page
      }, function(res) {
        if (res.status !== 'success') {
          $('#notesList').html('<div class="alert alert-info w-100">No notes yet — add your first note.</div>');
          $('#pagination').html('');
          return;
        }
        const notes = res.notes || [];
        if (notes.length === 0) {
          $('#notesList').html('<div class="alert alert-info w-100">No notes found on this page.</div>');
        } else {
          let html = '';
          notes.forEach(n => {
            const title = escapeHtml(n.title || '');
            const content = escapeHtml(n.content || '');
            const badge_1 = (Number(n.is_public) === 1) ? '<span class="badge badge-public ms-1">Public</span>' : '';
            const badge_2 = (Number(n.is_public) === 0) ? '<span class="badge badge-private ms-1">Private</span>' : '';
            const meta = `<div class="meta">${n.created_at} ${badge_1 || badge_2}</div>`;

            html += `<div class="col-md-6">
                  <div class="card note-card shadow-sm p-3 h-100">
                    <div class="d-flex justify-content-between align-items-start">
                      <div>
                        <div class="note-title">${title}</div>
                        <div class="note-content mt-2">${content}</div>
                      </div>
                      <div class="text-end">
                        <button class="btn btn-sm btn-outline-primary mb-1 editBtn" data-note='${encodeURIComponent(JSON.stringify(n))}'>Edit</button>
                        <button class="btn btn-sm btn-outline-danger mb-1 deleteBtn" data-id="${n.id}">Delete</button>
                      </div>
                    </div>
                    <div class="mt-3 ${n.is_public ? '' : 'text-muted'}">${meta}</div>
                  </div>
                </div>`;
          });
          $('#notesList').html(html);
        }

        // pagination
        let pagHtml = '';
        const totalPages = res.total_pages || 1;

        // Previous button
        pagHtml += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
              <a class="page-link" href="#" data-page="${currentPage - 1}" aria-label="Previous">
                &laquo;
              </a>
            </li>`;


        
        // Numbered pages
        for (let i = 1; i <= totalPages; i++) {
          pagHtml += `<li class="page-item ${i === currentPage ? 'active' : ''}">
                <a class="page-link" href="#" data-page="${i}">${i}</a>
              </li>`;
        }

        // Next button
        pagHtml += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
              <a class="page-link" href="#" data-page="${currentPage + 1}" aria-label="Next">
                &raquo;
              </a>
            </li>`;

        $('#pagination').html(pagHtml);

      }, 'json').fail(function(xhr) {
        console.error(xhr.responseText);
        $('#notesList').html('<div class="alert alert-danger w-100">Server error</div>');
      });
    }

    $(function() {
      loadNotes();

      // Open add modal
      $('#openAddBtn').on('click', function() {
        $('#addModal').modal('show');
      });

      // Submit add form via AJAX
      $('#addForm').on('submit', function(e) {
        e.preventDefault();
        const data = {
          action: 'add',
          title: $('#addTitle').val(),
          content: $('#addContent').val(),
          is_public: $('#addPublic').is(':checked') ? 1 : 0
        };
        $.post(ajaxUrl, data, function(res) {
          if (res.status === 'success') {
            $('#alertBox').html('<div class="alert alert-success">Saved successfully</div>');
            $('#addForm')[0].reset();
            $('#addModal').modal('hide');
            loadNotes(currentPage);
          } else {
            $('#alertBox').html('<div class="alert alert-danger">' + escapeHtml(res.message || 'Error') + '</div>');
          }
        }, 'json').fail(function(xhr) {
          console.error(xhr.responseText);
          $('#alertBox').html('<div class="alert alert-danger">Server error</div>');
        });
      });

      // Click pagination
      $(document).on('click', '#pagination .page-link', function(e) {
        e.preventDefault();
        const p = parseInt($(this).data('page')) || 1;
        loadNotes(p);
      });

      // Edit button: open edit modal and populate fields
      $(document).on('click', '.editBtn', function() {
        try {
          const note = JSON.parse(decodeURIComponent($(this).attr('data-note')));
          $('#editId').val(note.id);
          $('#editTitle').val(note.title);
          $('#editContent').val(note.content);
          $('#editPublic').prop('checked', (note.is_public * 1) === 1);
          $('#editModal').modal('show');
        } catch (e) {
          console.error(e);
          alert('Could not open editor');
        }
      });

      // Submit edit form via AJAX
      $('#editForm').on('submit', function(e) {
        e.preventDefault();
        const data = {
          action: 'edit',
          id: $('#editId').val(),
          title: $('#editTitle').val(),
          content: $('#editContent').val(),
          is_public: $('#editPublic').is(':checked') ? 1 : 0
        };
        $.post(ajaxUrl, data, function(res) {
          if (res.status === 'success') {
            $('#alertBox').html('<div class="alert alert-success">Updated successfully</div>');
            $('#editModal').modal('hide');
            loadNotes(currentPage);
          } else {
            $('#alertBox').html('<div class="alert alert-danger">' + escapeHtml(res.message || 'Error') + '</div>');
          }
        }, 'json').fail(function(xhr) {
          console.error(xhr.responseText);
          $('#alertBox').html('<div class="alert alert-danger">Server error</div>');
        });
      });

      // Delete note
      $(document).on('click', '.deleteBtn', function() {
        if (!confirm('Delete this note?')) return;
        const id = $(this).data('id');
        $.post(ajaxUrl, {
          action: 'delete',
          id: id
        }, function(res) {
          if (res.status === 'success') {
            loadNotes(currentPage);
          } else {
            alert(res.message || 'Delete failed');
          }
        }, 'json').fail(function(xhr) {
          console.error(xhr.responseText);
          alert('Server error');
        });
      });
    });
  </script>
</body>

</html>