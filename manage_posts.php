<?php
session_start();
require_once 'config.php';

// Only admins
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header("Location: login.php");
    exit();
}

// Fetch posts with author information
$sql = "SELECT p.id, p.title, p.created_at, p.image, p.image_url, u.name AS author_name, u.id AS author_id
        FROM posts p 
        LEFT JOIN users u ON p.author_id = u.id
        ORDER BY p.created_at DESC";
$result = $conn->query($sql);

// Handle delete action
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $delete_stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
    $delete_stmt->bind_param("i", $id);
    
    if ($delete_stmt->execute()) {
        $message = "success:Post deleted successfully!";
        header("Location: manage_posts.php?success=Post+deleted+successfully");
        exit();
    } else {
        $message = "error:Error deleting post: " . $conn->error;
        header("Location: manage_posts.php?error=Error+deleting+post");
        exit();
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Posts - StudentSphere Admin</title>
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
    
    .admin-container {
      max-width: 1400px;
      margin: 0 auto;
    }
    
    .admin-header {
      background: var(--primary);
      color: white;
      padding: 15px 20px;
      border-radius: 10px;
      margin-bottom: 25px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    
    .admin-card {
      background: white;
      border-radius: 10px;
      padding: 25px;
      margin-bottom: 25px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    
    .btn-primary {
      background-color: var(--primary);
      border-color: var(--primary);
    }
    
    .btn-primary:hover {
      background-color: #1a2530;
      border-color: #1a2530;
    }
    
    .btn-success {
      background-color: var(--secondary);
      border-color: var(--secondary);
    }
    
    .btn-warning {
      background-color: var(--accent);
      border-color: var(--accent);
      color: white;
    }
    
    .section-title {
      position: relative;
      padding-bottom: 15px;
      margin-bottom: 25px;
      font-weight: 700;
    }
    
    .section-title:after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 60px;
      height: 4px;
      background: var(--accent);
      border-radius: 2px;
    }
    
    .action-buttons .btn {
      margin-right: 5px;
    }
    
    .table th {
      background-color: var(--primary);
      color: white;
    }
    
    .breadcrumb {
      background-color: transparent;
      padding: 0;
      margin-bottom: 20px;
    }
    
    .form-label {
      font-weight: 500;
    }
    
    .alert {
      border-radius: 8px;
      padding: 12px 15px;
    }
    
    .logo-text {
      font-weight: 700;
      font-size: 1.5rem;
      text-align: center;
      margin-bottom: 30px;
      color: white;
      padding: 0 15px;
    }
    
    .tagline {
      font-size: 0.9rem;
      color: #bdc3c7;
      text-align: center;
      margin-bottom: 30px;
    }
    
    .post-title {
      font-weight: 600;
      color: var(--primary);
      max-width: 250px;
    }
    
    .post-date {
      font-size: 0.85rem;
      color: #6c757d;
    }
    
    .post-author {
      font-weight: 500;
    }
    
    .post-image {
      width: 80px;
      height: 50px;
      object-fit: cover;
      border-radius: 4px;
    }
    
    .no-image {
      width: 80px;
      height: 50px;
      background-color: #f8f9fa;
      border-radius: 4px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #6c757d;
      font-size: 0.8rem;
    }
    
    @media (max-width: 992px) {
      .sidebar {
        width: 200px;
      }
      .content {
        margin-left: 200px;
      }
    }
    
    @media (max-width: 768px) {
      .sidebar {
        width: 100%;
        height: auto;
        position: relative;
        padding: 10px;
      }
      .content {
        margin-left: 0;
      }
      .logo-text {
        margin-bottom: 15px;
      }
      
      .admin-header {
        flex-direction: column;
        text-align: center;
        gap: 15px;
      }
      
      .action-buttons .btn {
        margin-bottom: 5px;
        display: block;
        width: 100%;
      }
      
      .table-responsive {
        overflow-x: auto;
      }
      
      .post-title {
        max-width: 150px;
      }
    }
  </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
  <div class="text-center mb-3">
    <div style="background: linear-gradient(135deg, #f39c12, #e74c3c); width: 120px; height: 120px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
      <span style="color: white; font-weight: 700; font-size: 1.8rem;">SS</span>
    </div>
  </div>
  <div class="logo-text">StudentSphere</div>
  <div class="tagline">Admin Dashboard</div>
  <a href="admin_dashboard.php"><i class="fas fa-home me-2"></i> Dashboard</a>
  <a href="manage_users.php"><i class="fas fa-users me-2"></i> Manage Users</a>
  <a href="manage_posts.php" class="active"><i class="fas fa-newspaper me-2"></i> Manage Posts</a>
  <a href="manage_events.php"><i class="fas fa-calendar me-2"></i> Manage Events</a>
  <a href="manage_categories.php"><i class="fas fa-folder me-2"></i> Manage Categories</a>
  <a href="manage_slides.php"><i class="fas fa-images me-2"></i> Manage Slides</a>
  <a href="../index.php"><i class="fas fa-globe me-2"></i> View Site</a>
  <a href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
</div>

<!-- Main Content -->
<div class="content">
  <div class="admin-container">
    <!-- Header -->
    <div class="admin-header">
      <div>
        <h2><i class="fas fa-newspaper me-2"></i> Manage Posts</h2>
        <p class="mb-0">Manage blog posts and content for StudentSphere</p>
      </div>
      <div>
        <a href="admin_dashboard.php" class="btn btn-light me-2"><i class="fas fa-arrow-left me-1"></i> Back to Dashboard</a>
      </div>
    </div>
    
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="admin_dashboard.php">Admin Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Manage Posts</li>
      </ol>
    </nav>

    <!-- Success/Error Messages -->
    <?php if (isset($_GET['success'])): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i> <?php echo htmlspecialchars(urldecode($_GET['success'])); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['error'])): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i> <?php echo htmlspecialchars(urldecode($_GET['error'])); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

    <!-- Add New Post Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h3 class="section-title mb-0">Blog Posts</h3>
      <a href="add_post.php" class="btn btn-success"><i class="fas fa-plus-circle me-1"></i> Add New Post</a>
    </div>

    <!-- Posts Table -->
    <div class="admin-card">
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>ID</th>
              <th>Image</th>
              <th>Title</th>
              <th>Author</th>
              <th>Created At</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
              <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                  <td><?= $row['id'] ?></td>
                  <td>
                    <?php if (!empty($row['image_url'])): ?>
                      <img src="<?= htmlspecialchars($row['image_url']) ?>" alt="Post image" class="post-image">
                    <?php elseif (!empty($row['image'])): ?>
                      <div class="no-image" title="<?= htmlspecialchars($row['image']) ?>">
                        <i class="fas fa-image"></i>
                      </div>
                    <?php else: ?>
                      <div class="no-image">
                        <i class="fas fa-times"></i>
                      </div>
                    <?php endif; ?>
                  </td>
                  <td>
                    <div class="post-title" title="<?= htmlspecialchars($row['title']) ?>">
                      <?= htmlspecialchars($row['title']) ?>
                    </div>
                  </td>
                  <td>
                    <div class="post-author">
                      <?= !empty($row['author_name']) ? htmlspecialchars($row['author_name']) : 'Unknown' ?>
                    </div>
                    <?php if (!empty($row['author_id'])): ?>
                      <small class="text-muted">ID: <?= $row['author_id'] ?></small>
                    <?php endif; ?>
                  </td>
                  <td>
                    <div class="post-date"><?= date('M j, Y', strtotime($row['created_at'])) ?></div>
                    <small class="text-muted"><?= date('g:i A', strtotime($row['created_at'])) ?></small>
                  </td>
                  <td class="action-buttons">
                    <a href="edit_post.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit me-1"></i> Edit</a>
                    <a href="manage_posts.php?delete=<?= $row['id'] ?>" 
                       onclick="return confirm('Are you sure you want to delete this post? This action cannot be undone.')" 
                       class="btn btn-sm btn-danger">
                      <i class="fas fa-trash me-1"></i> Delete
                    </a>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" class="text-center py-4">
                  <div class="py-3">
                    <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                    <h5>No posts found</h5>
                    <p class="text-muted">Get started by creating your first blog post.</p>
                    <a href="add_post.php" class="btn btn-primary mt-2">Create Your First Post</a>
                  </div>
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
      
      <?php if ($result && $result->num_rows > 0): ?>
        <div class="d-flex justify-content-between align-items-center mt-3">
          <div>Showing <?= $result->num_rows ?> post<?= $result->num_rows === 1 ? '' : 's' ?></div>
          <!-- Pagination would go here if needed -->
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>