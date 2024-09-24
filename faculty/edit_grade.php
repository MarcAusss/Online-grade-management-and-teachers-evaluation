<?php
include 'db_connect.php';

if (isset($_GET['id'])) {
    $grade_id = $_GET['id'];
    $grade_query = mysqli_query($conn, "SELECT * FROM grades WHERE id = '$grade_id'");
    $grade = mysqli_fetch_assoc($grade_query);
}

// Form for updating the grade
?>
<form action="update_grade_action.php" method="POST">
    <input type="hidden" name="id" value="<?= $grade['id'] ?>">
    <label for="grade">Edit Grade</label>
    <input type="number" name="grade" value="<?= $grade['grade'] ?>" step="0.01" min="0" max="100" required>
    <button type="submit">Update Grade</button>
</form>
