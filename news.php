<?php
include 'config.php';
include 'header.php';
include 'sidebar.php';

// Get posts from database
$sql = "SELECT p.*, u.name as author_name 
        FROM posts p 
        LEFT JOIN users u ON p.author_id = u.id 
        ORDER BY p.created_at DESC";
$result = $conn->query($sql);
?>

<!-- News Header -->
<div class="page-header text-center">
    <h1 class="display-4 fw-bold">Campus News</h1>
    <p class="lead">Stay updated with the latest happenings around campus</p>
</div>

<div class="row">
    <!-- Main News Content -->
    <div class="col-lg-8">
        <?php
        if ($result->num_rows > 0) {
            $first = true;
            while($post = $result->fetch_assoc()) {
                $post_date = new DateTime($post['created_at']);
                
                if ($first) {
                    // Featured post
                    echo '<div class="card mb-4">';
                    if (!empty($post['image'])) {
                        echo '<img src="uploads/posts/'.$post['image'].'" class="card-img-top" alt="'.$post['title'].'">';
                    } else {
                        echo '<img src="https://images.unsplash.com/photo-1584697964358-3e14ca57658b?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" class="card-img-top" alt="News image">';
                    }
                    echo '<div class="card-body">';
                    echo '<span class="badge bg-primary mb-2">Announcement</span>';
                    echo '<h2 class="card-title">'.$post['title'].'</h2>';
                    echo '<p class="card-text">'.substr(strip_tags($post['content']), 0, 200).'...</p>';
                    echo '<div class="d-flex justify-content-between align-items-center">';
                    echo '<span class="text-muted"><i class="fas fa-calendar me-1"></i> '.$post_date->format('F j, Y').'</span>';
                    echo '<a href="post.php?id='.$post['id'].'" class="btn btn-primary">Read More</a>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                    $first = false;
                } else {
                    // Regular posts
                    echo '<div class="col-md-6 mb-4">';
                    echo '<div class="card">';
                    if (!empty($post['image'])) {
                        echo '<img src="uploads/posts/'.$post['image'].'" class="card-img-top" alt="'.$post['title'].'">';
                    } else {
                        echo '<img src="https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" class="card-img-top" alt="News image">';
                    }
                    echo '<div class="card-body">';
                    echo '<span class="badge bg-success mb-2">News</span>';
                    echo '<h5 class="card-title">'.$post['title'].'</h5>';
                    echo '<p class="card-text">'.substr(strip_tags($post['content']), 0, 100).'...</p>';
                    echo '<div class="d-flex justify-content-between align-items-center">';
                    echo '<span class="text-muted small"><i class="fas fa-calendar me-1"></i> '.$post_date->format('M j, Y').'</span>';
                    echo '<a href="post.php?id='.$post['id'].'" class="btn btn-sm btn-outline-primary">Read More</a>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            }
        } else {
            echo '<div class="col-12 text-center py-5">';
            echo '<i class="fas fa-newspaper fa-3x text-muted mb-3"></i>';
            echo '<h4>No news articles</h4>';
            echo '<p class="text-muted">Check back later for news updates</p>';
            echo '</div>';
        }
        ?>
    </div>
    
   
            
            <h5 class="mt-4 mb-3">Upcoming Events</h5>
            <?php
            $event_sql = "SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC LIMIT 2";
            $event_result = $conn->query($event_sql);
            
            if ($event_result->num_rows > 0) {
                while($event = $event_result->fetch_assoc()) {
                    $event_date = new DateTime($event['event_date']);
                    echo '<div class="d-flex mb-3">';
                    echo '<div class="event-date-sm bg-primary text-white text-center me-3">';
                    echo '<div>'.$event_date->format('d').'</div>';
                    echo '<div>'.$event_date->format('M').'</div>';
                    echo '</div>';
                    echo '<div>';
                    echo '<h6 class="mb-0">'.$event['title'].'</h6>';
                    echo '<small class="text-muted">'.$event['location'].'</small>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p class="text-muted">No upcoming events</p>';
            }
            ?>
        </div>
    </div>
</div>

<?php
include 'footer.php';
?>