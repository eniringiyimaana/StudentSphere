<?php
session_start();
require_once 'config.php';

// Restrict to admin (role_id = 3, adjust if needed)
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header("Location: login.php");
    exit();
}

// Fetch statistics
$totalUsers = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'] ?? 0;
$totalPosts = $conn->query("SELECT COUNT(*) AS total FROM posts")->fetch_assoc()['total'] ?? 0;
$totalEvents = $conn->query("SELECT COUNT(*) AS total FROM events WHERE event_date >= NOW()")->fetch_assoc()['total'] ?? 0;
$totalCategories = $conn->query("SELECT COUNT(*) AS total FROM categories")->fetch_assoc()['total'] ?? 0;

// Check if activity_log table exists
$activityTableCheck = $conn->query("SHOW TABLES LIKE 'activity_log'");
$activity = [];

if ($activityTableCheck->num_rows > 0) {
    // Fetch recent activity (last 10)
    $activityResult = $conn->query("
        SELECT a.*, u.name 
        FROM activity_log a
        JOIN users u ON a.user_id = u.id
        ORDER BY a.created_at DESC
        LIMIT 10
    ");
    if ($activityResult) {
        $activity = $activityResult->fetch_all(MYSQLI_ASSOC);
    }
} else {
    // Create some dummy activity data if table doesn't exist
    $activity = [
        ['name' => 'System', 'action' => 'Login', 'item' => 'Admin Dashboard', 'created_at' => date('Y-m-d H:i:s')],
        ['name' => 'System', 'action' => 'Table Check', 'item' => 'Activity log table not found', 'created_at' => date('Y-m-d H:i:s', strtotime('-1 hour'))]
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - StudentSphere</title>
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
  <a href="admin_dashboard.php" class="active"><i class="fas fa-home me-2"></i> Dashboard</a>
  <a href="manage_users.php"><i class="fas fa-users me-2"></i> Manage Users</a>
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
      <span class="navbar-brand mb-0 h1">Admin Dashboard</span>
      <div class="d-flex align-items-center">
        <span class="me-3">Welcome, <strong><?php echo htmlspecialchars($_SESSION['name'] ?? 'Admin'); ?></strong> 👑</span>
        <a href="logout.php" class="btn btn-danger btn-sm">🚪 Logout</a>
      </div>
    </div>
  </nav>

  <!-- Stats Section -->
  <div class="row mb-5">
    <div class="col-md-3 col-6 mb-3">
      <div class="stat-card">
        <p class="stat-number"><?= $totalUsers ?></p>
        <p class="stat-label">Total Users</p>
      </div>
    </div>
    <div class="col-md-3 col-6 mb-3">
      <div class="stat-card">
        <p class="stat-number"><?= $totalPosts ?></p>
        <p class="stat-label">Active Posts</p>
      </div>
    </div>
    <div class="col-md-3 col-6 mb-3">
      <div class="stat-card">
        <p class="stat-number"><?= $totalEvents ?></p>
        <p class="stat-label">Upcoming Events</p>
      </div>
    </div>
    <div class="col-md-3 col-6 mb-3">
      <div class="stat-card">
        <p class="stat-number"><?= $totalCategories ?></p>
        <p class="stat-label">Categories</p>
      </div>
    </div>
  </div>

  <!-- Management Tools -->
  <h2 class="section-title">Management Tools</h2>
  <div class="row mb-5">
    <div class="col-md-3 mb-4">
      <div class="admin-card">
        <i class="fas fa-users admin-icon" style="color: #3498db;"></i>
        <h4>Manage Users</h4>
        <p>View, edit, and manage user accounts and permissions</p>
        <a href="manage_users.php" class="btn btn-primary mt-2">Manage Users</a>
      </div>
    </div>
    <div class="col-md-3 mb-4">
      <div class="admin-card">
        <i class="fas fa-newspaper admin-icon" style="color: #2ecc71;"></i>
        <h4>Manage Posts</h4>
        <p>Review, approve, and manage content posts</p>
        <a href="manage_posts.php" class="btn btn-success mt-2">Manage Posts</a>
      </div>
    </div>
    <div class="col-md-3 mb-4">
      <div class="admin-card">
        <i class="fas fa-calendar admin-icon" style="color: #f39c12;"></i>
        <h4>Manage Events</h4>
        <p>Create and manage events and activities</p>
        <a href="manage_events.php" class="btn btn-warning mt-2">Manage Events</a>
      </div>
    </div>
    <div class="col-md-3 mb-4">
      <div class="admin-card">
        <i class="fas fa-folder admin-icon" style="color: #9b59b6;"></i>
        <h4>Manage Categories</h4>
        <p>Organize content with categories and tags</p>
        <a href="manage_categories.php" class="btn btn-info mt-2">Manage Categories</a>
      </div>
    </div>
  </div>

  <!-- Recent Activity Section -->
  <!-- Recent Activity Section -->
<h2 class="section-title">Recent Activity</h2>
<div class="admin-table">
  <table class="table table-hover">
    <thead>
      <tr>
        <th>User</th>
        <th>Action</th>
        <th>Item</th>
        <th>Time</th>
      </tr>
    </thead>
    <tbody>
      <?php 
      // Debug: Check if activity_log table exists and has data
      $tableCheck = $conn->query("SHOW TABLES LIKE 'activity_log'");
      if ($tableCheck->num_rows > 0) {
          $dataCheck = $conn->query("SELECT COUNT(*) as count FROM activity_log");
          $row = $dataCheck->fetch_assoc();
          $hasData = $row['count'] > 0;
      } else {
          $hasData = false;
      }
      
      if ($hasData && !empty($activity)): ?>
        <?php foreach ($activity as $act): ?>
          <tr>
            <td><?= htmlspecialchars($act['name'] ?? 'Unknown User') ?></td>
            <td><?= htmlspecialchars($act['action']) ?></td>
            <td><?= htmlspecialchars($act['item']) ?></td>
            <td><?= date("M d, Y H:i", strtotime($act['created_at'])) ?></td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="4" class="text-center">
            <?php if (!$hasData): ?>
              <div class="text-muted">
                <i class="fas fa-info-circle me-2"></i>
                Activity log is empty. No recent activities to display.
              </div>
            <?php else: ?>
              <div class="text-muted">
                <i class="fas fa-info-circle me-2"></i>
                No recent activity found.
              </div>
            <?php endif; ?>
          </td>
        </tr>
      <?php endif; ?>
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