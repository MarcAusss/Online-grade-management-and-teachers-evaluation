<!-- Include Bootstrap CSS -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

<!-- Custom Styles -->
<style>
  table {
    width: 100%;
    border-collapse: collapse;
  }

  thead th {
    background-color: #007BFF;
    color: white;
    text-align: center;
  }

  tbody td {
    text-align: center;
    vertical-align: middle;
  }

  tbody tr:nth-child(even) {
    background-color: #f2f2f2;
  }

  .table-responsive {
    margin-top: 20px;
  }

  .table {
    border: 1px solid #dee2e6;
  }

  .table td, .table th {
    padding: 12px;
  }

  .card {
    margin-top: 20px;
  }
</style>

<!-- Card Container for Table -->
<div class="card">
  <div class="card-header bg-primary text-white">
    <h4 class="mb-0">Submitted Grades</h4>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <th>Student</th>
            <th>Subject</th>
            <th>Grade</th>
            <th>Submitted By</th>
            <th>Submitted On</th>
          </tr>
        </thead>
        <tbody>
          <?php 
            // Query to fetch grades and join with the student, subject, and faculty tables.
            $grades = mysqli_query($conn, "SELECT g.*, CONCAT(s.firstname, ' ', s.lastname) as student_name, sub.code, 
                                           IFNULL(CONCAT(f.firstname, ' ', f.lastname), 'Unknown') as faculty_name, g.timestamp 
                                           FROM grades g
                                           JOIN student_list s ON g.student_id = s.id
                                           JOIN subject_list sub ON g.subject_id = sub.id
                                           LEFT JOIN faculty_list f ON g.faculty_id = f.id");

            // Displaying the data in the table.
            while($row = mysqli_fetch_assoc($grades)) {
              // Format the timestamp to "Month Day, Year" and 12-hour time format
              $formatted_timestamp = date('F j, Y, g:i A', strtotime($row['timestamp']));

              echo "<tr>
                      <td>{$row['student_name']}</td>
                      <td>{$row['code']}</td>
                      <td>{$row['grade']}</td>
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
