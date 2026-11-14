<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<?php
// Include database configuration
include 'config.php';

// Initialize variables
$clubs = [];
$message = "";
$success = false;

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch clubs from the database
$clubs_query = "SELECT * FROM clubs ORDER BY name ASC";
$clubs_result = $conn->query($clubs_query);

if ($clubs_result) {
    if ($clubs_result->num_rows > 0) {
        while ($club = $clubs_result->fetch_assoc()) {
            $clubs[] = $club;
        }
    }
} else {
    $message = "Error fetching clubs: " . $conn->error;
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_name = trim($_POST['student_name']);
    $student_email = trim($_POST['student_email']);
    $student_id = trim($_POST['student_id']);
    $club_id = intval($_POST['club_id']);
    $interest_reason = trim($_POST['interest_reason']);
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    
    // Validate inputs
    if (!empty($student_name) && !empty($student_email) && !empty($club_id)) {
        if (filter_var($student_email, FILTER_VALIDATE_EMAIL)) {
            if ($user_id) {
                // Check if student is already a member of this club
                $check_query = "SELECT id FROM club_members WHERE user_id = ? AND club_id = ?";
                $check_stmt = $conn->prepare($check_query);
                $check_stmt->bind_param("ii", $user_id, $club_id);
                $check_stmt->execute();
                $check_result = $check_stmt->get_result();
                
                if ($check_result->num_rows > 0) {
                    $message = "You are already a member of this club!";
                } else {
                    // Insert into database using user_id
                    $stmt = $conn->prepare("INSERT INTO club_members (club_id, user_id, joined_at) VALUES (?, ?, NOW())");
                    $stmt->bind_param("ii", $club_id, $user_id);
                    
                    if ($stmt->execute()) {
                        $success = true;
                        $message = "Successfully joined the club! The club leader will contact you soon.";
                        
                        // Clear form fields
                        $student_name = $student_email = $student_id = $interest_reason = "";
                    } else {
                        $message = "Sorry, there was an error processing your request. Please try again.";
                    }
                }
            } else {
                $message = "You need to be logged in to join a club.";
            }
        } else {
            $message = "Please enter a valid email address.";
        }
    } else {
        $message = "Please fill in all required fields.";
    }
}

// Get user information if logged in
$user_name = "";
$user_email = "";
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $user_query = "SELECT name, email FROM users WHERE id = ?";
    $user_stmt = $conn->prepare($user_query);
    $user_stmt->bind_param("i", $user_id);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();
    
    if ($user_result->num_rows > 0) {
        $user_data = $user_result->fetch_assoc();
        $user_name = $user_data['name'];
        $user_email = $user_data['email'];
    }
}
?>

