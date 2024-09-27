<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    // If not logged in, redirect to login page
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>
<body>
    <h2>Welcome to the Admin Dashboard</h2>
    
    <p>Hello, <?php echo $_SESSION['admin']; ?>! You are logged in as an admin.</p>

    <p><a href="logout.php">Logout</a></p>

    <!-- Admin functionalities can be added here -->
    <div>
        <h3>Admin Actions</h3>
        <ul>
            <li><a href="add_student.php">Add Student</a></li>
            <li><a href="view_reports.php">View Student Reports</a></li>
            <!-- Add more admin functionality links as needed -->
        </ul>
    </div>
</body>
</html>
