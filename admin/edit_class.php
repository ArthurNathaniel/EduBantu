<?php
include 'db.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $class_id = $_POST['class_id'];
    $class_name = $_POST['class_name'];

    // Check for duplicate class name
    $checkClass = "SELECT * FROM classes WHERE class_name = '$class_name' AND id != $class_id";
    $result = $conn->query($checkClass);

    if ($result->num_rows > 0) {
        echo "<script>alert('Class name already exists!'); window.location.href = 'add_class.php';</script>";
    } else {
        // Update the class name
        $sql = "UPDATE classes SET class_name = '$class_name' WHERE id = $class_id";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Class updated successfully!'); window.location.href = 'add_class.php';</script>";
        } else {
            echo "<script>alert('Error updating class: " . $conn->error . "'); window.location.href = 'add_class.php';</script>";
        }
    }
}
?>
