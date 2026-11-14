<?php 
include 'header.php';
include 'sidebar.php';
include 'config.php'; // database connection

// Fetch latest posts (limit 5)
$postsQuery = $conn->query("
    SELECT id, title, content, image, created_at
    FROM posts
    ORDER BY created_at DESC
    LIMIT 3
");
$posts = $postsQuery ? $postsQuery->fetch_all(MYSQLI_ASSOC) : [];

// Fetch upcoming events (limit 5)
$eventsQuery = $conn->query("
    SELECT id, title, event_date, location, description, image
    FROM events
    WHERE event_date >= NOW()
    ORDER BY event_date ASC
    LIMIT 5
");
$events = $eventsQuery ? $eventsQuery->fetch_all(MYSQLI_ASSOC) : [];

// Fetch counts for stats
function getCount($conn, $table, $where = "") {
    $sql = "SELECT COUNT(*) AS total FROM $table";
    if ($where) $sql .= " WHERE $where";
    $result = $conn->query($sql);
    return ($result && $row = $result->fetch_assoc()) ? $row['total'] : 0;
}

$eventsCount = getCount($conn, "events", "event_date >= NOW()");
$clubsCount = getCount($conn, "clubs");
$studentsCount = getCount($conn, "users", "role_id = 1");
$volunteerCount = getCount($conn, "volunteer_opportunities");

?>

<!-- Hero Slider -->
<?php
$slides = $conn->query("SELECT * FROM slides WHERE status='active' ORDER BY created_at DESC");
?>

<div id="homepageCarousel" class="carousel slide" data-bs-ride="carousel">
  <div class="carousel-inner">
    <?php $first = true; while ($slide = $slides->fetch_assoc()): ?>
      <div class="carousel-item <?= $first ? 'active' : '' ?>">
        <img src="<?= $slide['image_url'] ?>" class="d-block w-100" alt="<?= htmlspecialchars($slide['title']) ?>">
        <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-3">
          <h5><?= htmlspecialchars($slide['title']) ?></h5>
          <p><?= htmlspecialchars($slide['description']) ?></p>
          <?php if ($slide['link_url'] && $slide['link_url'] != '#'): ?>
            <a href="<?= $slide['link_url'] ?>" class="btn btn-primary">Learn More</a>
          <?php endif; ?>
        </div>
      </div>
    <?php $first = false; endwhile; ?>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#homepageCarousel" data-bs-slide="prev">
    <span class="carousel-control-prev-icon"></span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#homepageCarousel" data-bs-slide="next">
    <span class="carousel-control-next-icon"></span>
  </button>
</div>

 <!-- Spacer between slider and stats -->
  <div class="spacer"></div>



<!-- Stats Section -->
<div class="row mb-5">
  <div class="col-md-3 col-6 mb-3">
    <div class="stat-card"><p class="stat-number"><?= $eventsCount ?></p><p class="stat-label">Upcoming Events</p></div>
  </div>
  <div class="col-md-3 col-6 mb-3">
    <div class="stat-card"><p class="stat-number"><?= $clubsCount ?></p><p class="stat-label">Active Clubs</p></div>
  </div>
  <div class="col-md-3 col-6 mb-3">
    <div class="stat-card"><p class="stat-number"><?= $studentsCount ?></p><p class="stat-label">Registered Students</p></div>
  </div>
  <div class="col-md-3 col-6 mb-3">
    <div class="stat-card"><p class="stat-number"><?= $volunteerCount ?></p><p class="stat-label">Volunteer Opportunities</p></div>
  </div>
</div>

<div class="row mb-5">
  <div class="col-md-8">
    <h2 class="section-title">Welcome to StudentSphere</h2>
    <p class="lead">Your portal for all co-curricular activities — sports, debates, charity, and culture at the university.</p>
    <p>StudentSphere connects you with all campus activities, events, and opportunities to get involved.</p>
    
    <!-- Category cards dynamically fetched from database -->
    <div class="row mt-4">
      <?php
      $categoriesQuery = $conn->query("SELECT * FROM categories");
      if ($categoriesQuery) {
          while ($category = $categoriesQuery->fetch_assoc()) {
              echo '<div class="col-md-6 mb-4">
                      <div class="category-card p-4 text-center">
                        <div class="category-icon"><i class="'.htmlspecialchars($category['icon']).'"></i></div>
                        <h4>'.htmlspecialchars($category['name']).'</h4>
                        <p>'.htmlspecialchars($category['description']).'</p>
                        <a href="'.htmlspecialchars($category['link']).'" class="btn btn-primary mt-2">Explore '.htmlspecialchars($category['name']).'</a>
                      </div>
                    </div>';
          }
      }
      ?>
    </div>
  </div>
  
  <div class="col-md-4">
    <!-- Upcoming Events Sidebar -->
    <div class="events-sidebar mb-4">
      <h4 class="section-title">Upcoming Events</h4>
      <?php if (!empty($events)): ?>
        <?php foreach ($events as $event): ?>
          <div class="event-item">
            <div class="event-date"><?= date("M d, Y • h:i A", strtotime($event['event_date'])) ?></div>
            <h6><?= htmlspecialchars($event['title']) ?></h6>
            <p class="mb-0"><?= htmlspecialchars($event['location']) ?></p>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>No upcoming events.</p>
      <?php endif; ?>
      <a href="calendar.php" class="btn btn-primary w-100 mt-3">View Full Calendar</a>
    </div>
    
    <!-- Quick Links -->
    <div class="events-sidebar">
      <h4 class="section-title">Quick Links</h4>
      <div class="d-grid gap-2">
        <a href="join-club.php" class="btn btn-outline-primary">Join a Club</a>
        <a href="calendar.php" class="btn btn-outline-primary">Event Calendar</a>
        <a href="campus-map.php" class="btn btn-outline-primary">Campus Map</a>
        <a href="contact.php" class="btn btn-outline-primary">Contact Us</a>
      </div>
    </div>
  </div>
</div>



<!-- Latest News Section -->
<h2 class="section-title">Campus News & Updates</h2>
<div class="row mb-5">
  <?php if (!empty($posts)): ?>
    <?php foreach ($posts as $post): ?>
      <div class="col-md-4 mb-4">
        <div class="news-card">
          <img src="uploads/posts/<?= htmlspecialchars($post['image']) ?>" class="news-img" alt="<?= htmlspecialchars($post['title']) ?>">
          <div class="p-3">
            <div class="news-date"><?= date("F d, Y", strtotime($post['created_at'])) ?></div>
            <h5><?= htmlspecialchars($post['title']) ?></h5>
            <p><?= substr(strip_tags($post['content']), 0, 100) ?>...</p>
            <a href="post.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-primary">Read More</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p>No news available.</p>
  <?php endif; ?>
</div>

<!-- Events Section -->
<h3>Upcoming Events</h3>
<div class="row">
  <?php if (!empty($events)): ?>
    <?php foreach ($events as $event): ?>
      <div class="col-md-6">
        <div class="card mb-3 border-success">
          <?php if (!empty($event['image'])): ?>
            <img src="<?= htmlspecialchars($event['image']) ?>" class="card-img-top" style="height:200px; object-fit:cover;">
          <?php endif; ?>
          <div class="card-body">
            <h5 class="card-title text-success"><?= htmlspecialchars($event['title']) ?></h5>
            <p class="card-text"><?= substr(htmlspecialchars($event['description']), 0, 120) ?>...</p>
            <p><strong>Date:</strong> <?= htmlspecialchars($event['event_date']) ?></p>
            <p><strong>Location:</strong> <?= htmlspecialchars($event['location']) ?></p>
            <a href="event_details.php?id=<?= $event['id'] ?>" class="btn btn-outline-success btn-sm">View Details</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p>No upcoming events.</p>
  <?php endif; ?>
</div>

<?php include 'footer.php'; ?>