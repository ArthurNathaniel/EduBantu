<?php
include 'db.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $subject_id = $_POST['subject_id'];
    $subject_name = $_POST['subject_name'];

    // Check if the subject name already exists
    $checkSubject = "SELECT * FROM subjects WHERE subject_name = '$subject_name' AND id != $subject_id";
    $result = $conn->query($checkSubject);

    if ($result->num_rows > 0) {
        echo "<script>alert('Subject name already exists. Please choose a different name.'); window.location.href = 'add_subject.php';</script>";
    } else {
        // Update subject name
        $sql = "UPDATE subjects SET subject_name = '$subject_name' WHERE id = $subject_id";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Subject updated successfully!'); window.location.href = 'add_subject.php';</script>";
        } else {
            echo "<script>alert('Error: " . $conn->error . "'); window.location.href = 'add_subject.php';</script>";
        }
    }
}
?>