<div class="content">
  <h2 class="section-title">Join a Student Club</h2>
  <p class="mb-4">Discover and join various student clubs to enhance your university experience, develop skills, and meet like-minded students.</p>

  <?php if ($message): ?>
    <div class="alert alert-<?php echo $success ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
      <?php echo $message; ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <div class="row">
    <div class="col-md-8">
      <div class="card mb-4">
        <div class="card-header bg-primary text-white">
          <h4 class="mb-0"><i class="fas fa-sign-in-alt me-2"></i>Club Membership Form</h4>
        </div>
        <div class="card-body">
          <?php if (!isset($_SESSION['user_id'])): ?>
            <div class="alert alert-warning">
              <i class="fas fa-exclamation-triangle me-2"></i>
              You need to <a href="login.php" class="alert-link">login</a> to join a club.
            </div>
          <?php else: ?>
            <form action="join-club.php" method="post">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="student_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="student_name" id="student_name" 
                         value="<?php echo !empty($student_name) ? htmlspecialchars($student_name) : htmlspecialchars($user_name); ?>" 
                         required <?php echo !empty($user_name) ? 'readonly' : ''; ?>>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="student_email" class="form-label">Email Address <span class="text-danger">*</span></label>
                  <input type="email" class="form-control" name="student_email" id="student_email" 
                         value="<?php echo !empty($student_email) ? htmlspecialchars($student_email) : htmlspecialchars($user_email); ?>" 
                         required <?php echo !empty($user_email) ? 'readonly' : ''; ?>>
                </div>
              </div>
              
              <div class="mb-3">
                <label for="student_id" class="form-label">Student ID (Optional)</label>
                <input type="text" class="form-control" name="student_id" id="student_id" 
                       value="<?php echo isset($student_id) ? htmlspecialchars($student_id) : ''; ?>" 
                       placeholder="Enter your student ID if available">
              </div>
              
              <div class="mb-3">
                <label for="club_id" class="form-label">Select Club <span class="text-danger">*</span></label>
                <select class="form-select" name="club_id" id="club_id" required>
                  <option value="">-- Choose a club --</option>
                  <?php if (!empty($clubs)): ?>
                    <?php foreach ($clubs as $club): ?>
                      <option value="<?php echo $club['id']; ?>" <?php echo (isset($_POST['club_id']) && $_POST['club_id'] == $club['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($club['name']); ?>
                      </option>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <option value="">No clubs available at the moment</option>
                  <?php endif; ?>
                </select>
              </div>
              
              <div class="mb-3">
                <label for="interest_reason" class="form-label">Why are you interested in joining this club?</label>
                <textarea class="form-control" name="interest_reason" id="interest_reason" rows="3" 
                          placeholder="Share your interests, skills, or reasons for joining"><?php echo isset($interest_reason) ? htmlspecialchars($interest_reason) : ''; ?></textarea>
              </div>
              
              <button type="submit" class="btn btn-success"><i class="fas fa-users me-2"></i>Join Club</button>
            </form>
          <?php endif; ?>
        </div>
      </div>
    </div>
    
    <div class="col-md-4">
      <div class="card mb-4">
        <div class="card-header bg-info text-white">
          <h4 class="mb-0"><i class="fas fa-info-circle me-2"></i>Why Join a Club?</h4>
        </div>
        <div class="card-body">
          <ul class="list-unstyled">
            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Develop new skills and talents</li>
            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Meet students with similar interests</li>
            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Enhance your university experience</li>
            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Build your network and connections</li>
            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Gain leadership experience</li>
            <li><i class="fas fa-check-circle text-success me-2"></i>Participate in exciting events and activities</li>
          </ul>
        </div>
      </div>
      
      <div class="card">
        <div class="card-header bg-warning text-dark">
          <h4 class="mb-0"><i class="fas fa-question-circle me-2"></i>Need Help Choosing?</h4>
        </div>
        <div class="card-body">
          <p class="mb-3">Not sure which club to join? Here are some tips:</p>
          <ul class="list-unstyled">
            <li class="mb-2"><i class="fas fa-lightbulb text-warning me-2"></i>Consider your hobbies and interests</li>
            <li class="mb-2"><i class="fas fa-lightbulb text-warning me-2"></i>Think about skills you want to develop</li>
            <li class="mb-2"><i class="fas fa-lightbulb text-warning me-2"></i>Ask friends about their club experiences</li>
            <li><i class="fas fa-lightbulb text-warning me-2"></i>Attend club fairs and open events</li>
          </ul>
          <div class="text-center mt-3">
            <a href="clubs.php" class="btn btn-outline-primary btn-sm">Explore All Clubs</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row mt-4">
    <div class="col-12">
      <div class="card">
        <div class="card-header bg-success text-white">
          <h4 class="mb-0"><i class="fas fa-users me-2"></i>Available Clubs</h4>
        </div>
        <div class="card-body">
          <?php if (!empty($clubs)): ?>
            <div class="row">
              <?php foreach ($clubs as $club): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                  <div class="club-card h-100">
                    <div class="club-image">
                      <img src="<?php echo !empty($club['image_url']) ? htmlspecialchars($club['image_url']) : 'https://via.placeholder.com/300x200/3c8dbc/ffffff?text=' . urlencode($club['name']); ?>" 
                           alt="<?php echo htmlspecialchars($club['name']); ?>" 
                           class="img-fluid rounded-top">
                    </div>
                    <div class="club-content p-3">
                      <h5 class="club-title"><?php echo htmlspecialchars($club['name']); ?></h5>
                      <p class="club-description"><?php 
                        $description = !empty($club['description']) ? $club['description'] : 'No description available.';
                        echo htmlspecialchars(substr($description, 0, 100)) . (strlen($description) > 100 ? '...' : '');
                      ?></p>
                      <div class="club-meta">
                        <small class="text-muted">
                          <i class="fas fa-user-friends me-1"></i> 
                          <?php 
                          // Get member count
                          $member_query = "SELECT COUNT(*) as count FROM club_members WHERE club_id = " . $club['id'];
                          $member_result = $conn->query($member_query);
                          if ($member_result) {
                              $member_count = $member_result->fetch_assoc()['count'];
                              echo $member_count . ' member' . ($member_count != 1 ? 's' : '');
                          } else {
                              echo '0 members';
                          }
                          ?>
                        </small>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <div class="text-center py-4">
              <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
              <h5>No Clubs Available</h5>
              <p class="text-muted">Check back later for new clubs and organizations.</p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.section-title {
  position: relative;
  padding-bottom: 15px;
  margin-bottom: 30px;
  font-weight: 700;
}

.section-title:after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 60px;
  height: 4px;
  background: #f39c12;
  border-radius: 2px;
}

.club-card {
  border: 1px solid #e3f2fd;
  border-radius: 8px;
  background-color: #fff;
  transition: all 0.3s;
  box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}

.club-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.club-image {
  height: 150px;
  overflow: hidden;
}

.club-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.club-title {
  color: #2c3e50;
  font-weight: 600;
}

.club-description {
  color: #6c757d;
  font-size: 0.9rem;
}

.card {
  border: none;
  border-radius: 10px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.05);
}

.card-header {
  border-radius: 10px 10px 0 0 !important;
}
</style>

<?php include 'footer.php'; ?>