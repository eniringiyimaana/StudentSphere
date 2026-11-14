<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header("Location: ../login.php");
    exit();
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();

if (!$event) {
    die("Event not found!");
}

// Fetch categories for dropdown
$categories_result = $conn->query("SELECT * FROM categories ORDER BY name ASC");

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $event_date = $_POST['event_date'];
    $location = $_POST['location'];
    $category_id = $_POST['category_id'] ?? null;
    $image = $event['image']; // Keep existing image by default

    // Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/events/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        
        $imageName = time() . "_" . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $imageName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        
        // Check if image file is an actual image
        $check = getimagesize($_FILES['image']['tmp_name']);
        if ($check !== false) {
            // Check file size (max 5MB)
            if ($_FILES['image']['size'] > 5000000) {
                $message = "Sorry, your file is too large. Maximum size is 5MB.";
            } elseif (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            } elseif (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                // Delete old image if it exists
                if (!empty($event['image']) && file_exists($event['image'])) {
                    unlink($event['image']);
                }
                $image = $targetFile;
            } else {
                $message = "Sorry, there was an error uploading your file.";
            }
        } else {
            $message = "File is not an image.";
        }
    }

    if (empty($message)) {
        // Check if category_id is provided, otherwise set to NULL
        if (empty($category_id)) {
            $update = $conn->prepare("UPDATE events SET title=?, description=?, event_date=?, location=?, image=?, category_id = NULL WHERE id=?");
            $update->bind_param("sssssi", $title, $description, $event_date, $location, $image, $id);
        } else {
            $update = $conn->prepare("UPDATE events SET title=?, description=?, event_date=?, location=?, image=?, category_id=? WHERE id=?");
            $update->bind_param("sssssii", $title, $description, $event_date, $location, $image, $category_id, $id);
        }

        if ($update->execute()) {
            header("Location: manage_events.php?msg=Event+updated+successfully");
            exit();
        } else {
            $message = "Error: " . $update->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Event - StudentSphere</title>
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
    .btn-primary { background-color: var(--primary); border-color: var(--primary); }
    .btn-primary:hover { background-color: #1a2530; border-color: #1a2530; }
    .btn-success { background-color: var(--secondary); border-color: var(--secondary); }
    .btn-success:hover { background-color: #219653; border-color: #219653; }
    .section-title {
      position: relative; padding-bottom: 15px; margin-bottom: 30px; font-weight: 700;
    }
    .section-title:after {
      content: ''; position: absolute; bottom: 0; left: 0; width: 60px; height: 4px;
      background: var(--accent); border-radius: 2px;
    }
    .form-container {
      background: white;
      border-radius: 10px;
      padding: 25px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    .image-preview {
      max-width: 300px;
      max-height: 200px;
      margin-top: 10px;
      border: 1px solid #ddd;
      border-radius: 5px;
      padding: 5px;
    }
    @media (max-width: 992px) {
      .sidebar { width: 200px; }
      .content { margin-left: 200px; }
    }
    @media (max-width: 768px) {
      .sidebar { width: 100%; height: auto; position: relative; padding: 10px; }
      .content { margin-left: 0; }
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
  <a href="manage_users.php"><i class="fas fa-users me-2"></i> Manage Users</a>
  <a href="manage_posts.php"><i class="fas fa-newspaper me-2"></i> Manage Posts</a>
  <a href="manage_events.php" class="active"><i class="fas fa-calendar me-2"></i> Manage Events</a>
  <a href="manage_categories.php"><i class="fas fa-folder me-2"></i> Manage Categories</a>
  <a href="index.php"><i class="fas fa-globe me-2"></i> View Site</a>
  <a href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
</div>

<!-- Main Content -->
<div class="content">

  <!-- Top Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
    <div class="container-fluid">
      <span class="navbar-brand mb-0 h1">Edit Event</span>
      <div class="d-flex align-items-center">
        <span class="me-3">Welcome, <strong><?php echo htmlspecialchars($_SESSION['name'] ?? 'Admin'); ?></strong> 👑</span>
        <a href="logout.php" class="btn btn-danger btn-sm">🚪 Logout</a>
      </div>
    </div>
  </nav>

  <!-- Page Content -->
  <h2 class="section-title">Edit Event</h2>
  
  <div class="form-container">
    <?php if ($message): ?>
      <div class="alert alert-danger"><?= $message ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
      <div class="mb-3">
        <label class="form-label">Event Title</label>
        <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($event['title']) ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="4" required><?= htmlspecialchars($event['description']) ?></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Date</label>
        <input type="date" name="event_date" class="form-control" value="<?= $event['event_date'] ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Location</label>
        <input type="text" name="location" class="form-control" value="<?= htmlspecialchars($event['location']) ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Category (Optional)</label>
        <select class="form-select" name="category_id">
          <option value="">-- Select Category --</option>
          <?php if ($categories_result->num_rows > 0): ?>
            <?php while ($category = $categories_result->fetch_assoc()): ?>
              <option value="<?= $category['id'] ?>" <?= ($event['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                <?= htmlspecialchars($category['name']) ?>
              </option>
            <?php endwhile; ?>
          <?php endif; ?>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Event Image (Optional)</label>
        <?php if (!empty($event['image'])): ?>
          <div class="mb-2">
            <p>Current Image:</p>
            <img src="<?= htmlspecialchars($event['image']) ?>" class="image-preview" alt="Current event image">
          </div>
        <?php endif; ?>
        <input type="file" name="image" class="form-control" id="imageUpload" accept="image/*">
        <div class="form-text">Upload a new image to replace the current one. Allowed formats: JPG, JPEG, PNG, GIF. Max size: 5MB</div>
        <img id="imagePreview" class="image-preview mt-2" src="#" alt="Image preview" style="display: none;">
      </div>
      <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Update Event</button>
      <a href="manage_events.php" class="btn btn-secondary"><i class="fas fa-times me-2"></i>Cancel</a>
    </form>
  </div>
  
  <!-- Footer -->
  <div class="footer mt-4">
    <p>&copy; <?= date("Y") ?> StudentSphere | Admin Dashboard</p>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Image preview functionality
  document.getElementById('imageUpload').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('imagePreview');
    
    if (file) {
      const reader = new FileReader();
      
      reader.onload = function(e) {
        preview.src = e.target.result;
        preview.style.display = 'block';
      }
      
      reader.readAsDataURL(file);
    } else {
      preview.style.display = 'none';
    }
  });
</script>
</body>
</html>