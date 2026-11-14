<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<?php
// Include database configuration
include 'config.php';

$message = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message_content = trim($_POST['message']);
    
    // Validate inputs
    if (!empty($name) && !empty($email) && !empty($subject) && !empty($message_content)) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Insert into database
            $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->bind_param("ssss", $name, $email, $subject, $message_content);
            
            if ($stmt->execute()) {
                $success = true;
                $message = "Thank you for your message! We'll get back to you soon.";
                
                // Clear form fields
                $name = $email = $subject = $message_content = "";
            } else {
                $message = "Sorry, there was an error sending your message. Please try again.";
            }
        } else {
            $message = "Please enter a valid email address.";
        }
    } else {
        $message = "Please fill in all required fields.";
    }
}
?>

<div class="content">
  <h2 class="section-title">Contact Us</h2>
  <p class="mb-4">Have questions, suggestions, or need assistance? We'd love to hear from you! Reach out through any of the following channels.</p>

  <div class="row">
    <div class="col-md-8">
      <div class="card mb-4">
        <div class="card-header bg-primary text-white">
          <h4 class="mb-0"><i class="fas fa-envelope me-2"></i>Send us a Message</h4>
        </div>
        <div class="card-body">
          <?php if ($message): ?>
            <div class="alert alert-<?php echo $success ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
              <?php echo $message; ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php endif; ?>

          <form action="contact.php" method="post">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="name" id="name" value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                <input type="email" class="form-control" name="email" id="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
              </div>
            </div>
            
            <div class="mb-3">
              <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
              <select class="form-select" name="subject" id="subject" required>
                <option value="">Select a subject</option>
                <option value="General Inquiry" <?php echo (isset($subject) && $subject == 'General Inquiry') ? 'selected' : ''; ?>>General Inquiry</option>
                <option value="Technical Support" <?php echo (isset($subject) && $subject == 'Technical Support') ? 'selected' : ''; ?>>Technical Support</option>
                <option value="Event Information" <?php echo (isset($subject) && $subject == 'Event Information') ? 'selected' : ''; ?>>Event Information</option>
                <option value="Partnership" <?php echo (isset($subject) && $subject == 'Partnership') ? 'selected' : ''; ?>>Partnership</option>
                <option value="Feedback" <?php echo (isset($subject) && $subject == 'Feedback') ? 'selected' : ''; ?>>Feedback</option>
                <option value="Other" <?php echo (isset($subject) && $subject == 'Other') ? 'selected' : ''; ?>>Other</option>
              </select>
            </div>
            
            <div class="mb-3">
              <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
              <textarea class="form-control" name="message" id="message" rows="5" required><?php echo isset($message_content) ? htmlspecialchars($message_content) : ''; ?></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-2"></i>Send Message</button>
          </form>
        </div>
      </div>
    </div>
    
    <div class="col-md-4">
      <div class="card mb-4">
        <div class="card-header bg-success text-white">
          <h4 class="mb-0"><i class="fas fa-info-circle me-2"></i>Contact Information</h4>
        </div>
        <div class="card-body">
          <div class="contact-info-item mb-3">
            <div class="d-flex align-items-center">
              <div class="contact-icon me-3">
                <i class="fas fa-map-marker-alt fa-2x text-primary"></i>
              </div>
              <div>
                <h6 class="mb-0">Address</h6>
                <p class="mb-0 text-muted">University Campus, Main Building<br>Kampala, Uganda</p>
              </div>
            </div>
          </div>
          
          <div class="contact-info-item mb-3">
            <div class="d-flex align-items-center">
              <div class="contact-icon me-3">
                <i class="fas fa-phone fa-2x text-primary"></i>
              </div>
              <div>
                <h6 class="mb-0">Phone</h6>
                <p class="mb-0 text-muted">+256 772 123456<br>+256 752 987654</p>
              </div>
            </div>
          </div>
          
          <div class="contact-info-item mb-3">
            <div class="d-flex align-items-center">
              <div class="contact-icon me-3">
                <i class="fas fa-envelope fa-2x text-primary"></i>
              </div>
              <div>
                <h6 class="mb-0">Email</h6>
                <p class="mb-0 text-muted">info@studentsphere.ac.ug<br>support@studentsphere.ac.ug</p>
              </div>
            </div>
          </div>
          
          <div class="contact-info-item">
            <div class="d-flex align-items-center">
              <div class="contact-icon me-3">
                <i class="fas fa-clock fa-2x text-primary"></i>
              </div>
              <div>
                <h6 class="mb-0">Office Hours</h6>
                <p class="mb-0 text-muted">Mon - Fri: 8:00 AM - 5:00 PM<br>Sat: 9:00 AM - 1:00 PM</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="card">
        <div class="card-header bg-info text-white">
          <h4 class="mb-0"><i class="fas fa-question-circle me-2"></i>Frequently Asked Questions</h4>
        </div>
        <div class="card-body">
          <div class="accordion" id="faqAccordion">
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                  How do I reset my password?
                </button>
              </h2>
              <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                  You can reset your password by clicking on the "Forgot Password" link on the login page. You'll receive instructions via email.
                </div>
              </div>
            </div>
            
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingTwo">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                  How can I join a student club?
                </button>
              </h2>
              <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                  Visit the Clubs section on the platform, browse available clubs, and click "Join" on any club you're interested in. Club leaders will approve your request.
                </div>
              </div>
            </div>
            
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingThree">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                  Where can I find event information?
                </button>
              </h2>
              <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                  All upcoming events are listed on the Events page. You can also view them in calendar format on the Calendar page.
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row mt-4">
    <div class="col-12">
      <div class="card">
        <div class="card-header bg-secondary text-white">
          <h4 class="mb-0"><i class="fas fa-map-marked-alt me-2"></i>Find Us on Campus</h4>
        </div>
        <div class="card-body">
          <div class="map-container rounded overflow-hidden">
            <iframe 
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d63835.25209785548!2d32.52885745!3d0.3475965!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x177dbb6e8b742bd5%3A0x982df7d5e11383e7!2sMakerere%20University!5e0!3m2!1sen!2sug!4v1690200000000!5m2!1sen!2sug" 
              width="100%" 
              height="300" 
              style="border:0;" 
              allowfullscreen="" 
              loading="lazy" 
              referrerpolicy="no-referrer-when-downgrade">
            </iframe>
          </div>
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

.contact-info-item {
  padding: 10px 0;
  border-bottom: 1px solid #eee;
}

.contact-info-item:last-child {
  border-bottom: none;
}

.contact-icon {
  width: 40px;
  text-align: center;
}

.map-container {
  border: 1px solid #dee2e6;
  border-radius: 0.375rem;
}

.card {
  border: none;
  border-radius: 10px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.05);
}

.card-header {
  border-radius: 10px 10px 0 0 !important;
}

.accordion-button:not(.collapsed) {
  background-color: #e3f2fd;
  color: #0c63e4;
}

.accordion-button:focus {
  box-shadow: none;
  border-color: rgba(0,0,0,.125);
}
</style>

<?php include 'footer.php'; ?>