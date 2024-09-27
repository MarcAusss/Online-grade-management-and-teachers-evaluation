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

  /* Flex container for filters */
  .filter-container {
    display: flex;
    justify-content: space-between; /* Space between filters */
    align-items: center; /* Center vertically */
    margin-bottom: 20px; /* Space below filters */
  }

  /* Adjust input widths */
  #classFilter {
    flex: 1; /* Take available space */
    margin-right: 10px; /* Space between inputs */
  }

  #searchInput {
    flex: 2; /* Take more space than the class filter */
  }
</style>

<!-- Card Container for Table -->
<div class="card">
  <div class="card-header bg-primary text-white">
    <h4 class="mb-0">Submitted Grades</h4>
  </div>
  <div class="card-body">

    <!-- Flex Container for Filters -->
    <div class="filter-container">
      <!-- Class Filter -->
      <div class="form-group mb-0">
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

      <!-- Search Input -->
      <div class="form-group mb-0">
        <label for="searchInput" class="sr-only">Search by Student:</label>
        <input type="text" id="searchInput" class="form-control" placeholder="Search by Student Name...">
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-bordered table-hover" id="gradesTable">
        <thead>
          <tr>
            <th>Student</th>
            <th>Subject</th>
            <th>Grade</th>
            <th>Class</th>
            <th>Professor</th>
            <th>Submitted On</th>
          </tr>
        </thead>
        <tbody>
          <?php 
            // Set a variable to hold the class ID for filtering
            $class_id_filter = isset($_GET['class_id']) ? intval($_GET['class_id']) : '';

            // Base query to fetch grades and join with the student, subject, faculty, and class tables.
            $query = "SELECT g.*, 
                      CONCAT(s.firstname, ' ', s.lastname) as student_name, 
                      sub.code, 
                      IFNULL(CONCAT(f.firstname, ' ', f.lastname), 'Unknown') as faculty_name, 
                      c.curriculum, 
                      c.level, 
                      c.section, 
                      g.timestamp 
                      FROM grades g
                      JOIN student_list s ON g.student_id = s.id
                      JOIN subject_list sub ON g.subject_id = sub.id
                      LEFT JOIN faculty_list f ON g.faculty_id = f.id
                      JOIN class_list c ON s.class_id = c.id"; // Join with class_list

            // Add filtering condition if a specific class is selected
            if ($class_id_filter) {
              $query .= " WHERE s.class_id = $class_id_filter";
            }

            $grades = mysqli_query($conn, $query);

            // Displaying the data in the table.
            while($row = mysqli_fetch_assoc($grades)) {
              // Format the timestamp to "Month Day, Year" and 12-hour time format
              $formatted_timestamp = date('F j, Y, g:i A', strtotime($row['timestamp']));
              
              // Concatenate curriculum, level, and section for the Class column
              $class_info = "{$row['curriculum']} - Level {$row['level']} - Section {$row['section']}";

              echo "<tr>
                      <td>{$row['student_name']}</td>
                      <td>{$row['code']}</td>
                      <td>{$row['grade']}</td>
                      <td>{$class_info}</td>
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

    // Check if "All Classes" is selected
    if (classId === "") {
      // Redirect to the specific URL
      window.location.href = "http://localhost/php/eval/index.php?page=grade";
    } else {
      // Redirect to the same page with the selected class filter
      const currentPage = window.location.pathname; // Get the current page URL
      const params = new URLSearchParams(window.location.search); // Get existing query params
      params.set('class_id', classId); // Set the new class ID

      window.location.href = currentPage + '?' + params.toString(); // Redirect with updated params
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

