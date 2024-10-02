<?php
session_start();
require 'db.php'; // Include your database connection

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $full_name = $_POST['full_name'];
    $gender = $_POST['gender'];
    $date_of_birth = $_POST['date_of_birth'];
    $email = $_POST['email'];

    // Update query without password
    $sql = "UPDATE accountants SET full_name = ?, gender = ?, date_of_birth = ?, email = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $full_name, $gender, $date_of_birth, $email, $id);

    // Check if a new password was provided
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password
        $sql = "UPDATE accountants SET full_name = ?, gender = ?, date_of_birth = ?, email = ?, password = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $full_name, $gender, $date_of_birth, $email, $password, $id);
    }

    if ($stmt->execute()) {
        // Redirect back to the accountant list with a success message
        header("Location: view_accountants.php?success=Accountant updated successfully");
        exit();
    } else {
        // Redirect back with an error message
        header("Location: view_accountants.php?error=Error updating accountant");
        exit();
    }
}
?>
