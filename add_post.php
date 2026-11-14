<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header("Location: login.php");
    exit();
}

$message = '';

// Fetch categories
$categories_result = $conn->query("SELECT id, name FROM categories");
$categories = [];
if ($categories_result && $categories_result->num_rows > 0) {
    while ($cat = $categories_result->fetch_assoc()) {
        $categories[] = $cat;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $author_id = $_SESSION['user_id'];
    $selected_categories = $_POST['categories'] ?? [];
    $link_url = $_POST['link_url'] ?? '#';

    // Handle main image upload
    $main_image = null;
    if (!empty($_FILES['main_image']['name'])) {
        $upload_dir = 'uploads/posts/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_extension = pathinfo($_FILES['main_image']['name'], PATHINFO_EXTENSION);
        $main_image = 'post_' . time() . '_' . uniqid() . '.' . $file_extension;
        $upload_path = $upload_dir . $main_image;
        
        if (!move_uploaded_file($_FILES['main_image']['tmp_name'], $upload_path)) {
            $message = "Error: Failed to upload main image.";
        }
    }

    if (empty($message)) {
        // DEBUG: Check if posts table has the correct columns
        $debug_check = $conn->query("SHOW COLUMNS FROM posts");
        $columns = [];
        while($col = $debug_check->fetch_assoc()) {
            $columns[] = $col['Field'];
        }
        
        // Check if link_url column exists, if not, use a different query
        if (in_array('link_url', $columns)) {
            $sql = "INSERT INTO posts (title, content, author_id, image, link_url, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("ssiss", $title, $content, $author_id, $main_image, $link_url);
            }
        } else {
            // Fallback if link_url column doesn't exist
            $sql = "INSERT INTO posts (title, content, author_id, image, created_at) VALUES (?, ?, ?, ?, NOW())";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("ssis", $title, $content, $author_id, $main_image);
            }
        }
        
        if ($stmt && $stmt->execute()) {
            $post_id = $conn->insert_id;
            
            // Insert categories
            if (!empty($selected_categories)) {
                // First check if post_categories table exists
                $table_check = $conn->query("SHOW TABLES LIKE 'post_categories'");
                if ($table_check->num_rows > 0) {
                    $category_stmt = $conn->prepare("INSERT INTO post_categories (post_id, category_id) VALUES (?, ?)");
                    foreach ($selected_categories as $cat_id) {
                        $category_stmt->bind_param("ii", $post_id, $cat_id);
                        $category_stmt->execute();
                    }
                    $category_stmt->close();
                }
            }
            
            // Handle gallery images
            if (!empty($_FILES['gallery_images']['name'][0])) {
                // Check if post_images table exists
                $table_check = $conn->query("SHOW TABLES LIKE 'post_images'");
                if ($table_check->num_rows > 0) {
                    $gallery_dir = 'uploads/posts/gallery/';
                    if (!is_dir($gallery_dir)) {
                        mkdir($gallery_dir, 0755, true);
                    }
                    
                    $gallery_stmt = $conn->prepare("INSERT INTO post_images (post_id, image_path) VALUES (?, ?)");
                    
                    foreach ($_FILES['gallery_images']['name'] as $key => $name) {
                        if ($_FILES['gallery_images']['error'][$key] === UPLOAD_ERR_OK) {
                            $file_extension = pathinfo($name, PATHINFO_EXTENSION);
                            $gallery_image = 'gallery_' . time() . '_' . $key . '_' . uniqid() . '.' . $file_extension;
                            $gallery_path = $gallery_dir . $gallery_image;
                            
                            if (move_uploaded_file($_FILES['gallery_images']['tmp_name'][$key], $gallery_path)) {
                                $gallery_stmt->bind_param("is", $post_id, $gallery_image);
                                $gallery_stmt->execute();
                            }
                        }
                    }
                    $gallery_stmt->close();
                }
            }
            
            header("Location: manage_posts.php?success=Post+created+successfully");
            exit();
        } else {
            $error_msg = $stmt ? $stmt->error : $conn->error;
            $message = "Error: " . $error_msg . ". SQL: " . $sql;
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Post - StudentSphere Admin</title>
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
    
    #content {
      min-height: 300px;
    }
    
    .character-count {
      font-size: 0.85rem;
      color: #6c757d;
      text-align: right;
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
    
    .upload-area {
      border: 2px dashed #dee2e6;
      border-radius: 8px;
      padding: 20px;
      text-align: center;
      cursor: pointer;
      transition: all 0.3s;
    }
    
    .upload-area:hover {
      border-color: var(--accent);
      background-color: #f8f9fa;
    }
    
    .upload-icon {
      font-size: 3rem;
      color: #6c757d;
      margin-bottom: 10px;
    }
    
    .gallery-preview {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-top: 15px;
    }
    
    .gallery-item {
      width: 100px;
      height: 80px;
      border-radius: 6px;
      overflow: hidden;
      position: relative;
    }
    
    .gallery-item img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    
    .gallery-item .remove-btn {
      position: absolute;
      top: 5px;
      right: 5px;
      background: rgba(255, 255, 255, 0.8);
      border: none;
      border-radius: 50%;
      width: 24px;
      height: 24px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
    }
    
    .category-checkboxes {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 10px;
      margin-top: 10px;
    }
    
    .category-checkbox {
      background: #f8f9fa;
      padding: 10px;
      border-radius: 6px;
      border: 1px solid #dee2e6;
      transition: all 0.2s;
    }
    
    .category-checkbox:hover {
      background: #e9ecef;
      border-color: var(--accent);
    }
    
    .category-checkbox input[type="checkbox"] {
      margin-right: 8px;
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
      
      .category-checkboxes {
        grid-template-columns: 1fr;
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
        <h2><i class="fas fa-plus-circle me-2"></i> Add New Post</h2>
        <p class="mb-0">Create a new blog post for StudentSphere</p>
      </div>
      <div>
        <a href="manage_posts.php" class="btn btn-light"><i class="fas fa-arrow-left me-1"></i> Back to Posts</a>
      </div>
    </div>
    
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="admin_dashboard.php">Admin Dashboard</a></li>
        <li class="breadcrumb-item"><a href="manage_posts.php">Manage Posts</a></li>
        <li class="breadcrumb-item active" aria-current="page">Add New Post</li>
      </ol>
    </nav>

    <!-- Error Message -->
    <?php if (!empty($message)): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i> <?= htmlspecialchars($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

    <!-- Add Post Form -->
    <div class="admin-card">
      <h3 class="section-title">Create New Post</h3>
      
      <form method="post" action="" enctype="multipart/form-data">
        <div class="mb-4">
          <label for="title" class="form-label">Post Title *</label>
          <input type="text" id="title" name="title" class="form-control form-control-lg" 
                 value="<?= isset($_POST['title']) ? htmlspecialchars($_POST['title']) : '' ?>" 
                 required placeholder="Enter a compelling title for your post">
        </div>
        
        <!-- Link URL -->
        <div class="mb-4">
          <label for="link_url" class="form-label">Link URL</label>
          <input type="url" id="link_url" name="link_url" class="form-control" 
                 value="<?= isset($_POST['link_url']) ? htmlspecialchars($_POST['link_url']) : '#' ?>" 
                 placeholder="https://example.com/related-page">
          <small class="text-muted">Optional: Add a link to a related page</small>
        </div>
        
        <!-- Main Image Upload -->
        <div class="mb-4">
          <label class="form-label">Main Image *</label>
          <div class="upload-area" onclick="document.getElementById('main_image').click()">
            <div class="upload-icon">
              <i class="fas fa-cloud-upload-alt"></i>
            </div>
            <p class="mb-1">Click to upload a main image</p>
            <small class="text-muted">JPG, PNG, GIF, or WEBP (Max: 2MB)</small>
            <input type="file" id="main_image" name="main_image" accept="image/*" class="d-none" 
                   onchange="previewImage(this, 'mainImagePreview')" required>
          </div>
          <div id="mainFileName" class="mt-2 small text-muted"></div>
          <img id="mainImagePreview" class="image-preview" src="" alt="Main image preview">
        </div>
        
        <!-- Gallery Images -->
        <div class="mb-4">
          <label class="form-label">Gallery Images</label>
          <div class="upload-area" onclick="document.getElementById('gallery_images').click()">
            <div class="upload-icon">
              <i class="fas fa-images"></i>
            </div>
            <p class="mb-1">Click to upload multiple gallery images</p>
            <small class="text-muted">Select multiple images (Max: 5MB each)</small>
            <input type="file" id="gallery_images" name="gallery_images[]" accept="image/*" 
                   class="d-none" multiple onchange="previewGalleryImages(this)">
          </div>
          <div id="galleryFileNames" class="mt-2 small text-muted"></div>
          <div id="galleryPreviews" class="gallery-preview"></div>
        </div>
        
        <!-- Categories -->
        <div class="mb-4">
          <label class="form-label">Categories</label>
          <div class="category-checkboxes">
            <?php foreach ($categories as $cat): ?>
              <label class="category-checkbox">
                <input type="checkbox" name="categories[]" value="<?= $cat['id'] ?>">
                <?= htmlspecialchars($cat['name']) ?>
              </label>
            <?php endforeach; ?>
          </div>
        </div>
        
        <!-- Content -->
        <div class="mb-4">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <label for="content" class="form-label">Content *</label>
            <span class="character-count" id="characterCount">0 characters</span>
          </div>
          <textarea id="content" name="content" class="form-control" rows="12" 
                    placeholder="Write your post content here..." 
                    required><?= isset($_POST['content']) ? htmlspecialchars($_POST['content']) : '' ?></textarea>
        </div>

        <div class="button-group mt-4">
          <button type="submit" class="btn btn-success me-2"><i class="fas fa-save me-1"></i> Create Post</button>
          <a href="manage_posts.php" class="btn btn-secondary"><i class="fas fa-times me-1"></i> Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
  // Character count functionality
  const contentTextarea = document.getElementById('content');
  const characterCount = document.getElementById('characterCount');
  
  function updateCharacterCount() {
    const count = contentTextarea.value.length;
    characterCount.textContent = `${count} characters`;
  }
  
  contentTextarea.addEventListener('input', updateCharacterCount);
  updateCharacterCount(); // Initialize count on page load
  
  // Auto-resize textarea as user types
  contentTextarea.addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = (this.scrollHeight) + 'px';
  });
  
  // Main image preview
  function previewImage(input, previewId) {
    const fileName = document.getElementById('mainFileName');
    const preview = document.getElementById(previewId);
    
    if (input.files && input.files[0]) {
      fileName.textContent = input.files[0].name;
      
      const reader = new FileReader();
      reader.onload = function(e) {
        preview.src = e.target.result;
        preview.style.display = 'block';
      }
      reader.readAsDataURL(input.files[0]);
    }
  }
  
  // Gallery images preview
  function previewGalleryImages(input) {
    const fileNames = document.getElementById('galleryFileNames');
    const previews = document.getElementById('galleryPreviews');
    
    previews.innerHTML = '';
    fileNames.textContent = '';
    
    if (input.files && input.files.length > 0) {
      fileNames.textContent = `${input.files.length} file(s) selected`;
      
      for (let i = 0; i < input.files.length; i++) {
        const reader = new FileReader();
        reader.onload = function(e) {
          const galleryItem = document.createElement('div');
          galleryItem.className = 'gallery-item';
          galleryItem.innerHTML = `
            <img src="${e.target.result}" alt="Gallery preview">
            <button type="button" class="remove-btn" onclick="removeGalleryImage(this)">
              <i class="fas fa-times"></i>
            </button>
          `;
          previews.appendChild(galleryItem);
        }
        reader.readAsDataURL(input.files[i]);
      }
    }
  }
  
  // Remove gallery image from preview
  function removeGalleryImage(btn) {
    const galleryItem = btn.closest('.gallery-item');
    galleryItem.remove();
    
    // Update the file input (this would need a more complex solution to actually remove from FileList)
    const fileNames = document.getElementById('galleryFileNames');
    const previews = document.getElementById('galleryPreviews');
    fileNames.textContent = `${previews.children.length} file(s) remaining`;
  }
  
  // Trigger auto-resize on page load
  setTimeout(() => {
    contentTextarea.style.height = 'auto';
    contentTextarea.style.height = (contentTextarea.scrollHeight) + 'px';
  }, 100);
</script>
</body>
</html>