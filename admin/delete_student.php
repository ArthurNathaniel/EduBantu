<?php
session_start();
require 'db.php'; // Include your database connection file

// Check if the ID is set in the URL
if (isset($_GET['id'])) {
    $student_id = $_GET['id'];

    // Fetch the student record to get the image path
    $fetch_sql = "SELECT profile_image FROM students WHERE id = ?";
    $fetch_stmt = $conn->prepare($fetch_sql);
    $fetch_stmt->bind_param("i", $student_id);
    $fetch_stmt->execute();
    $result = $fetch_stmt->get_result();
    $student = $result->fetch_assoc();

    // Check if student exists
    if ($student) {
        // Define the path to the image
        $image_path = "uploads/students/" . $student['profile_image'];

        // Delete the image file from the server if it exists
        if (file_exists($image_path)) {
            unlink($image_path);
        }

        // Prepare the delete statement
        $delete_sql = "DELETE FROM students WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $student_id);

        // Execute the delete statement
        if ($delete_stmt->execute()) {
            // Set a success message
            $_SESSION['message'] = "Student deleted successfully!";
        } else {
            // Set an error message
            $_SESSION['message'] = "Error deleting student: " . $delete_stmt->error;
        }

        // Close the delete statement
        $delete_stmt->close();
    } else {
        // Set an error message if student does not exist
        $_SESSION['message'] = "Student not found!";
    }

    // Close the fetch statement
    $fetch_stmt->close();
} else {
    // Set an error message if ID is not set
    $_SESSION['message'] = "Invalid student ID!";
}

// Redirect back to the students view page
header('Location: view_students.php');
exit();
?>
