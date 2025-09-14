<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Login - NoteApp</title>
  <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.2/dist/flatly/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background:#f0f4f8; }
    .card { border-radius:12px; }
  </style>
</head>
<body>
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card shadow">
        <div class="card-body">
          <h3 class="text-center text-primary mb-4">🔑 Login</h3>
          <form method="post" action="<?php echo site_url('auth/login'); ?>">
            <div class="mb-3">
              <label>Username or Email</label>
              <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Password</label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
          </form>
          <div class="text-center mt-3">
            No account? <a href="<?php echo site_url('register'); ?>">Register</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
