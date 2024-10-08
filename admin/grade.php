<?php
// Include Composer's autoload file for PHPMailer
require 'vendor/autoload.php';

// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Create a new PHPMailer instance
// Handle form submission
if (isset($_POST['sendEmail'])) {
    // Get form data
    $facultyEmail = $_POST['facultyEmail'];
    $subject = $_POST['emailSubject'];
    $messageBody = $_POST['emailBody'];


    require 'vendor/autoload.php';

    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);  // Passing 'true' enables exceptions

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'arvingm1522@gmail.com'; // Replace with your actual Gmail
        $mail->Password = 'guxuezdzfvmqtoks'; // Replace with your app-specific password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('arvingm1522@gmail.com', 'Registrar');  // Adjust sender's email and name
        $mail->addAddress($facultyEmail);  // Use the selected faculty's email

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $messageBody;

        // Send email
        $mail->send();
        echo '<script>alert("Message has been sent successfully!");</script>';
    } catch (Exception $e) {
        echo "<script>alert('Message could not be sent. Mailer Error: {$mail->ErrorInfo}');</script>";
    }
}
?>

<style>
.custom-btn {
    background-color: #28a745; /* Green background */
    color: white; /* White text */
    padding: 10px 20px; /* Adjust padding for better look */
    border-radius: 5px; /* Rounded corners */
    border: none; /* Remove border */
    font-size: 16px; /* Adjust font size */
    cursor: pointer; /* Change cursor to pointer */
  }

  /* Hover effect */
  .custom-btn:hover {
    background-color: #218838; /* Darker green on hover */
  }
</style>

<!-- Include Bootstrap CSS -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

       <!-- Button to open modal -->
       <button type="button" class="btn btn-primary custom-btn" data-toggle="modal" data-target="#emailModal">
            Send Email to Faculty
        </button>
<!-- Card Container for Table -->
<div class="card">

<!-- Modal for sending email -->
<div class="modal fade" id="emailModal" tabindex="-1" role="dialog" aria-labelledby="emailModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="emailModalLabel">Send Email to Faculty</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="emailForm" action="" method="POST">
          <!-- Faculty Dropdown -->
          <div class="form-group">
            <label for="facultyEmail">Select Faculty</label>
            <select id="facultyEmail" name="facultyEmail" class="form-control" required>
  <option value="">-- Select Faculty --</option>
  <option value="all">-- All Faculty --</option> <!-- New option for all faculty -->
  <?php
    // Fetch the list of faculty emails
    $faculty_result = mysqli_query($conn, "SELECT id, CONCAT(firstname, ' ', lastname) as name, email FROM faculty_list");
    while($faculty = mysqli_fetch_assoc($faculty_result)) {
      echo "<option value=\"{$faculty['email']}\">{$faculty['name']} - {$faculty['email']}</option>";
    }
  ?>
</select>

          </div>

          <!-- Subject Input -->
          <div class="form-group">
            <label for="emailSubject">Subject</label>
            <input type="text" id="emailSubject" name="emailSubject" class="form-control" placeholder="Enter subject" required>
          </div>

          <!-- Message Body Input -->
          <div class="form-group">
            <label for="emailBody">Message</label>
            <textarea id="emailBody" name="emailBody" class="form-control" rows="5" placeholder="Enter your message" required></textarea>
          </div>

          <button type="submit" name="sendEmail" class="btn btn-primary">Send Email</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- End of Modal -->
