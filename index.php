<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      
      <?php if(isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          Invalid username, password, or role.
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>

      <div class="card shadow border-0">
        <div class="card-header bg-primary text-white text-center py-3">
          <h4 class="mb-0">Admin Login</h4>
        </div>
        <div class="card-body p-4">
          
          <form action="login.php" method="post">
            
            <div class="mb-3">
              <label class="form-label fw-bold">Role</label>
              <select class="form-select" name="role" required>
                <option value="" selected disabled>Select your role</option>
                <option value="admin">Admin</option>
                <option value="staff">Staff</option>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold">Username</label>
              <input type="text" class="form-control" name="username" placeholder="Enter your Username" required>
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold">Password</label>
              <input type="password" class="form-control" name="password" placeholder="Enter password" required>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2" name="submit">Login</button>

          </form>
          
        </div>
      </div>
      
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>