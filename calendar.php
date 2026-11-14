<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<?php
// Include database configuration
include 'config.php';

// Fetch upcoming events from the database
$current_date = date('Y-m-d');
$events_query = "SELECT * FROM events WHERE event_date >= '$current_date' ORDER BY event_date ASC";
$events_result = $conn->query($events_query);

// Group events by month
$events_by_month = [];
while ($event = $events_result->fetch_assoc()) {
    $month_year = date('F Y', strtotime($event['event_date']));
    $events_by_month[$month_year][] = $event;
}

// Get current month and year
$current_month = date('n');
$current_year = date('Y');

// Generate calendar for current month
function generateCalendar($month, $year, $events = []) {
    $first_day = mktime(0, 0, 0, $month, 1, $year);
    $days_in_month = date('t', $first_day);
    $first_day_of_week = date('w', $first_day);
    
    // Adjust Sunday from 0 to 7 for easier calculation
    if ($first_day_of_week == 0) {
        $first_day_of_week = 7;
    }
    
    $calendar = '<table class="calendar-table">';
    $calendar .= '<thead><tr>';
    $calendar .= '<th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th><th>Sun</th>';
    $calendar .= '</tr></thead><tbody>';
    
    $day = 1;
    $calendar .= '<tr>';
    
    // Fill empty cells before the first day
    for ($i = 1; $i < $first_day_of_week; $i++) {
        $calendar .= '<td class="empty"></td>';
    }
    
    // Fill days
    while ($day <= $days_in_month) {
        // Start new row if it's Monday
        if ($first_day_of_week > 7) {
            $calendar .= '</tr><tr>';
            $first_day_of_week = 1;
        }
        
        $current_date = sprintf('%04d-%02d-%02d', $year, $month, $day);
        $has_event = isset($events[$current_date]);
        $is_today = ($current_date == date('Y-m-d'));
        
        $calendar .= '<td class="' . ($is_today ? 'today ' : '') . ($has_event ? 'has-event' : '') . '">';
        $calendar .= '<div class="day-number">' . $day . '</div>';
        
        if ($has_event) {
            $calendar .= '<div class="event-indicator" title="' . $events[$current_date][0]['title'] . '">';
            $calendar .= '<i class="fas fa-calendar-check"></i>';
            $calendar .= '</div>';
        }
        
        $calendar .= '</td>';
        
        $day++;
        $first_day_of_week++;
    }
    
    // Fill empty cells after the last day
    while ($first_day_of_week <= 7) {
        $calendar .= '<td class="empty"></td>';
        $first_day_of_week++;
    }
    
    $calendar .= '</tr></tbody></table>';
    return $calendar;
}

// Prepare events for calendar
$calendar_events = [];
if ($events_result->num_rows > 0) {
    $events_result->data_seek(0); // Reset pointer
    while ($event = $events_result->fetch_assoc()) {
        $event_date = $event['event_date'];
        $calendar_events[$event_date][] = $event;
    }
}
?>

<div class="content">
  <h2 class="section-title">Event Calendar</h2>
  <p class="mb-4">Stay updated with all upcoming university events, activities, and important dates.</p>

  <div class="row">
    <div class="col-md-8">
      <div class="card mb-4">
        <div class="card-header bg-primary text-white">
          <h4 class="mb-0"><i class="fas fa-calendar-alt me-2"></i><?php echo date('F Y'); ?> Calendar</h4>
        </div>
        <div class="card-body">
          <?php echo generateCalendar($current_month, $current_year, $calendar_events); ?>
        </div>
      </div>
    </div>
    
    <div class="col-md-4">
      <div class="card mb-4">
        <div class="card-header bg-success text-white">
          <h4 class="mb-0"><i class="fas fa-info-circle me-2"></i>Calendar Legend</h4>
        </div>
        <div class="card-body">
          <div class="d-flex align-items-center mb-2">
            <div class="day-sample today me-3"></div>
            <span>Today</span>
          </div>
          <div class="d-flex align-items-center mb-2">
            <div class="day-sample has-event me-3"></div>
            <span>Event Day</span>
          </div>
          <div class="d-flex align-items-center">
            <div class="day-sample me-3"></div>
            <span>Regular Day</span>
          </div>
        </div>
      </div>
      
      <div class="card">
        <div class="card-header bg-info text-white">
          <h4 class="mb-0"><i class="fas fa-question-circle me-2"></i>How to Use</h4>
        </div>
        <div class="card-body">
          <ul class="list-unstyled">
            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Click on event days to view details</li>
            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Hover over event indicators for event titles</li>
            <li><i class="fas fa-check-circle text-success me-2"></i>Check back regularly for updates</li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <div class="row mt-4">
    <div class="col-12">
      <div class="card">
        <div class="card-header bg-secondary text-white">
          <h4 class="mb-0"><i class="fas fa-list me-2"></i>Upcoming Events</h4>
        </div>
        <div class="card-body">
          <?php if (!empty($events_by_month)): ?>
            <?php foreach ($events_by_month as $month => $events): ?>
              <h5 class="mt-4 text-primary"><?php echo $month; ?></h5>
              <div class="row">
                <?php foreach ($events as $event): ?>
                  <div class="col-md-6 mb-3">
                    <div class="event-card p-3 h-100">
                      <div class="d-flex justify-content-between align-items-start">
                        <h5 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h5>
                        <span class="badge bg-primary"><?php echo date('M j', strtotime($event['event_date'])); ?></span>
                      </div>
                      <p class="event-description"><?php echo htmlspecialchars($event['description']); ?></p>
                      <div class="event-details">
                        <small class="text-muted">
                          <i class="fas fa-map-marker-alt me-1"></i> <?php echo htmlspecialchars($event['location']); ?>
                        </small>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="text-center py-4">
              <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
              <h5>No Upcoming Events</h5>
              <p class="text-muted">Check back later for new events and activities.</p>
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

.calendar-table {
  width: 100%;
  border-collapse: collapse;
}

.calendar-table th {
  background-color: #f8f9fa;
  padding: 10px;
  text-align: center;
  font-weight: 600;
  border: 1px solid #dee2e6;
}

.calendar-table td {
  width: 14.28%;
  height: 80px;
  vertical-align: top;
  padding: 5px;
  border: 1px solid #dee2e6;
  position: relative;
}

.calendar-table td.empty {
  background-color: #f8f9fa;
}

.calendar-table td.today {
  background-color: #e3f2fd;
  font-weight: bold;
}

.calendar-table td.has-event {
  background-color: #e8f5e9;
  cursor: pointer;
}

.calendar-table td.has-event:hover {
  background-color: #c8e6c9;
}

.day-number {
  font-size: 14px;
  font-weight: 500;
}

.event-indicator {
  position: absolute;
  bottom: 5px;
  right: 5px;
  color: #28a745;
  font-size: 12px;
}

.day-sample {
  width: 20px;
  height: 20px;
  border: 1px solid #dee2e6;
}

.day-sample.today {
  background-color: #e3f2fd;
}

.day-sample.has-event {
  background-color: #e8f5e9;
}

.event-card {
  border: 1px solid #e3f2fd;
  border-radius: 8px;
  background-color: #f8f9fa;
  transition: all 0.3s;
}

.event-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.event-title {
  color: #2c3e50;
  font-weight: 600;
}

.event-description {
  color: #6c757d;
  font-size: 0.9rem;
}

.card-header {
  border-radius: 8px 8px 0 0 !important;
}
</style>

<?php include 'footer.php'; ?>