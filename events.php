<?php
include 'config.php';
include 'header.php';
include 'sidebar.php';

// Get events from database
$sql = "SELECT e.*, c.name as category_name, c.icon 
        FROM events e 
        JOIN categories c ON e.category_id = c.id 
        WHERE e.event_date >= CURDATE() 
        ORDER BY e.event_date ASC";
$result = $conn->query($sql);
?>

<!-- Events Header -->
<div class="page-header text-center">
    <h1 class="display-4 fw-bold">Campus Events</h1>
    <p class="lead">Discover upcoming events, activities, and gatherings</p>
</div>

<div class="row">
    <!-- Sidebar Filters -->
    
    
    <!-- Events List -->
    <div class="col-lg-9">
        <div class="row">
            <?php
            if ($result->num_rows > 0) {
                while($event = $result->fetch_assoc()) {
                    $event_date = new DateTime($event['event_date']);
                    echo '<div class="col-md-6 col-lg-4 mb-4">';
                    echo '<div class="card">';
                    echo '<span class="badge bg-primary category-badge">'.$event['category_name'].'</span>';
                    
                    if (!empty($event['image'])) {
                        echo '<img src="uploads/events/'.$event['image'].'" class="card-img-top" alt="'.$event['title'].'">';
                    } else {
                        echo '<img src="https://images.unsplash.com/photo-1546519638-68e109498ffc?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" class="card-img-top" alt="Event image">';
                    }
                    
                    echo '<div class="card-body">';
                    echo '<h5 class="card-title">'.$event['title'].'</h5>';
                    echo '<p class="card-text">'.substr($event['description'], 0, 100).'...</p>';
                    echo '<div class="d-flex align-items-center mt-3">';
                    echo '<div class="event-date">';
                    echo '<span class="fw-bold">'.$event_date->format('d').'</span>';
                    echo '<span>'.$event_date->format('M').'</span>';
                    echo '</div>';
                    echo '<div>';
                    echo '<p class="mb-0 small"><i class="fas fa-map-marker-alt me-1"></i> '.$event['location'].'</p>';
                    echo '<p class="mb-0 small"><i class="fas fa-clock me-1"></i> '.$event_date->format('g:i A').'</p>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                    echo '<div class="card-footer bg-transparent">';
                    echo '<a href="event_details.php?id='.$event['id'].'" class="btn btn-sm btn-outline-primary w-100">View Details</a>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<div class="col-12 text-center py-5">';
                echo '<i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>';
                echo '<h4>No upcoming events</h4>';
                echo '<p class="text-muted">Check back later for new events</p>';
                echo '</div>';
            }
            ?>
        </div>
    </div>
</div>

<?php
include 'footer.php';
?>