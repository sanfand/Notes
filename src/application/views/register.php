<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Register - NotesApp</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body class="bg-light">
  <div class="container d-flex align-items-center justify-content-center vh-100">
    <div class="card shadow" style="width:420px;">
      <div class="card-body p-4">
        <h4 class="mb-3 text-center text-success">📝 Register</h4>
        <div id="alertBox"></div>
        <form id="registerForm">
          <div class="mb-3"><label class="form-label">Username</label><input type="text" name="username" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
          <button class="btn btn-success w-100" type="submit">Register</button>
        </form>
        <p class="mt-3 text-center">Already have an account? <a href="<?= site_url('login') ?>">Login</a></p>
      </div>
    </div>
  </div>

<script>
$(function(){
  $('#registerForm').on('submit', function(e){
    e.preventDefault();
    $('#alertBox').html('');
    $.post("<?= site_url('auth/do_register') ?>", $(this).serialize(), function(res){
      if (res.status === 'success') {
        window.location.href = "<?= site_url('login') ?>";
      } else {
        $('#alertBox').html('<div class="alert alert-danger">'+res.message+'</div>');
      }
    }, 'json').fail(function(xhr){
      console.error(xhr.responseText);
      $('#alertBox').html('<div class="alert alert-danger">Server error. Check logs.</div>');
    });
  });
});
</script>
</body>
</html>
