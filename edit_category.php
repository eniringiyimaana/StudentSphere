<?php
session_start();
include 'config.php'; // database connection

// Ensure only admin can access
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header("Location: login.php");
    exit();
}

// Get category ID from URL
if (!isset($_GET['id'])) {
    header("Location: manage_categories.php");
    exit();
}

$id = intval($_GET['id']);

// Fetch category data
$stmt = $conn->prepare("SELECT * FROM categories WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$category = $result->fetch_assoc();

if (!$category) {
    echo "<div class='alert alert-danger'>Category not found!</div>";
    exit();
}

// Handle update
if (isset($_POST['update_category'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $icon = $_POST['icon'];
    $link = $_POST['link'];

    $updateStmt = $conn->prepare("UPDATE categories SET name=?, description=?, icon=?, link=? WHERE id=?");
    $updateStmt->bind_param("ssssi", $name, $description, $icon, $link, $id);
    
    if ($updateStmt->execute()) {
        $success_message = "Category updated successfully!";
        // Refresh category data
        $category['name'] = $name;
        $category['description'] = $description;
        $category['icon'] = $icon;
        $category['link'] = $link;
    } else {
        $error_message = "Error updating category: " . $conn->error;
    }
    
    $updateStmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Category - StudentSphere Admin</title>
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
      max-width: 1000px;
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
    
    .icon-preview {
      font-size: 2rem;
      text-align: center;
      margin: 15px 0;
      padding: 15px;
      background-color: #f8f9fa;
      border-radius: 8px;
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
  <a href="admin-dashboard.php"><i class="fas fa-home me-2"></i> Dashboard</a>
  <a href="manage_users.php"><i class="fas fa-users me-2"></i> Manage Users</a>
  <a href="manage_posts.php"><i class="fas fa-newspaper me-2"></i> Manage Posts</a>
  <a href="manage_events.php"><i class="fas fa-calendar me-2"></i> Manage Events</a>
  <a href="manage_categories.php" class="active"><i class="fas fa-folder me-2"></i> Manage Categories</a>
  <a href="index.php"><i class="fas fa-globe me-2"></i> View Site</a>
  <a href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
</div>

<!-- Main Content -->
<div class="content">
  <div class="admin-container">
    <!-- Header -->
    <div class="admin-header">
      <div>
        <h2><i class="fas fa-edit me-2"></i> Edit Category</h2>
        <p class="mb-0">Update category details for StudentSphere</p>
      </div>
      <div>
        <a href="manage_categories.php" class="btn btn-light me-2"><i class="fas fa-arrow-left me-1"></i> Back to Categories</a>
      </div>
    </div>
    
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="admin-dashboard.php">Admin Dashboard</a></li>
        <li class="breadcrumb-item"><a href="manage_categories.php">Manage Categories</a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit Category</li>
      </ol>
    </nav>

    <!-- Success/Error Messages -->
    <?php if (isset($success_message)): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i> <?php echo $success_message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>
    
    <?php if (isset($error_message)): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error_message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

    <!-- Edit Category Form -->
    <div class="admin-card">
      <h3 class="section-title">Edit Category: <?= htmlspecialchars($category['name']) ?></h3>
      
      <!-- Icon Preview -->
      <div class="icon-preview">
        <?php if (!empty($category['icon'])): ?>
          <i class="<?= htmlspecialchars($category['icon']) ?>" id="iconPreview"></i>
        <?php else: ?>
          <i class="fas fa-folder" id="iconPreview"></i>
        <?php endif; ?>
        <div class="mt-2 text-muted">Icon Preview</div>
      </div>
      
      <form method="POST">
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Category Name</label>
            <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($category['name']) ?>" required>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Icon (FontAwesome class)</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-icons"></i></span>
              <input type="text" class="form-control" name="icon" id="iconInput" value="<?= htmlspecialchars($category['icon']) ?>" placeholder="fas fa-basketball-ball">
            </div>
            <small class="form-text text-muted">Enter FontAwesome icon class (e.g. fas fa-basketball-ball)</small>
          </div>
        </div>
        
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Description</label>
            <textarea class="form-control" name="description" rows="3"><?= htmlspecialchars($category['description']) ?></textarea>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Link</label>
            <div class="input-group">
              <span class="input-group-text">/</span>
              <input type="text" class="form-control" name="link" value="<?= htmlspecialchars($category['link']) ?>" placeholder="sports.php">
            </div>
            <small class="form-text text-muted">Page link for this category</small>
          </div>
        </div>
        
        <div class="button-group mt-4">
          <button type="submit" name="update_category" class="btn btn-success me-2"><i class="fas fa-save me-1"></i> Update Category</button>
          <a href="manage_categories.php" class="btn btn-secondary"><i class="fas fa-times me-1"></i> Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
  // Update icon preview in real-time
  document.getElementById('iconInput').addEventListener('input', function() {
    const iconPreview = document.getElementById('iconPreview');
    const iconClass = this.value.trim();
    
    if (iconClass) {
      // Remove all existing classes
      iconPreview.className = '';
      // Add the new icon class
      iconPreview.classList.add(...iconClass.split(' '));
    } else {
      // Default to folder icon if empty
      iconPreview.className = 'fas fa-folder';
    }
  });
</script>
</body>
</html>