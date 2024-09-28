<?php
session_start();
require 'db.php'; // Include database connection

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $emergency_name = $_POST['emergency_name'];
    $emergency_phone = $_POST['emergency_phone'];
    $emergency_relation = $_POST['emergency_relation'];
    $class_name = $_POST['class_name'];
    $index_number = $_POST['index_number'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Secure password hashing

    // Handling profile image upload
    if ($_FILES['profile_image']['name']) {
        $target_dir = "uploads/students/";
        $image_name = time() . "_" . basename($_FILES["profile_image"]["name"]);
        $target_file = $target_dir . $image_name;
        move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file);
    } else {
        // If no image is uploaded, use a default image
        $image_name = 'default.png';
    }

    // Check for duplicate index_number
    $check_sql = "SELECT * FROM students WHERE index_number = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $index_number);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        // Index number already exists, trigger JavaScript alert
        echo "<script>alert('Error: The index number \"$index_number\" is already registered. Please use a different index number.');</script>";
    } else {
        // Insert into the database
        $sql = "INSERT INTO students (name, gender, dob, emergency_name, emergency_phone, emergency_relation, class_name, index_number, password, profile_image) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssss", $name, $gender, $dob, $emergency_name, $emergency_phone, $emergency_relation, $class_name, $index_number, $password, $image_name);

        if ($stmt->execute()) {
            echo "<script>alert('Student registered successfully!');</script>";
        } else {
            echo "<script>alert('Error: " . $stmt->error . "');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Student</title>
    <?php include '../cdn.php'; ?>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/register_student.css">
</head>

<body>
    <?php include 'sidebar.php' ?>
    <div class="register_student_all">
        <h1>Register Student</h1>

        <form action="register_student.php" method="POST" enctype="multipart/form-data">

            <div class="forms_title">
                <h3>Personal Information</h3>
            </div>

            <div class="forms">
                <label for="profile_image">Profile Image:</label>
                <input type="file" name="profile_image" accept="image/*" required>
            </div>

            <div class="forms_groups">
                <div class="forms">
                    <label for="name">Full Name:</label>
                    <input type="text" name="name" required>
                </div>

                <div class="forms">
                    <label for="gender">Gender:</label>
                    <select name="gender" required>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>

                <div class="forms">
                    <label for="dob">Date of Birth:</label>
                    <input type="date" name="dob" required>
                </div>
            </div>

            <div class="forms_title">
                <h3>Parent Information</h3>
            </div>

            <div class="forms_groups">
                <div class="forms">
                    <label for="emergency_name">Emergency Contact Name:</label>
                    <input type="text" name="emergency_name" required>
                </div>

                <div class="forms">
                    <label for="emergency_phone">Emergency Phone Number:</label>
                    <input type="text" name="emergency_phone" required>
                </div>

                <div class="forms">
                    <label for="emergency_relation">Relation:</label>
                    <select name="emergency_relation" required>
                        <option value="Mother">Mother</option>
                        <option value="Father">Father</option>
                        <option value="Guardian">Guardian</option>
                    </select>
                </div>
            </div>

            <div class="forms_title">
                <h3>Academic Information</h3>
            </div>
            <div class="forms_groups">
                <div class="forms">
                    <label for="class_name">Class Name:</label>
                    <select name="class_name" required>
                        <?php
                        // Fetch classes from the database
                        $sql = "SELECT id, class_name FROM classes";
                        $result = $conn->query($sql);
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='{$row['id']}'>{$row['class_name']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="forms">
                    <label for="index_number">Index Number:</label>
                    <input type="text" name="index_number" required>
                </div>
                
                <div class="forms">
                    <label for="password">Password:</label>
                    <input type="password" name="password" required>
                </div>
            </div>

            <div class="forms_groups">
                <div class="forms">
                    <button type="submit">Register Student</button>
                </div>
            </div>

        </form>
    </div>
</body>

</html>
