<?php
session_start();
require 'db.php'; // Include database connection

// Get student ID from URL or redirect if not set
if (!isset($_GET['id'])) {
    header('Location: students.php'); // Redirect if ID is not provided
    exit();
}

$student_id = $_GET['id'];

// Fetch student details
$sql = "SELECT * FROM students WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    echo "<script>alert('Student not found!'); window.location.href = 'view_students.php';</script>";
    exit();
}

// Handle form submission to update student details
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $emergency_name = $_POST['emergency_name'];
    $emergency_phone = $_POST['emergency_phone'];
    $emergency_relation = $_POST['emergency_relation'];
    $class_name = $_POST['class_name'];
    $index_number = $_POST['index_number'];

    // Check for duplicates
    $duplicate_check_sql = "SELECT * FROM students WHERE index_number = ? AND id != ?";
    $duplicate_stmt = $conn->prepare($duplicate_check_sql);
    $duplicate_stmt->bind_param("si", $index_number, $student_id);
    $duplicate_stmt->execute();
    $duplicate_result = $duplicate_stmt->get_result();

    if ($duplicate_result->num_rows > 0) {
        echo "<script>alert('Error: Index number already exists for another student!');</script>";
    } else {
        // Handling profile image upload
        if ($_FILES['profile_image']['name']) {
            $target_dir = "uploads/students/";
            $image_name = time() . "_" . basename($_FILES["profile_image"]["name"]);
            $target_file = $target_dir . $image_name;
            move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file);
        } else {
            // Use existing image if no new image is uploaded
            $image_name = $student['profile_image'];
        }

        // Update student details in the database
        $update_sql = "UPDATE students SET name = ?, gender = ?, dob = ?, emergency_name = ?, emergency_phone = ?, emergency_relation = ?, class_name = ?, index_number = ?, profile_image = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sssssssssi", $name, $gender, $dob, $emergency_name, $emergency_phone, $emergency_relation, $class_name, $index_number, $image_name, $student_id);

        if ($update_stmt->execute()) {
            echo "<script>alert('Student details updated successfully!'); window.location.href = 'view_students.php';</script>";
        } else {
            echo "<script>alert('Error: " . $update_stmt->error . "');</script>";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <?php include '../cdn.php'; ?>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/view_students.css">
    <link rel="stylesheet" href="../css/register_student.css">
</head>

<body>
    <?php include 'sidebar.php' ?>
    <div class="edit_student_all">
        <h1>Edit Student</h1>

        <form action="edit_student.php?id=<?php echo $student_id; ?>" method="POST" enctype="multipart/form-data">

            <div class="forms_title">
                <h3>Personal Information</h3>
            </div>

            <div class="forms">
                <label for="profile_image">Profile Image:</label>
                <input type="file" name="profile_image" accept="image/*">
                <img src="uploads/students/<?php echo $student['profile_image']; ?>" alt="Profile Image" width="100">
            </div>

            <div class="forms_groups">
         
                <div class="forms">
                    <label for="name">Full Name:</label>
                    <input type="text" name="name" value="<?php echo $student['name']; ?>" required>
                </div>

                <div class="forms">
                    <label for="gender">Gender:</label>
                    <select name="gender" required>
                        <option value="Male" <?php if ($student['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                        <option value="Female" <?php if ($student['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                    </select>
                </div>

                <div class="forms">
                    <label for="dob">Date of Birth:</label>
                    <input type="date" name="dob" value="<?php echo $student['dob']; ?>" required>
                </div>
            </div>
          

            <div class="forms_title">
                <h3>Parent Information</h3>
            </div>

            <div class="forms_groups">
                <div class="forms">
                    <label for="emergency_name">Emergency Contact Name:</label>
                    <input type="text" name="emergency_name" value="<?php echo $student['emergency_name']; ?>" required>
                </div>

                <div class="forms">
                    <label for="emergency_phone">Emergency Phone Number:</label>
                    <input type="text" name="emergency_phone" value="<?php echo $student['emergency_phone']; ?>" required>
                </div>

                <div class="forms">
                    <label for="emergency_relation">Relation:</label>
                    <select name="emergency_relation" required>
                        <option value="Mother" <?php if ($student['emergency_relation'] == 'Mother') echo 'selected'; ?>>Mother</option>
                        <option value="Father" <?php if ($student['emergency_relation'] == 'Father') echo 'selected'; ?>>Father</option>
                        <option value="Guardian" <?php if ($student['emergency_relation'] == 'Guardian') echo 'selected'; ?>>Guardian</option>
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
                            $selected = $row['id'] == $student['class_name'] ? 'selected' : '';
                            echo "<option value='{$row['id']}' $selected>{$row['class_name']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="forms">
                    <label for="index_number">Index Number:</label>
                    <input type="text" name="index_number" value="<?php echo $student['index_number']; ?>" required>
                </div>
            </div>

            <div class="forms_groups">
                <div class="forms">
                    <button type="submit">Update Student</button>
                </div>
            </div>

        </form>
    </div>
</body>

</html>
