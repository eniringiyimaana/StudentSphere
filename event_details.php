<?php
session_start();
require_once 'config.php';

if (!isset($_GET['id'])) {
    die("Event not found!");
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();

if (!$event) {
    die("Event not found!");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($event['title']) ?> - StudentSphere Event</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --primary: #4e73df;
      --secondary: #6f42c1;
      --success: #1cc88a;
      --info: #36b9cc;
      --warning: #f6c23e;
      --danger: #e74a3b;
      --light: #f8f9fc;
      --dark: #5a5c69;
    }
    
    body {
      background-color: #f8f9fc;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: #5a5c69;
    }
    
    .event-header {
      background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
      color: white;
      border-radius: 10px;
      padding: 2rem;
      margin-bottom: 2rem;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }
    
    .event-details-card {
      background: white;
      border-radius: 10px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
      padding: 2rem;
      margin-bottom: 2rem;
    }
    
    .detail-item {
      display: flex;
      align-items: center;
      margin-bottom: 1.5rem;
    }
    
    .detail-icon {
      background: var(--light);
      width: 50px;
      height: 50px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 1rem;
      color: var(--primary);
      font-size: 1.2rem;
    }
    
    .detail-content h5 {
      margin-bottom: 0.2rem;
      color: var(--dark);
    }
    
    .event-description {
      line-height: 1.8;
      font-size: 1.1rem;
    }
    
    .action-buttons {
      display: flex;
      gap: 1rem;
      margin-top: 2rem;
    }
    
    .btn-primary {
      background: var(--primary);
      border: none;
      padding: 0.6rem 1.5rem;
    }
    
    .btn-primary:hover {
      background: #3a5fc8;
    }
    
    .btn-outline-primary {
      border-color: var(--primary);
      color: var(--primary);
    }
    
    .btn-outline-primary:hover {
      background: var(--primary);
    }
    
    .back-btn {
      margin-top: 2rem;
    }
    
    .event-image {
      width: 100%;
      border-radius: 10px;
      margin-bottom: 1.5rem;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }
    
    @media (max-width: 768px) {
      .action-buttons {
        flex-direction: column;
      }
    }
  </style>
</head>
<body>
  <div class="container py-5">
    <!-- Event Header -->
    <div class="event-header text-center">
      <h1 class="display-4 fw-bold"><?= htmlspecialchars($event['title']) ?></h1>
      <p class="lead mb-0">Organized by StudentSphere</p>
    </div>
    
    <div class="row">
      <div class="col-lg-8">
        <!-- Event Details Card -->
        <div class="event-details-card">
          <h3 class="mb-4">Event Details</h3>
          
          <?php if (!empty($event['image'])): ?>
          <img src="<?= htmlspecialchars($event['image']) ?>" alt="<?= htmlspecialchars($event['title']) ?>" class="event-image">
          <?php endif; ?>
          
          <!-- Date -->
          <div class="detail-item">
            <div class="detail-icon">
              <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="detail-content">
              <h5>Date & Time</h5>
              <p class="mb-0"><?= date("l, F j, Y • h:i A", strtotime($event['event_date'])) ?></p>
            </div>
          </div>
          
          <!-- Location -->
          <div class="detail-item">
            <div class="detail-icon">
              <i class="fas fa-map-marker-alt"></i>
            </div>
            <div class="detail-content">
              <h5>Location</h5>
              <p class="mb-0"><?= htmlspecialchars($event['location']) ?></p>
            </div>
          </div>
          
          <!-- Description -->
          <div class="detail-item">
            <div class="detail-icon">
              <i class="fas fa-info-circle"></i>
            </div>
            <div class="detail-content">
              <h5>Description</h5>
              <div class="event-description"><?= nl2br(htmlspecialchars($event['description'])) ?></div>
            </div>
          </div>
          
          <!-- Action Buttons -->
          <div class="action-buttons">
            <button class="btn btn-primary">
              <i class="fas fa-calendar-plus me-2"></i>Add to Calendar
            </button>
            <button class="btn btn-outline-primary">
              <i class="fas fa-share-alt me-2"></i>Share Event
            </button>
            <button class="btn btn-outline-primary">
              <i class="fas fa-heart me-2"></i>Save for Later
            </button>
          </div>
        </div>
      </div>
      
      <div class="col-lg-4">
        <!-- Additional Info Card -->
        <div class="event-details-card">
          <h4 class="mb-4">Event Information</h4>
          
          <div class="mb-4">
            <h6><i class="fas fa-users me-2 text-primary"></i>Attendance</h6>
            <div class="progress mb-2" style="height: 10px;">
              <div class="progress-bar bg-success" role="progressbar" style="width: 75%;" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <p class="small text-muted">45 out of 60 spots filled</p>
          </div>
          
          <div class="mb-4">
            <h6><i class="fas fa-tag me-2 text-primary"></i>Category</h6>
            <span class="badge bg-primary">Campus Event</span>
          </div>
          
          <div class="mb-4">
            <h6><i class="fas fa-clock me-2 text-primary"></i>Duration</h6>
            <p class="mb-0">Approx. 2 hours</p>
          </div>
          
          <div class="mb-4">
            <h6><i class="fas fa-user-check me-2 text-primary"></i>Organizer</h6>
            <p class="mb-0">Student Activities Board</p>
          </div>
          
          <div class="text-center mt-4">
            <button class="btn btn-primary w-100 py-2">
              <i class="fas fa-check-circle me-2"></i>Register for Event
            </button>
            <p class="small text-muted mt-2">Registration closes 24 hours before the event</p>
          </div>
        </div>
        
        <!-- Related Events -->
        <div class="event-details-card">
          <h4 class="mb-4">You Might Also Like</h4>
          
          <div class="card mb-3 border-0 shadow-sm">
            <div class="card-body">
              <h6 class="card-title">Spring Festival</h6>
              <p class="card-text small text-muted">April 15, 2023 • 2:00 PM</p>
              <a href="#" class="btn btn-sm btn-outline-primary">View Details</a>
            </div>
          </div>
          
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <h6 class="card-title">Career Fair</h6>
              <p class="card-text small text-muted">May 5, 2023 • 10:00 AM</p>
              <a href="#" class="btn btn-sm btn-outline-primary">View Details</a>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Back Button -->
    <div class="text-center back-btn">
      <a href="index.php" class="btn btn-outline-primary">
        <i class="fas fa-arrow-left me-2"></i>Back to Home
      </a>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>