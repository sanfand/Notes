<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - NotesApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .card { border-radius: 1rem; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .btn-gradient {
            background: linear-gradient(90deg, #6f42c1, #d63384);
            color: white;
            border: none;
        }
        .btn-gradient:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4 col-md-5">
        <h3 class="text-center mb-3">Create an account</h3>

        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger"><?= $this->session->flashdata('error') ?></div>
        <?php endif; ?>
        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success"><?= $this->session->flashdata('success') ?></div>
        <?php endif; ?>

        <form method="post" action="<?= site_url('auth/register') ?>">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-gradient w-100">Register</button>
        </form>

        <div class="mt-3 text-center">
            Already have an account? <a href="<?= site_url('auth/login') ?>">Login here</a>.
        </div>
    </div>
</div>
</body>
</html>