<div>
  <div class="card-header text-black d-flex justify-content-between align-items-center">
    <h4 class="mb-0">Submitted Grades</h4>
  </div>

  <div class="card-body">
    <!-- Flex Container for Filters -->
    <div class="d-flex justify-content-between align-items-center mb-3">
      <!-- Search Input on the left side -->
      <div class="col-md-4 pl-0">
        <label for="searchInput" class="sr-only">Search by Student:</label>
        <input type="text" id="searchInput" class="form-control" placeholder="Search by Student Name...">
      </div>

      <!-- Class Filter on the right side -->
      <div class="col-md-3 pr-0">
        <label for="classFilter" class="sr-only">Filter by Class:</label>
        <select id="classFilter" class="form-control">
          <option value="">All Classes</option>
          <?php
            // Fetch the list of classes for the filter
            $classes = mysqli_query($conn, "SELECT DISTINCT CONCAT(c.curriculum, ' - Level ', c.level, ' - Section ', c.section) as class_info, c.id as class_id FROM class_list c");
            while($class = mysqli_fetch_assoc($classes)) {
              echo "<option value=\"{$class['class_id']}\">{$class['class_info']}</option>";
            }
          ?>
        </select>
      </div>
    </div>

    <!-- Updated Grades Table -->
    <div class="table-responsive">
      <table class="table table-bordered table-hover" id="gradesTable">
        <thead>
          <tr>
            <th>Student</th>
            <th>Subject</th>
            <th>Prelim</th>
            <th>Midterm</th>
            <th>Pre-Finals</th>
            <th>Finals</th>
            <th>Class</th>
            <th>Professor</th>
            <th>Submitted On</th>
          </tr>
        </thead>
        <tbody>
          <?php 
            // Base query to fetch grades and join with the student, subject, faculty, and class tables.
            $query = "
              SELECT g.student_id, g.subject_id, CONCAT(s.firstname, ' ', s.lastname) as student_name,
                     sub.code, 
                     MAX(CASE WHEN g.term = 'Prelim' THEN g.grade END) AS Prelim,
                     MAX(CASE WHEN g.term = 'Midterm' THEN g.grade END) AS Midterm,
                     MAX(CASE WHEN g.term = 'Pre-Finals' THEN g.grade END) AS PreFinals,
                     MAX(CASE WHEN g.term = 'Finals' THEN g.grade END) AS Finals,
                     IFNULL(CONCAT(f.firstname, ' ', f.lastname), 'Unknown') as faculty_name, 
                     CONCAT(c.curriculum, ' - Level ', c.level, ' - Section ', c.section) as class_info,
                     g.timestamp
              FROM grades g
              JOIN student_list s ON g.student_id = s.id
              JOIN subject_list sub ON g.subject_id = sub.id
              LEFT JOIN faculty_list f ON g.faculty_id = f.id
              JOIN class_list c ON s.class_id = c.id
              GROUP BY g.student_id, g.subject_id
            ";

            $grades = mysqli_query($conn, $query);

            // Displaying the data in the table.
            while ($row = mysqli_fetch_assoc($grades)) {
              // Format the timestamp to "Month Day, Year" and 12-hour time format
              $formatted_timestamp = date('F j, Y, g:i A', strtotime($row['timestamp']));
          
              // Using isset() to check if the value exists, otherwise default to 'N/A'
              echo "<tr>
                      <td>{$row['student_name']}</td>
                      <td>{$row['code']}</td>
                      <td>" . (isset($row['Prelim']) ? $row['Prelim'] : 'N/A') . "</td>
                      <td>" . (isset($row['Midterm']) ? $row['Midterm'] : 'N/A') . "</td>
                      <td>" . (isset($row['PreFinals']) ? $row['PreFinals'] : 'N/A') . "</td>
                      <td>" . (isset($row['Finals']) ? $row['Finals'] : 'N/A') . "</td>
                      <td>{$row['class_info']}</td>
                      <td>{$row['faculty_name']}</td>
                      <td>{$formatted_timestamp}</td>
                    </tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
 // Handle class filter change
 document.getElementById('classFilter').addEventListener('change', function() {
    const classId = this.value;

    // Redirect to the appropriate URL based on selected class filter
    if (classId === "") {
      window.location.href = "http://localhost/php/eval/index.php?page=grade";
    } else {
      const currentPage = window.location.pathname;
      const params = new URLSearchParams(window.location.search);
      params.set('class_id', classId);
      window.location.href = currentPage + '?' + params.toString();
    }
  });

 // Handle search input for the table
 document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('#gradesTable tbody tr');

    rows.forEach(row => {
      const studentCell = row.cells[0].textContent.toLowerCase(); // Check student name in the first column

      if (studentCell.includes(searchTerm)) {
        row.style.display = ''; // Show the row
      } else {
        row.style.display = 'none'; // Hide the row
      }
    });
  });
</script>