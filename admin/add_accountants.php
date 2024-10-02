<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

require 'db.php'; // Include your database connection

$full_name = $gender = $date_of_birth = $email = $password = "";
$errors = [];
$success = false; // To track success

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs
    $full_name = trim($_POST['full_name']);
    $gender = $_POST['gender'];
    $date_of_birth = $_POST['date_of_birth'];
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validation
    if (empty($full_name)) {
        $errors[] = "Full Name is required.";
    }

    if (empty($gender)) {
        $errors[] = "Gender is required.";
    }

    if (empty($date_of_birth)) {
        $errors[] = "Date of Birth is required.";
    }

    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    // Check if email already exists
    $email_check_sql = "SELECT * FROM accountants WHERE email = ?";
    $stmt = $conn->prepare($email_check_sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $errors[] = "Email is already registered.";
    }

    // If no errors, insert the accountant into the database
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password

        $sql = "INSERT INTO accountants (full_name, gender, date_of_birth, email, password) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $full_name, $gender, $date_of_birth, $email, $hashed_password);
        
        if ($stmt->execute()) {
            $success = true; // Mark success
        } else {
            $errors[] = "Error: Could not register accountant.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Accountant</title>
    <?php include '../cdn.php'; ?>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/accountant.css">
    <script>
        // Success alert
        <?php if ($success): ?>
        alert("Accountant registered successfully!");
        <?php endif; ?>

        // Error alert
        <?php if (!empty($errors)): ?>
        alert("<?php echo implode("\\n", $errors); ?>");
        <?php endif; ?>
    </script>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="accountant_all">
        <div class="forms_title">
            <h2>Register Accountant</h2>
        </div>

        <form action="add_accountants.php" method="POST">
            <div class="forms">
                <label for="full_name">Full Name:</label>
                <input type="text" name="full_name" id="full_name" value="<?php echo htmlspecialchars($full_name); ?>" required>
            </div>

            <div class="forms">
                <label for="gender">Gender:</label>
                <select name="gender" id="gender" required>
                    <option value="Male" <?php echo ($gender == 'Male') ? 'selected' : ''; ?>>Male</option>
                    <option value="Female" <?php echo ($gender == 'Female') ? 'selected' : ''; ?>>Female</option>
                </select>
            </div>

            <div class="forms">
                <label for="date_of_birth">Date of Birth:</label>
                <input type="date" name="date_of_birth" id="date_of_birth" value="<?php echo htmlspecialchars($date_of_birth); ?>" required>
            </div>

            <div class="forms">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>

            <div class="forms">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
            </div>

            <div class="forms">
                <button type="submit">Register Accountant</button>
            </div>
        </form>

    </div>
</body>
</html>
