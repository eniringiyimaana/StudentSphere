<?php

require_once 'config.php'; // DB connection
?>

<!-- Sidebar -->
<div class="sidebar">
  <div class="text-center mb-3">
    <div style="width: 120px; height: 120px; margin: 0 auto;">
      <img src="logo.png" alt="StudentSphere Logo" style="width: 100%; height: 100%; object-fit: contain; border-radius: 50%;">
    </div>
  </div>
  <div class="logo-text">StudentSphere</div>
  <div class="tagline">Stay Informed. Stay Involved.</div>
  
  <a href="index.php" class="active"><i class="fas fa-home me-2"></i> Home</a>
  <a href="calendar.php"><i class="fas fa-calendar-alt me-2"></i> Calendar</a>
  <a href="about.php"><i class="fas fa-info-circle me-2"></i> About</a>
  <a href="contact.php"><i class="fas fa-envelope me-2"></i> Contact</a>
  <a href="join-club.php"><i class="fas fa-users me-2"></i> Join a Club</a>
  <a href="campus-map.php"><i class="fas fa-map-marked-alt me-2"></i> Campus Map</a>

  <?php if(isset($_SESSION['user_id'])): ?>
    <a href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout (<?= htmlspecialchars($_SESSION['name']) ?>)</a>
  <?php else: ?>
    <a href="login.php"><i class="fas fa-sign-in-alt me-2"></i> Login</a>
  <?php endif; ?>
</div>
