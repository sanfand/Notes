<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Register - NoteApp</title>
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
          <h3 class="text-center text-success mb-4">📝 Register</h3>
          <form method="post" action="<?php echo site_url('auth/register'); ?>">
            <div class="mb-3">
              <label>Username</label>
              <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Email</label>
              <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Password</label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success w-100">Register</button>
          </form>
          <div class="text-center mt-3">
            Already have an account? <a href="<?php echo site_url('login'); ?>">Login</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
