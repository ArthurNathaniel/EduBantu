<?php
session_start();
require 'db.php'; // Include your database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];

    // Delete accountant from the database
    $sql = "DELETE FROM accountants WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: view_accountants.php");
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}
?>
