<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if email exists in the database
    $sql = "SELECT * FROM admins WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $row['password'])) {
            // Start session and redirect to dashboard
            $_SESSION['admin'] = $row['email'];
            echo "<script>alert('Login successful!'); window.location.href = 'dashboard.php';</script>";
        } else {
            // Incorrect password
            echo "<script>alert('Invalid password. Please try again.'); window.location.href = 'login.php';</script>";
        }
    } else {
        // No user found with the provided email
        echo "<script>alert('No admin found with that email. Please try again.'); window.location.href = 'login.php';</script>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
</head>
<body>
    <h2>Admin Login</h2>
    <form method="POST" action="login.php">
        <label>Email:</label>
        <input type="email" name="email" required><br>
        
        <label>Password:</label>
        <input type="password" name="password" required><br>
        
        <button type="submit">Login</button>
    </form>
</body>
</html>
