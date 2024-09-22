<?php
// upload_file.php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Directory where the file will be uploaded
    $uploadDir = 'uploads/';
    // Ensure the upload directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Path where the file will be saved
    $uploadFile = $uploadDir . basename($_FILES['file']['name']);

    // Check if the file was uploaded without errors
    if ($_FILES['file']['error'] == UPLOAD_ERR_OK) {
        // Move the uploaded file to the designated directory
        if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
            echo "File successfully uploaded: " . htmlspecialchars(basename($_FILES['file']['name']));
        } else {
            echo "File upload failed. Please try again.";
        }
    } else {
        echo "File upload error: " . $_FILES['file']['error'];
    }
} else {
    echo "No file uploaded.";
}
?>
