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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <?php include '../cdn.php'; ?>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/login.css">
</head>

<body>
    <div class="login_all">
        <div class="login_box">
            <div class="logo"></div>
         <div class="forms_title">
         <h2>Admin Login</h2>
         </div>
            <form method="POST" action="login.php">
                <div class="forms">
                    <label>Email:</label>
                    <input type="email" placeholder="Enter your email address" name="email" required>
                </div>

                <div class="forms">
                    <label>Password:</label>
                    <input type="password" placeholder="Enter your password" name="password" required>
                </div>

                <div class="forms">
                    <button type="submit">Login</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>