<?php
session_start();
include "config.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($name) && !empty($email) && !empty($password)) {
        // Check if email exists
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = "⚠️ Email already registered!";
        } else {
            $check->close();

            // Hash password for security
            // $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new student (role_id = 1 for students)
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role_id, created_at) VALUES (?, ?, ?, 1, NOW())");
            if ($stmt) {
                $stmt->bind_param("sss", $name, $email, $password);
                if ($stmt->execute()) {
                    $message = "✅ Registration successful! You can now <a href='login.php' class='alert-link'>login</a>.";
                } else {
                    $message = "❌ Registration failed. Try again.";
                }
                $stmt->close();
            }
        }
    } else {
        $message = "⚠️ Please fill in all fields!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - StudentSphere</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #2c3e50;
      --accent: #f39c12;
      --secondary: #27ae60;
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
    
    .registration-card {
      background: white;
      border-radius: 15px;
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      width: 100%;
      max-width: 450px;
    }
    
    .registration-header {
      background: var(--primary);
      color: white;
      padding: 25px;
      text-align: center;
    }
    
    .registration-body {
      padding: 25px;
    }
    
    .form-label {
      font-weight: 500;
      color: var(--primary);
    }
    
    .btn-register {
      background: var(--secondary);
      border: none;
      padding: 12px;
      font-weight: 600;
      transition: all 0.3s;
    }
    
    .btn-register:hover {
      background: #219653;
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
  </style>
</head>
<body>
  <div class="registration-card">
    <div class="registration-header">
      <h3><i class="fas fa-user-graduate me-2"></i>Student Registration</h3>
      <p class="mb-0">Join the StudentSphere community</p>
    </div>
    
    <div class="registration-body">
      <?php if (!empty($message)): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
          <?= $message ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>

      <form action="register.php" method="POST">
        <div class="mb-3">
          <label for="name" class="form-label">Full Name</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fas fa-user"></i></span>
            <input type="text" class="form-control" id="name" name="name" required placeholder="Enter your full name">
          </div>
        </div>
        
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
            <input type="email" class="form-control" id="email" name="email" required placeholder="Enter your email">
          </div>
        </div>
        
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fas fa-lock"></i></span>
            <input type="password" class="form-control" id="password" name="password" required placeholder="Create a password">
          </div>
          <div class="form-text">Use at least 8 characters with a mix of letters, numbers & symbols</div>
        </div>
        
        <button type="submit" class="btn btn-register w-100">Register</button>
      </form>

      <div class="mt-4 text-center">
        <p>Already have an account? <a href="login.php" class="login-link">Login here</a></p>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>