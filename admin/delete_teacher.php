<?php
include 'db.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Check if teacher ID is provided
if (isset($_GET['id'])) {
    $teacher_id = $_GET['id'];

    // Delete the teacher from the database
    $delete_sql = "DELETE FROM teachers WHERE id = '$teacher_id'";
    
    if ($conn->query($delete_sql) === TRUE) {
        echo "<script>
                alert('Teacher deleted successfully.');
                window.location.href = 'view_teachers.php';
              </script>";
    } else {
        echo "<script>
                alert('Error deleting teacher: " . $conn->error . "');
                window.location.href = 'view_teachers.php';
              </script>";
    }
} else {
    echo "<script>
            alert('Invalid request.');
            window.location.href = 'view_teachers.php';
          </script>";
}

$conn->close();
?>
