<?php
include 'db.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Fetch teacher details
if (isset($_GET['id'])) {
    $teacher_id = $_GET['id'];
    $teacher_sql = "SELECT * FROM teachers WHERE id = '$teacher_id'";
    $teacher_result = $conn->query($teacher_sql);

    if ($teacher_result->num_rows > 0) {
        $teacher = $teacher_result->fetch_assoc();
    } else {
        echo "Teacher not found.";
        exit();
    }
} else {
    echo "Invalid request.";
    exit();
}

// Initialize error message variable
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $house_number = $_POST['house_number'];
    $hometown = $_POST['hometown'];
    $emergency_contact_name = $_POST['emergency_contact_name'];
    $emergency_contact_phone = $_POST['emergency_contact_phone'];
    $emergency_contact_relationship = $_POST['emergency_contact_relationship'];
    $class_id = $_POST['class_id'] === 'none' ? NULL : $_POST['class_id']; // Set class_id to NULL if "None" is selected
    $education_level = $_POST['education_level'];
    $password = $_POST['password'];

    // Check for duplicate teacher records
    $duplicate_sql = "SELECT * FROM teachers WHERE (email = '$email' OR phone = '$phone') AND id != '$teacher_id'";
    $duplicate_result = $conn->query($duplicate_sql);

    if ($duplicate_result->num_rows > 0) {
        $error_message = "A teacher with the same email or phone number already exists.";
    } else {
        // Check for duplicate class assignment if class_id is not NULL
        if ($class_id !== NULL) {
            $class_assignment_sql = "SELECT * FROM teachers WHERE class_id = '$class_id' AND id != '$teacher_id'";
            $class_assignment_result = $conn->query($class_assignment_sql);

            if ($class_assignment_result->num_rows > 0) {
                $error_message = "This class is already assigned to another teacher.";
            } else {
                // Update teacher details
                $update_sql = "UPDATE teachers SET
                    first_name = '$first_name',
                    middle_name = '$middle_name',
                    last_name = '$last_name',
                    dob = '$dob',
                    gender = '$gender',
                    email = '$email',
                    phone = '$phone',
                    house_number = '$house_number',
                    hometown = '$hometown',
                    emergency_contact_name = '$emergency_contact_name',
                    emergency_contact_phone = '$emergency_contact_phone',
                    emergency_contact_relationship = '$emergency_contact_relationship',
                    class_id = $class_id,
                    education_level = '$education_level',
                    password = '$password'
                    WHERE id = '$teacher_id'";

                if ($conn->query($update_sql) === TRUE) {
                    header("Location: view_teachers.php");
                    exit();
                } else {
                    $error_message = "Error updating teacher: " . $conn->error;
                }
            }
        } else {
            // Update teacher details without checking class assignment
            $update_sql = "UPDATE teachers SET
                first_name = '$first_name',
                middle_name = '$middle_name',
                last_name = '$last_name',
                dob = '$dob',
                gender = '$gender',
                email = '$email',
                phone = '$phone',
                house_number = '$house_number',
                hometown = '$hometown',
                emergency_contact_name = '$emergency_contact_name',
                emergency_contact_phone = '$emergency_contact_phone',
                emergency_contact_relationship = '$emergency_contact_relationship',
                class_id = NULL,
                education_level = '$education_level',
                password = '$password'
                WHERE id = '$teacher_id'";

            if ($conn->query($update_sql) === TRUE) {
                header("Location: view_teachers.php");
                exit();
            } else {
                $error_message = "Error updating teacher: " . $conn->error;
            }
        }
    }
}

// Fetch all classes for the dropdown
$classes_sql = "SELECT * FROM classes";
$classes_result = $conn->query($classes_sql);
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
    <script>
        // Display error message as alert if it exists
        window.onload = function() {
            <?php if ($error_message) { echo "alert('$error_message');"; } ?>
        };
    </script>
