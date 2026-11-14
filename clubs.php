<?php
include 'config.php';
include 'header.php';
include 'sidebar.php';

// Get clubs from database
$sql = "SELECT * FROM clubs ORDER BY name ASC";
$result = $conn->query($sql);
?>

<!-- Clubs Header -->
<div class="page-header text-center">
    <h1 class="display-4 fw-bold">Student Clubs</h1>
    <p class="lead">Join student organizations and connect with like-minded peers</p>
</div>

<div class="row">
    <!-- Clubs List -->
    <div class="col-lg-9">
        <div class="row">
            <?php
            if ($result->num_rows > 0) {
                while($club = $result->fetch_assoc()) {
                    // Get member count
                    $member_sql = "SELECT COUNT(*) as member_count FROM club_members WHERE club_id = " . $club['id'];
                    $member_result = $conn->query($member_sql);
                    $member_count = $member_result->fetch_assoc()['member_count'];
                    
                    echo '<div class="col-md-6 col-lg-4 mb-4">';
                    echo '<div class="card text-center">';
                    echo '<div class="card-body">';
                    echo '<div class="club-icon bg-primary">';
                    echo '<i class="fas fa-users fa-2x text-white"></i>';
                    echo '</div>';
                    echo '<h5 class="card-title">'.$club['name'].'</h5>';
                    echo '<p class="card-text">'.substr($club['description'], 0, 100).'...</p>';
                    echo '<div class="d-flex justify-content-between">';
                    echo '<span class="text-muted"><i class="fas fa-users me-1"></i> '.$member_count.' members</span>';
                    echo '<span class="text-muted"><i class="fas fa-star me-1"></i> 4.8</span>';
                    echo '</div>';
                    echo '</div>';
                    echo '<div class="card-footer bg-transparent">';
                    echo '<a href="club_details.php?id='.$club['id'].'" class="btn btn-sm btn-outline-primary w-100">View Club</a>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<div class="col-12 text-center py-5">';
                echo '<i class="fas fa-users fa-3x text-muted mb-3"></i>';
                echo '<h4>No clubs available</h4>';
                echo '<p class="text-muted">Check back later for club information</p>';
                echo '</div>';
            }
            ?>
        </div>
    </div>
    
    

<?php
include 'footer.php';
?>