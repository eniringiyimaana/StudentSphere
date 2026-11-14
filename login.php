<?php
session_start();
include "config.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT id, name, email, password, role_id FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        // Check password (plain text for now, but we'll add password_verify for hashed passwords)
        if ($password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role_id'] = $user['role_id'];

            // Redirect based on role
            if ($user['role_id'] == 3) {
                header("Location: admin-dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $message = "❌ Incorrect password.";
        }
    } else {
        $message = "⚠️ User not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - StudentSphere</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #2c3e50;
      --accent: #f39c12;
      --secondary: #3498db;
      --light: #f4f6f7;
    }
    
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }
    
    .login-card {
      background: white;
      border-radius: 15px;
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      width: 100%;
      max-width: 400px;
    }
    
    .login-header {
      background: var(--primary);
      color: white;
      padding: 25px;
      text-align: center;
    }
    
    .login-body {
      padding: 25px;
    }
    
    .form-label {
      font-weight: 500;
      color: var(--primary);
    }
    
    .btn-login {
      background: var(--secondary);
      border: none;
      padding: 12px;
      font-weight: 600;
      transition: all 0.3s;
    }
    
    .btn-login:hover {
      background: #2980b9;
      transform: translateY(-2px);
    }
    
    .input-group-text {
      background: var(--light);
    }
    
    .login-link {
      color: var(--primary);
      text-decoration: none;
      font-weight: 500;
    }
    
    .login-link:hover {
      color: var(--accent);
      text-decoration: underline;
    }
    
    .password-toggle {
      cursor: pointer;
      background: var(--light);
      border: 1px solid #ced4da;
      border-left: none;
      display: flex;
      align-items: center;
      padding: 0 12px;
      border-radius: 0 0.375rem 0.375rem 0;
    }
  </style>
</head>
<body>
  <div class="login-card">
    <div class="login-header">
      <h3><i class="fas fa-sign-in-alt me-2"></i>Welcome Back</h3>
      <p class="mb-0">Sign in to your StudentSphere account</p>
    </div>
    
    <div class="login-body">
      <?php if (!empty($message)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <?= $message ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>

      <form action="login.php" method="POST">
        <div class="mb-3">
          <label for="email" class="form-label">Email Address</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
            <input type="email" class="form-control" id="email" name="email" required placeholder="Enter your email">
          </div>
        </div>
        
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fas fa-lock"></i></span>
            <input type="password" class="form-control" id="password" name="password" required placeholder="Enter your password">
            <span class="password-toggle" id="togglePassword">
              <i class="fas fa-eye"></i>
            </span>
          </div>
        </div>
        
        <div class="mb-3 form-check">
          <input type="checkbox" class="form-check-input" id="rememberMe">
          <label class="form-check-label" for="rememberMe">Remember me</label>
          <a href="#" class="login-link float-end">Forgot password?</a>
        </div>
        
        <button type="submit" class="btn btn-login w-100">Login</button>
      </form>

      <div class="mt-4 text-center">
        <p>Don't have an account? <a href="register.php" class="login-link">Register here</a></p>
      </div>
      
      <div class="text-center mt-3">
        <small class="text-muted">By logging in, you agree to our <a href="#" class="login-link">Terms</a> and <a href="#" class="login-link">Privacy Policy</a></small>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const togglePassword = document.getElementById('togglePassword');
      const passwordInput = document.getElementById('password');
      
      togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        // Toggle eye icon
        if (type === 'password') {
          togglePassword.innerHTML = '<i class="fas fa-eye"></i>';
        } else {
          togglePassword.innerHTML = '<i class="fas fa-eye-slash"></i>';
        }
      });
    });
  </script>
</body>
</html>