</head>
<body>
<?php include 'sidebar.php'; ?>
    <div class="edit_teacher">
        <div class="forms_title">
            <h2>Edit Teacher Details</h2>
        </div>
        <form method="POST" action="">
            <div class="forms_groups">
                <div class="forms">
                    <label for="first_name">First Name:</label>
                    <input type="text" name="first_name" value="<?php echo $teacher['first_name']; ?>" required>
                </div>
                <div class="forms">
                    <label for="middle_name">Middle Name:</label>
                    <input type="text" name="middle_name" value="<?php echo $teacher['middle_name']; ?>">
                </div>
                <div class="forms">
                    <label for="last_name">Last Name:</label>
                    <input type="text" name="last_name" value="<?php echo $teacher['last_name']; ?>" required>
                </div>
                <div class="forms">
                    <label for="dob">Date of Birth:</label>
                    <input type="date" name="dob" value="<?php echo $teacher['dob']; ?>" required>
                </div>
                <div class="forms">
                    <label for="gender">Gender:</label>
                    <select name="gender" required>
                        <option value="Male" <?php echo $teacher['gender'] == 'Male' ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo $teacher['gender'] == 'Female' ? 'selected' : ''; ?>>Female</option>
                    </select>
                </div>
                <div class="forms">
                    <label for="email">Email:</label>
                    <input type="email" name="email" value="<?php echo $teacher['email']; ?>" required>
                </div>
                <div class="forms">
                    <label for="phone">Phone Number:</label>
                    <input type="text" name="phone" value="<?php echo $teacher['phone']; ?>" required>
                </div>
                <div class="forms">
                    <label for="house_number">House Number:</label>
                    <input type="text" name="house_number" value="<?php echo $teacher['house_number']; ?>" required>
                </div>
                <div class="forms">
                    <label for="hometown">Hometown:</label>
                    <input type="text" name="hometown" value="<?php echo $teacher['hometown']; ?>" required>
                </div>
                <div class="forms">
                    <label for="emergency_contact_name">Emergency Contact Name:</label>
                    <input type="text" name="emergency_contact_name" value="<?php echo $teacher['emergency_contact_name']; ?>" required>
                </div>
                <div class="forms">
                    <label for="emergency_contact_phone">Emergency Contact Phone:</label>
                    <input type="text" name="emergency_contact_phone" value="<?php echo $teacher['emergency_contact_phone']; ?>" required>
                </div>
                <div class="forms">
                    <label>Emergency Contact Relationship:</label>
                    <select name="emergency_contact_relationship" required>
                        <option value="" selected hidden>Select Emergency Contact Relationship</option>
                        <option value="Parent" <?php echo $teacher['emergency_contact_relationship'] == 'Parent' ? 'selected' : ''; ?>>Parent</option>
                        <option value="Sister" <?php echo $teacher['emergency_contact_relationship'] == 'Sister' ? 'selected' : ''; ?>>Sister</option>
                        <option value="Brother" <?php echo $teacher['emergency_contact_relationship'] == 'Brother' ? 'selected' : ''; ?>>Brother</option>
                        <option value="Friend" <?php echo $teacher['emergency_contact_relationship'] == 'Friend' ? 'selected' : ''; ?>>Friend</option>
                        <option value="Family" <?php echo $teacher['emergency_contact_relationship'] == 'Family' ? 'selected' : ''; ?>>Family</option>
                    </select>
                </div>
                <div class="forms">
                    <label for="class_id">Class Assigned:</label>
                    <select name="class_id" required>
                        <option value="none" <?php echo $teacher['class_id'] === NULL ? 'selected' : ''; ?>>None</option>
                        <?php while ($class = $classes_result->fetch_assoc()) { ?>
                            <option value="<?php echo $class['id']; ?>" <?php echo $teacher['class_id'] == $class['id'] ? 'selected' : ''; ?>>
                                <?php echo $class['class_name']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="forms">
                    <label for="education_level">Level of Education:</label>
                    <select name="education_level" required>
                        <option value="High School" <?php echo $teacher['education_level'] == 'High School' ? 'selected' : ''; ?>>High School</option>
                        <option value="Bachelor's" <?php echo $teacher['education_level'] == 'Bachelor\'s' ? 'selected' : ''; ?>>Bachelor's</option>
                        <option value="Master's" <?php echo $teacher['education_level'] == 'Master\'s' ? 'selected' : ''; ?>>Master's</option>
                        <option value="PhD" <?php echo $teacher['education_level'] == 'PhD' ? 'selected' : ''; ?>>PhD</option>
                    </select>
                </div>
                <div class="forms">
                    <label for="password">Password:</label>
                    <input type="password" name="password" >
                </div>
                <div class="forms">
                    <input type="submit" value="Update Teacher">
                </div>
            </div>
        </form>
    </div>
</body>
</html>
