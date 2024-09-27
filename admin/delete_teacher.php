<?php
include 'db.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $teacher_id = $_POST['teacher_id'];

    // Delete the teacher from the database
    $sql = "DELETE FROM teachers WHERE id=$teacher_id";
    
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Teacher deleted successfully!'); window.location.href = 'view_teachers.php';</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "'); window.location.href = 'view_teachers.php';</script>";
    }
}
?>
