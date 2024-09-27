<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password for security

    // Check if the email already exists
    $checkEmail = "SELECT * FROM admins WHERE email = '$email'";
    $result = $conn->query($checkEmail);

    if ($result->num_rows > 0) {
        // If email exists, show alert
        echo "<script>alert('Email already registered. Please use a different email.'); window.location.href = 'signup.php';</script>";
    } else {
        // Insert new admin if email is not duplicate
        $sql = "INSERT INTO admins (email, password) VALUES ('$email', '$password')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Admin registered successfully!'); window.location.href = 'login.php';</script>";
        } else {
            echo "<script>alert('Error: " . $conn->error . "'); window.location.href = 'signup.php';</script>";
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Signup</title>
</head>
<body>
    <h2>Admin Signup</h2>
    <form method="POST" action="signup.php">
        <label>Email:</label>
        <input type="email" name="email" required><br>
        
        <label>Password:</label>
        <input type="password" name="password" required><br>
        
        <button type="submit">Sign Up</button>
    </form>
</body>
</html>
