<?php $faculty_id = $_SESSION['login_id'] ?>
<?php 
// grade.php (Faculty Grade Management)

// Fetch students and subjects for dropdown
$students = mysqli_query($conn, "SELECT id, firstname, lastname FROM student_list");
$subjects = mysqli_query($conn, "SELECT id, code FROM subject_list");
$subjects_for_modal = mysqli_query($conn, "SELECT id, code FROM subject_list");
// Assuming academic years are already defined somewhere
$academic_years = ['2022-2023', '2023-2024']; // Example years
?>

<div class="container mt-4">
    <h2 class="text-center">Submitted Grades</h2>

    <!-- Button to trigger the grade submission modal -->
    <button type="button" class="btn btn-primary mb-4" data-toggle="modal" data-target="#submitGradeModal">
        Submit Grades
    </button>

    <!-- Filters and Search -->
    <div class="row mb-4">

    
        <div class="col-md-4 ">
            <label for="name-search">Search by Student Name</label>
            <input type="text" class="form-control" id="name-search" placeholder="Enter student name...">
        </div>
        
        <div class="col-md-2 ml-auto">
            <label for="subject-filter">Filter by Subject</label>
            <select class="form-control" id="subject-filter">
                <option value="">All Subjects</option>
                <?php while($row = mysqli_fetch_assoc($subjects)): ?>
                    <option value="<?= $row['code'] ?>"><?= $row['code'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="col-md-2 ">
            <label for="year-filter">Filter by Academic Year</label>
            <select class="form-control" id="year-filter">
                <option value="">All Academic Years</option>
                <?php foreach($academic_years as $year): ?>
                    <option value="<?= $year ?>"><?= $year ?></option>
                <?php endforeach; ?>
            </select>
        </div>

    </div>

    <!-- Submitted Grades Table -->
    <div class="card">
        <div class="card-header">
            <h4>Submitted Grades</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="grades-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Subject</th>
                            <th>Prelim</th>
                            <th>Midterm</th>
                            <th>Pre-Finals</th>
                            <th>Finals</th>
                            <th>Submitted On</th> <!-- Add Submitted On column -->
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch submitted grades for each term
                        $grades = mysqli_query($conn, "
                        SELECT g.student_id, g.subject_id, CONCAT(s.firstname, ' ', s.lastname) AS student_name, 
                               sub.code, 
                               MAX(CASE WHEN g.term = 'Prelim' THEN g.grade END) AS Prelim,
                               MAX(CASE WHEN g.term = 'Midterm' THEN g.grade END) AS Midterm,
                               MAX(CASE WHEN g.term = 'Pre-Finals' THEN g.grade END) AS PreFinals,
                               MAX(CASE WHEN g.term = 'Finals' THEN g.grade END) AS Finals,
                               g.timestamp AS submitted_on
                        FROM grades g
                        JOIN student_list s ON g.student_id = s.id
                        JOIN subject_list sub ON g.subject_id = sub.id
                        GROUP BY g.student_id, g.subject_id
                    ");

                        while($row = mysqli_fetch_assoc($grades)): ?>
                            <tr>
                                <td><?= $row['student_name'] ?></td>
                                <td><?= $row['code'] ?></td>
                                <td><?= $row['Prelim'] ?? 'N/A' ?></td>
                                <td><?= $row['Midterm'] ?? 'N/A' ?></td>
                                <td><?= $row['PreFinals'] ?? 'N/A' ?></td>
                                <td><?= $row['Finals'] ?? 'N/A' ?></td>
                                <td><?= date('Y-m-d H:i:s', strtotime($row['submitted_on'])) ?></td> <!-- Display the timestamp -->
                                <td>
                                    <button class="btn btn-sm btn-warning edit-grade" data-id="<?= $row['student_id'] ?>" data-subject="<?= $row['subject_id'] ?>">Edit</button>
                                    <button class="btn btn-sm btn-danger delete-grade" data-id="<?= $row['student_id'] ?>" data-subject="<?= $row['subject_id'] ?>">Delete</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Grade Submission Modal -->
    <div class="modal fade" id="submitGradeModal" tabindex="-1" role="dialog" aria-labelledby="submitGradeModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="submitGradeModalLabel">Submit Grades</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="submit_grade_action.php" method="POST" id="submit-grade-form">
                        <div class="form-group">
                            <label for="student">Select Student</label>
                            <select class="form-control" name="student_id" required>
                                <option value="" disabled selected>Select Student</option>
                                <?php while($row = mysqli_fetch_assoc($students)): ?>
                                    <option value="<?= $row['id'] ?>"><?= $row['firstname'] . ' ' . $row['lastname'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="subject">Select Subject</label>
                            <select class="form-control" name="subject_id" required>
                                <option value="" disabled selected>Select Subject</option>
                                <?php while($row = mysqli_fetch_assoc($subjects_for_modal)): ?>
                                    <option value="<?= $row['id'] ?>"><?= $row['code'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="term">Select Term</label>
                            <select class="form-control" name="term" required>
                                <option value="" disabled selected>Select Term</option>
                                <option value="Prelim">Prelim</option>
                                <option value="Midterm">Midterm</option>
                                <option value="Pre-Finals">Pre-Finals</option>
                                <option value="Finals">Finals</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="grade">Enter Grade</label>
                            <input type="number" class="form-control" name="grade" step="0.01" min="1" max="5" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Submit Grade</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Filter and Search Logic
    function filterTable() {
        var subject = $('#subject-filter').val().toLowerCase();
        var year = $('#year-filter').val();
        var searchValue = $('#name-search').val().toLowerCase();

        $('#grades-table tbody tr').filter(function() {
            var student = $(this).find('td:eq(0)').text().toLowerCase();
            var subjectCode = $(this).find('td:eq(1)').text().toLowerCase();

            $(this).toggle(
                (subject === "" || subjectCode.includes(subject)) && 
                (year === "" || subjectCode.includes(year)) && 
                (searchValue === "" || student.includes(searchValue))
            );
        });
    }

    $('#subject-filter, #year-filter, #name-search').on('keyup change', function() {
        filterTable();
    });

    // Grade submission via AJAX
    $('#submit-grade-form').on('submit', function(e) {
        e.preventDefault(); // Prevent the default form submission

        $.ajax({
            type: 'POST',
            url: 'ajax.php?action=submit_grade',
            data: $(this).serialize(),
            success: function(response) {
                alert(response); // Show success or error message
                location.reload(); // Reload the page to refresh the grades list
            },
            error: function() {
                alert('Error submitting grade.');
            }
        });
    });

    // Edit Grade
    $('.edit-grade').on('click', function() {
        var student_id = $(this).data('id');
        var subject_id = $(this).data('subject');
        var term = prompt("Enter the term (Prelim, Midterm, Pre-Finals, Finals):");
        var current_grade = prompt("Enter new grade:");

        if (term && current_grade) {
            $.ajax({
                type: 'POST',
                url: 'ajax.php?action=edit_grade',
                data: { student_id: student_id, subject_id: subject_id, term: term, grade: current_grade },
                success: function(response) {
                    alert(response);
                    location.reload();
                },
                error: function() {
                    alert('Error editing grade.');
                }
            });
        }
    });

    // Delete Grade
    $('.delete-grade').on('click', function() {
        var student_id = $(this).data('id');
        var subject_id = $(this).data('subject');
        if (confirm("Are you sure you want to delete this grade?")) {
            $.ajax({
                type: 'POST',
                url: 'ajax.php?action=delete_grade',
                data: { student_id: student_id, subject_id: subject_id },
                success: function(response) {
                    alert(response);
                    location.reload();
                },
                error: function() {
                    alert('Error deleting grade.');
                }
            });
        }
    });
});
</script>

<!-- Include Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
