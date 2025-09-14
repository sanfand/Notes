<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>NoteApp - Home</title>
  <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.2/dist/lux/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Inter', sans-serif; }
    .btn { transition: all 0.2s ease; }
    .btn:hover { transform: translateY(-2px); }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?php echo site_url(); ?>">NoteApp</a>
    <div class="d-flex align-items-center">
      <a href="<?php echo site_url('auth/login'); ?>" class="btn btn-outline-light btn-sm me-2 rounded-pill">Login</a>
      <a href="<?php echo site_url('auth/register'); ?>" class="btn btn-light btn-sm rounded-pill">Register</a>
    </div>
  </div>
</nav>

<section class="py-5 text-center">
  <div class="container">
    <h1 class="fw-bold text-primary mb-3">Welcome to NoteApp 📒</h1>
    <p class="lead text-muted mb-4">Your modern space to write, edit, and share notes beautifully.</p>
    <a href="<?php echo site_url('home'); ?>" class="btn btn-lg btn-primary rounded-pill shadow me-2">My Notes</a>
    <a href="<?php echo site_url('notes/fetch_public'); ?>" class="btn btn-lg btn-outline-primary rounded-pill shadow">Public Notes</a>
  </div>
</section>

</body>
</html>
