<?php
// submit_grade_action.php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $subject_id = $_POST['subject_id'];
    $grade = $_POST['grade'];
    $faculty_id = $_SESSION['faculty_id']; // Get faculty ID from session

    // Insert the grade into the database
    $query = "INSERT INTO grades (student_id, subject_id, faculty_id, grade)
              VALUES ('$student_id', '$subject_id', '$faculty_id', '$grade')";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Grade Submitted Successfully'); window.location.href = 'grade.php';</script>";
    } else {
        echo "<script>alert('Error Submitting Grade'); window.location.href = 'grade.php';</script>";
    }
}
?>
