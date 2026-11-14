<?php
session_start();
include 'config.php';

// Restrict access to admins only (adjust role_id as per your system)
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header("Location: login.php");
    exit();
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $image_url = $_POST['image_url'];
    $link_url = $_POST['link_url'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("INSERT INTO slides (title, description, image_url, link_url, status, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("sssss", $title, $description, $image_url, $link_url, $status);

    if ($stmt->execute()) {
        $message = "✅ Slide added successfully!";
        // Clear form fields
        $title = $description = $image_url = $link_url = '';
        $status = 'active';
    } else {
        $message = "❌ Error: " . $conn->error;
    }

    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add New Slide - StudentSphere Admin</title>
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
      max-width: 800px;
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
    
    .btn-secondary {
      background-color: #6c757d;
      border-color: #6c757d;
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
    
    .image-preview {
      width: 100%;
      max-height: 200px;
      object-fit: cover;
      border-radius: 8px;
      margin-top: 10px;
      display: none;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
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
      
      .button-group {
        display: flex;
        flex-direction: column;
        gap: 10px;
      }
      
      .button-group .btn {
        width: 100%;
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
  <a href="manage_posts.php"><i class="fas fa-newspaper me-2"></i> Manage Posts</a>
  <a href="manage_events.php"><i class="fas fa-calendar me-2"></i> Manage Events</a>
  <a href="manage_categories.php"><i class="fas fa-folder me-2"></i> Manage Categories</a>
  <a href="manage_slides.php" class="active"><i class="fas fa-images me-2"></i> Manage Slides</a>
  <a href="../index.php"><i class="fas fa-globe me-2"></i> View Site</a>
  <a href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
</div>

<!-- Main Content -->
<div class="content">
  <div class="admin-container">
    <!-- Header -->
    <div class="admin-header">
      <div>
        <h2><i class="fas fa-plus-circle me-2"></i> Add New Slide</h2>
        <p class="mb-0">Add a new slide to the homepage slider</p>
      </div>
      <div>
        <a href="manage_slides.php" class="btn btn-light"><i class="fas fa-arrow-left me-1"></i> Back to Slides</a>
      </div>
    </div>
    
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="admin_dashboard.php">Admin Dashboard</a></li>
        <li class="breadcrumb-item"><a href="manage_slides.php">Manage Slides</a></li>
        <li class="breadcrumb-item active" aria-current="page">Add New Slide</li>
      </ol>
    </nav>

    <!-- Success/Error Messages -->
    <?php if ($message): ?>
      <div class="alert alert-<?php echo strpos($message, '✅') !== false ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
        <?= $message ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

    <!-- Add Slide Form -->
    <div class="admin-card">
      <h3 class="section-title">Slide Details</h3>
      
      <form method="post" action="">
        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="title" class="form-label">Slide Title</label>
            <input type="text" class="form-control" id="title" name="title" value="<?= isset($title) ? htmlspecialchars($title) : '' ?>" required>
          </div>

          <div class="col-md-6 mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-control" id="status" name="status" required>
              <option value="active" <?= (isset($status) && $status == 'active') ? 'selected' : '' ?>>Active</option>
              <option value="inactive" <?= (isset($status) && $status == 'inactive') ? 'selected' : '' ?>>Inactive</option>
            </select>
          </div>
        </div>

        <div class="mb-3">
          <label for="description" class="form-label">Description</label>
          <textarea class="form-control" id="description" name="description" rows="3"><?= isset($description) ? htmlspecialchars($description) : '' ?></textarea>
        </div>

        <div class="mb-3">
          <label for="image_url" class="form-label">Image URL</label>
          <input type="url" class="form-control" id="image_url" name="image_url" value="<?= isset($image_url) ? htmlspecialchars($image_url) : '' ?>" required 
                 placeholder="https://example.com/image.jpg">
          <small class="text-muted">Paste a full image link (e.g., from Unsplash or uploaded path).</small>
          
          <!-- Image Preview -->
          <img id="imagePreview" class="image-preview" src="" alt="Image preview">
        </div>

        <div class="mb-3">
          <label for="link_url" class="form-label">Link URL</label>
          <input type="url" class="form-control" id="link_url" name="link_url" value="<?= isset($link_url) ? htmlspecialchars($link_url) : '#' ?>" 
                 placeholder="https://example.com/page">
        </div>

        <div class="button-group mt-4">
          <button type="submit" class="btn btn-success me-2"><i class="fas fa-save me-1"></i> Add Slide</button>
          <a href="manage_slides.php" class="btn btn-secondary"><i class="fas fa-times me-1"></i> Cancel</a>
        </div>
      </form>
    </div>
    
    <!-- Sample Image URLs -->
    <div class="admin-card">
      <h4 class="mb-3"><i class="fas fa-lightbulb me-2"></i> Sample Image URLs</h4>
      <p class="text-muted">You can use these sample URLs for testing:</p>
      <ul class="list-group">
        <li class="list-group-item">
          <small>https://images.unsplash.com/photo-1543269865-cbf427effbad?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&h=600&q=80</small>
        </li>
        <li class="list-group-item">
          <small>https://images.unsplash.com/photo-1523580494863-6f3031224c94?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&h=600&q=80</small>
        </li>
        <li class="list-group-item">
          <small>https://images.unsplash.com/photo-1540575467063-178a50c2df87?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&h=600&q=80</small>
        </li>
      </ul>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
  // Image preview functionality
  document.getElementById('image_url').addEventListener('input', function() {
    const preview = document.getElementById('imagePreview');
    const url = this.value.trim();
    
    if (url) {
      preview.src = url;
      preview.style.display = 'block';
    } else {
      preview.style.display = 'none';
    }
  });
  
  // Trigger input event on page load if there's already a value
  const imageUrlField = document.getElementById('image_url');
  if (imageUrlField.value) {
    imageUrlField.dispatchEvent(new Event('input'));
  }
</script>
</body>
</html>