<?php
session_start();
include 'config.php';
include 'header.php';
include 'sidebar.php';

// Get search query
$query = isset($_GET['q']) ? trim($_GET['q']) : '';

// Initialize results arrays
$events = [];
$posts = [];
$clubs = [];

if (!empty($query)) {
    // Search events
    $events_stmt = $conn->prepare("
        SELECT id, title, description, event_date, location, image 
        FROM events 
        WHERE (title LIKE ? OR description LIKE ? OR location LIKE ?)
        AND event_date >= NOW()
        ORDER BY event_date ASC
    ");
    $search_term = "%$query%";
    $events_stmt->bind_param("sss", $search_term, $search_term, $search_term);
    $events_stmt->execute();
    $events_result = $events_stmt->get_result();
    $events = $events_result->fetch_all(MYSQLI_ASSOC);

    // Search posts
    $posts_stmt = $conn->prepare("
        SELECT id, title, content, image, created_at 
        FROM posts 
        WHERE title LIKE ? OR content LIKE ?
        ORDER BY created_at DESC
    ");
    $posts_stmt->bind_param("ss", $search_term, $search_term);
    $posts_stmt->execute();
    $posts_result = $posts_stmt->get_result();
    $posts = $posts_result->fetch_all(MYSQLI_ASSOC);

    // Search clubs - FIXED: Removed reference to non-existent 'category' column
    $clubs_stmt = $conn->prepare("
        SELECT id, name, description
        FROM clubs 
        WHERE name LIKE ? OR description LIKE ?
        ORDER BY name ASC
    ");
    $clubs_stmt->bind_param("ss", $search_term, $search_term);
    $clubs_stmt->execute();
    $clubs_result = $clubs_stmt->get_result();
    $clubs = $clubs_result->fetch_all(MYSQLI_ASSOC);
}
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <!-- Search Header -->
            <div class="search-header mb-4">
                <h2 class="section-title">Search Results</h2>
                
                <!-- Search Form -->
                <form class="d-flex mb-4" action="search.php" method="get">
                    <input class="form-control me-2" type="search" name="q" 
                           value="<?= htmlspecialchars($query) ?>" 
                           placeholder="Search events, news, clubs..." 
                           aria-label="Search" required>
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search me-1"></i> Search
                    </button>
                </form>

                <?php if (!empty($query)): ?>
                    <p class="text-muted">
                        Found <?= count($events) + count($posts) + count($clubs) ?> results for 
                        <strong>"<?= htmlspecialchars($query) ?>"</strong>
                    </p>
                <?php endif; ?>
            </div>

            <?php if (empty($query)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Please enter a search term to find events, news, and clubs.
                </div>
            <?php else: ?>
                <!-- Events Results -->
                <?php if (!empty($events)): ?>
                    <div class="search-section mb-5">
                        <h4 class="mb-3 text-primary">
                            <i class="fas fa-calendar-alt me-2"></i>Events (<?= count($events) ?>)
                        </h4>
                        <div class="row">
                            <?php foreach ($events as $event): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100">
                                        <?php if (!empty($event['image'])): ?>
                                            <img src="<?= htmlspecialchars($event['image']) ?>" 
                                                 class="card-img-top" 
                                                 alt="<?= htmlspecialchars($event['title']) ?>"
                                                 style="height: 150px; object-fit: cover;">
                                        <?php endif; ?>
                                        <div class="card-body">
                                            <h5 class="card-title"><?= htmlspecialchars($event['title']) ?></h5>
                                            <p class="card-text">
                                                <?= substr(strip_tags($event['description']), 0, 100) ?>...
                                            </p>
                                            <p class="text-muted small mb-1">
                                                <i class="fas fa-map-marker-alt me-1"></i>
                                                <?= htmlspecialchars($event['location']) ?>
                                            </p>
                                            <p class="text-muted small">
                                                <i class="fas fa-clock me-1"></i>
                                                <?= date("M d, Y", strtotime($event['event_date'])) ?>
                                            </p>
                                        </div>
                                        <div class="card-footer bg-transparent">
                                            <a href="event_details.php?id=<?= $event['id'] ?>" 
                                               class="btn btn-sm btn-outline-primary w-100">
                                                View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Posts Results -->
                <?php if (!empty($posts)): ?>
                    <div class="search-section mb-5">
                        <h4 class="mb-3 text-success">
                            <i class="fas fa-newspaper me-2"></i>News & Updates (<?= count($posts) ?>)
                        </h4>
                        <div class="row">
                            <?php foreach ($posts as $post): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100">
                                        <?php if (!empty($post['image'])): ?>
                                            <img src="uploads/posts/<?= htmlspecialchars($post['image']) ?>" 
                                                 class="card-img-top" 
                                                 alt="<?= htmlspecialchars($post['title']) ?>"
                                                 style="height: 150px; object-fit: cover;">
                                        <?php endif; ?>
                                        <div class="card-body">
                                            <h5 class="card-title"><?= htmlspecialchars($post['title']) ?></h5>
                                            <p class="card-text">
                                                <?= substr(strip_tags($post['content']), 0, 100) ?>...
                                            </p>
                                            <p class="text-muted small">
                                                <i class="fas fa-calendar me-1"></i>
                                                <?= date("M d, Y", strtotime($post['created_at'])) ?>
                                            </p>
                                        </div>
                                        <div class="card-footer bg-transparent">
                                            <a href="post.php?id=<?= $post['id'] ?>" 
                                               class="btn btn-sm btn-outline-success w-100">
                                                Read More
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Clubs Results -->
                <?php if (!empty($clubs)): ?>
                    <div class="search-section mb-5">
                        <h4 class="mb-3 text-warning">
                            <i class="fas fa-users me-2"></i>Clubs (<?= count($clubs) ?>)
                        </h4>
                        <div class="row">
                            <?php foreach ($clubs as $club): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title"><?= htmlspecialchars($club['name']) ?></h5>
                                            <p class="card-text">
                                                <?= substr(strip_tags($club['description']), 0, 120) ?>...
                                            </p>
                                        </div>
                                        <div class="card-footer bg-transparent">
                                            <a href="club_details.php?id=<?= $club['id'] ?>" 
                                               class="btn btn-sm btn-outline-warning w-100">
                                                View Club
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- No Results -->
                <?php if (empty($events) && empty($posts) && empty($clubs)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h4>No results found</h4>
                        <p class="text-muted">We couldn't find any matches for "<?= htmlspecialchars($query) ?>"</p>
                        <p>Try different keywords or browse our categories:</p>
                        <div class="mt-3">
                            <a href="events.php" class="btn btn-outline-primary me-2">Events</a>
                            <a href="news.php" class="btn btn-outline-success me-2">News</a>
                            <a href="clubs.php" class="btn btn-outline-warning">Clubs</a>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.search-header {
    background: white;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.search-section {
    background: white;
    padding: 1.5rem;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.section-title {
    color: #2c3e50;
    border-bottom: 3px solid #f39c12;
    padding-bottom: 10px;
    margin-bottom: 20px;
}

.card {
    transition: transform 0.2s;
    border: none;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
}
</style>

<?php include 'footer.php'; ?>