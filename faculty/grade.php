<?php $faculty_id = $_SESSION['login_id'] ?>
<?php 
// grade.php (Faculty Grade Management)

// Fetch students and subjects for dropdown
$students = mysqli_query($conn, "SELECT id, firstname, lastname FROM student_list");
$subjects = mysqli_query($conn, "SELECT id, code FROM subject_list");

?>

<div class="container">
    <h2 class="text-center mt-4">Grade Management</h2>

    <!-- Grade Submission Form -->
    <div class="card mt-4">
        <div class="card-header">
            <h4>Submit Grades</h4>
        </div>
        <div class="card-body">
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
                        <?php while($row = mysqli_fetch_assoc($subjects)): ?>
                            <option value="<?= $row['id'] ?>"><?= $row['code'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- New Term Field -->
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
                    <input type="number" class="form-control" name="grade" step="0.01" min="60" max="100" required>
                </div>

                <button type="submit" class="btn btn-primary">Submit Grade</button>
            </form>
        </div>
    </div>

    <!-- List of Submitted Grades -->
    <div class="card mt-5">
        <div class="card-header">
            <h4>Submitted Grades</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Subject</th>
                            <th>Term</th> <!-- New column for Term -->
                            <th>Grade</th>
                            <th>Submitted On</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch submitted grades
                        $grades = mysqli_query($conn, "SELECT g.id, CONCAT(s.firstname, ' ', s.lastname) AS student_name, sub.code, g.term, g.grade, g.timestamp 
                                                       FROM grades g
                                                       JOIN student_list s ON g.student_id = s.id
                                                       JOIN subject_list sub ON g.subject_id = sub.id");

                        while($row = mysqli_fetch_assoc($grades)): ?>
                            <tr>
                                <td><?= $row['student_name'] ?></td>
                                <td><?= $row['code'] ?></td>
                                <td><?= $row['term'] ?></td> <!-- Display term -->
                                <td><?= $row['grade'] ?></td>
                                <td><?= $row['timestamp'] ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning edit-grade" data-id="<?= $row['id'] ?>" data-grade="<?= $row['grade'] ?>">Edit</button>
                                    <button class="btn btn-sm btn-danger delete-grade" data-id="<?= $row['id'] ?>">Delete</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
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
});

 // Edit Grade
 $('.edit-grade').on('click', function() {
        var grade_id = $(this).data('id');
        var current_grade = $(this).data('grade');

        var new_grade = prompt("Enter new grade:", current_grade);
        if (new_grade !== null) {
            $.ajax({
                type: 'POST',
                url: 'ajax.php?action=edit_grade',
                data: { grade_id: grade_id, grade: new_grade },
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
        var grade_id = $(this).data('id');
        if (confirm("Are you sure you want to delete this grade?")) {
            $.ajax({
                type: 'POST',
                url: 'ajax.php?action=delete_grade',
                data: { grade_id: grade_id },
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
</script>
