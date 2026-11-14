<?php
session_start();
require_once 'config.php';

// Only allow admins (role_id = 3 for your Admin User)
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header("Location: ../login.php");
    exit();
}

$users = $conn->query("
    SELECT u.id, u.name, u.email, r.name AS role, u.created_at
    FROM users u
    LEFT JOIN roles r ON u.role_id = r.id
    ORDER BY u.id ASC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Users - StudentSphere</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #2c3e50;
      --accent: #f39c12;
      --secondary: #27ae60;
      --light: #f4f6f7;
      --dark: #2c3e50;
    }
    body {
      font-family: 'Poppins', sans-serif;
      background-color: var(--light);
      color: #333;
    }
    .sidebar {
      height: 100vh;
      background: var(--primary);
      color: #fff;
      position: fixed;
      top: 0;
      left: 0;
      width: 240px;
      padding-top: 20px;
      box-shadow: 3px 0 10px rgba(0,0,0,0.1);
      z-index: 1000;
      overflow-y: auto;
    }
    .sidebar a {
      display: block;
      padding: 12px 20px;
      color: #ecf0f1;
      text-decoration: none;
      transition: all 0.3s;
      border-left: 4px solid transparent;
    }
    .sidebar a:hover, .sidebar a.active {
      background: #34495e;
      color: var(--accent);
      border-left: 4px solid var(--accent);
    }
    .content {
      margin-left: 240px;
      padding: 20px;
    }
    .navbar {
      background: #fff;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      padding: 15px 20px;
    }
    .footer {
      background: var(--primary);
      color: #bdc3c7;
      text-align: center;
      padding: 15px;
      margin-top: 40px;
      border-radius: 5px;
    }
    .admin-card {
      background: white;
      border-radius: 10px;
      overflow: hidden;
      transition: transform 0.3s, box-shadow 0.3s;
      box-shadow: 0 5px 15px rgba(0,0,0,0.05);
      height: 100%;
      text-align: center;
      padding: 25px 15px;
    }
    .admin-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    .admin-icon {
      font-size: 2.5rem;
      margin-bottom: 15px;
      display: block;
    }
    .btn-primary { background-color: var(--primary); border-color: var(--primary); }
    .btn-primary:hover { background-color: #1a2530; border-color: #1a2530; }
    .btn-accent { background-color: var(--accent); border-color: var(--accent); color: white; }
    .btn-accent:hover { background-color: #e67e22; border-color: #e67e22; color: white; }
    .section-title {
      position: relative; padding-bottom: 15px; margin-bottom: 30px; font-weight: 700;
    }
    .section-title:after {
      content: ''; position: absolute; bottom: 0; left: 0; width: 60px; height: 4px;
      background: var(--accent); border-radius: 2px;
    }
    .logo-text {
      font-weight: 700; font-size: 1.5rem; text-align: center;
      margin-bottom: 30px; color: white; padding: 0 15px;
    }
    .tagline { font-size: 0.9rem; color: #bdc3c7; text-align: center; margin-bottom: 30px; }
    .stat-card {
      background: white; border-radius: 10px; padding: 20px; text-align: center;
      box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    .stat-number { font-size: 2.5rem; font-weight: 700; color: var(--primary); margin-bottom: 0; }
    .stat-label { color: #7f8c8d; font-size: 0.9rem; }
    .admin-table {
      background: white; border-radius: 10px; overflow: hidden;
      box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    .admin-table th { background-color: var(--primary); color: white; }
    @media (max-width: 992px) {
      .sidebar { width: 200px; }
      .content { margin-left: 200px; }
    }
    @media (max-width: 768px) {
      .sidebar { width: 100%; height: auto; position: relative; padding: 10px; }
      .content { margin-left: 0; }
      .logo-text { margin-bottom: 15px; }
    }
  </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
  <div class="text-center mb-3">
    <div style="width: 120px; height: 120px; margin: 0 auto;">
      <img src="logo.png" alt="StudentSphere Logo" style="width: 100%; height: 100%; object-fit: contain; border-radius: 50%;">
    </div>
  </div>
  <div class="logo-text">StudentSphere</div>
  <div class="tagline">Admin Dashboard</div>
  <a href="admin_dashboard.php"><i class="fas fa-home me-2"></i> Dashboard</a>
  <a href="manage_users.php" class="active"><i class="fas fa-users me-2"></i> Manage Users</a>
  <a href="manage_posts.php"><i class="fas fa-newspaper me-2"></i> Manage Posts</a>
  <a href="manage_events.php"><i class="fas fa-calendar me-2"></i> Manage Events</a>
  <a href="manage_categories.php"><i class="fas fa-folder me-2"></i> Manage Categories</a>
  <a href="index.php"><i class="fas fa-globe me-2"></i> View Site</a>
  <a href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
</div>

<!-- Main Content -->
<div class="content">

  <!-- Top Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
    <div class="container-fluid">
      <span class="navbar-brand mb-0 h1">Manage Users</span>
      <div class="d-flex align-items-center">
        <span class="me-3">Welcome, <strong><?php echo htmlspecialchars($_SESSION['name'] ?? 'Admin'); ?></strong> 👑</span>
        <a href="logout.php" class="btn btn-danger btn-sm">🚪 Logout</a>
      </div>
    </div>
  </nav>

  <!-- Page Content -->
  <h2 class="section-title">User Management</h2>
  <p class="text-muted mb-4">View, edit, and manage user accounts and permissions</p>

  <a href="add_user.php" class="btn btn-success mb-4"><i class="fas fa-plus me-2"></i>Add User</a>

  <div class="admin-table">
    <table class="table table-hover">
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Role</th>
          <th>Created At</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while($user = $users->fetch_assoc()): ?>
          <tr>
            <td><?= $user['id'] ?></td>
            <td><?= htmlspecialchars($user['name']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td><?= htmlspecialchars($user['role'] ?? 'Unknown') ?></td>
            <td><?= date("M d, Y", strtotime($user['created_at'])) ?></td>
            <td>
              <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit me-1"></i>Edit</a>
              <a href="delete_user.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?')"><i class="fas fa-trash me-1"></i>Delete</a>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
  
  <!-- Footer -->
  <div class="footer mt-4">
    <p>&copy; <?= date("Y") ?> StudentSphere | Admin Dashboard</p>
  </div>
</div> <!-- end .content -->

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>