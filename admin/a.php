<?php
include 'db.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Check if ID is set
if (!isset($_GET['id'])) {
    echo "<script>alert('Teacher ID is missing!'); window.location.href = 'register_teacher.php';</script>";
    exit();
}

$teacher_id = $_GET['id'];

// Fetch teacher details
$sql = "SELECT * FROM teachers WHERE id = '$teacher_id'";
$result = $conn->query($sql);
$teacher = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $education_level = $_POST['education_level'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $house_number = $_POST['house_number'];
    $hometown = $_POST['hometown'];
    $emergency_contact_name = $_POST['emergency_contact_name'];
    $emergency_contact_phone = $_POST['emergency_contact_phone'];
    $emergency_contact_relationship = $_POST['emergency_contact_relationship'];
    $class_id = $_POST['class_id'];

    // Check for duplicate teacher registration
    $check_sql = "SELECT * FROM teachers WHERE (email = '$email' OR phone = '$phone') AND id != '$teacher_id'";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        echo "<script>alert('Teacher with this email or phone number already exists!'); window.location.href = 'edit_teacher.php?id=$teacher_id';</script>";
    } else {
        // Prepare the update statement
        $stmt = $conn->prepare("UPDATE teachers SET 
            first_name = ?, 
            middle_name = ?, 
            last_name = ?, 
            dob = ?, 
            gender = ?, 
            education_level = ?, 
            email = ?, 
            phone = ?, 
            house_number = ?, 
            hometown = ?, 
            emergency_contact_name = ?, 
            emergency_contact_phone = ?, 
            emergency_contact_relationship = ?, 
            class_id = ? 
            WHERE id = ?");

        // Check for statement preparation
        if (!$stmt) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }

        // Bind parameters
        $stmt->bind_param('ssssssssssssssi', 
            $first_name, 
            $middle_name, 
            $last_name, 
            $dob, 
            $gender, 
            $education_level, 
            $email, 
            $phone, 
            $house_number, 
            $hometown, 
            $emergency_contact_name, 
            $emergency_contact_phone, 
            $emergency_contact_relationship, 
            $class_id, 
            $teacher_id
        );

        // Execute the statement
        if ($stmt->execute()) {
            echo "<script>alert('Teacher updated successfully!'); window.location.href = 'register_teacher.php';</script>";
        } else {
            echo "<script>alert('Error: " . $stmt->error . "'); window.location.href = 'edit_teacher.php?id=$teacher_id';</script>";
        }

        // Close the statement
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Teacher</title>
    <?php include '../cdn.php'; ?>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/register_teacher.css">
</head>
<body>
<?php include 'sidebar.php' ?>
    <div class="register_teacher_all">
        <div class="forms_title">
            <h2>Edit Teacher</h2>
        </div>
        <form method="POST" action="edit_teacher.php?id=<?php echo $teacher_id; ?>">
            <div class="forms_groups">

                <div class="forms">
                    <label>First Name:</label>
                    <input type="text" placeholder="Enter your first name" name="first_name" value="<?php echo $teacher['first_name']; ?>" required>
                </div>

                <div class="forms">
                    <label>Middle Name:</label>
                    <input type="text" placeholder="Enter your middle name" name="middle_name" value="<?php echo $teacher['middle_name']; ?>">
                </div>

                <div class="forms">
                    <label>Last Name:</label>
                    <input type="text" placeholder="Enter your last name" name="last_name" value="<?php echo $teacher['last_name']; ?>" required>
                </div>

                <div class="forms">
                    <label>Date of Birth:</label>
                    <input type="date" name="dob" value="<?php echo $teacher['dob']; ?>" required>
                </div>

                <div class="forms">
                    <label>Gender:</label>
                    <select name="gender" required>
                        <option value="" selected hidden>Select Gender</option>
                        <option value="Male" <?php if($teacher['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                        <option value="Female" <?php if($teacher['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                    </select>
                </div>

                <div class="forms">
                    <label>Level of Education:</label>
                    <select name="education_level" required>
                        <option value="" selected hidden>Select Level of Education</option>
                        <option value="SHS" <?php if($teacher['education_level'] == 'SHS') echo 'selected'; ?>>SHS</option>
                        <option value="Diploma" <?php if($teacher['education_level'] == 'Diploma') echo 'selected'; ?>>Diploma</option>
                        <option value="HND" <?php if($teacher['education_level'] == 'HND') echo 'selected'; ?>>HND</option>
                        <option value="Degree" <?php if($teacher['education_level'] == 'Degree') echo 'selected'; ?>>Degree</option>
                        <option value="Master" <?php if($teacher['education_level'] == 'Master') echo 'selected'; ?>>Master</option>
                        <option value="PhD" <?php if($teacher['education_level'] == 'PhD') echo 'selected'; ?>>PhD</option>
                    </select>
                </div>

                <div class="forms">
                    <label>Email:</label>
                    <input type="email" placeholder="Enter your email address" name="email" value="<?php echo $teacher['email']; ?>" required>
                </div>

                <div class="forms">
                    <label>Phone Number:</label>
                    <input type="text" placeholder="Enter your phone number" name="phone" value="<?php echo $teacher['phone']; ?>" required>
                </div>

                <div class="forms">
                    <label>House Number:</label>
                    <input type="text" placeholder="Enter your house number" name="house_number" value="<?php echo $teacher['house_number']; ?>" required>
                </div>

                <div class="forms">
                    <label>Hometown:</label>
                    <input type="text" placeholder="Enter your hometown" name="hometown" value="<?php echo $teacher['hometown']; ?>" required>
                </div>

                <div class="forms">
                    <label>Emergency Contact Name:</label>
                    <input type="text" placeholder="Enter emergency contact name" name="emergency_contact_name" value="<?php echo $teacher['emergency_contact_name']; ?>" required>
                </div>

                <div class="forms">
                    <label>Emergency Contact Phone:</label>
                    <input type="text" placeholder="Enter emergency contact phone" name="emergency_contact_phone" value="<?php echo $teacher['emergency_contact_phone']; ?>" required>
                </div>

                <div class="forms">
                    <label>Emergency Contact Relationship:</label>
                    <input type="text" placeholder="Enter relationship" name="emergency_contact_relationship" value="<?php echo $teacher['emergency_contact_relationship']; ?>" required>
                </div>

                <div class="forms">
                    <label>Class:</label>
                    <select name="class_id" required>
                        <option value="" selected hidden>Select Class</option>
                        <?php
                        // Fetch classes from the database
                        $class_sql = "SELECT * FROM classes";
                        $class_result = $conn->query($class_sql);
                        while ($class = $class_result->fetch_assoc()) {
                            $selected = ($teacher['class_id'] == $class['id']) ? 'selected' : '';
                            echo "<option value='{$class['id']}' $selected>{$class['class_name']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form_btns">
                    <button type="submit" class="submit_btn">Update Teacher</button>
                </div>

            </div>
        </form>
    </div>
</body>
</html>
