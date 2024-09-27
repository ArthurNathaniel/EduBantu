<?php
include 'db.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

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
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Check for duplicate teacher registration
    $check_sql = "SELECT * FROM teachers WHERE email = '$email' OR phone = '$phone'";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        echo "<script>alert('Teacher with this email or phone number already exists!'); window.location.href = 'register_teacher.php';</script>";
    } else {
        // Check for duplicate class assignment
        if ($class_id != 0) { // If a class is selected (not 'None')
            $class_check_sql = "SELECT * FROM teachers WHERE class_id = '$class_id'";
            $class_check_result = $conn->query($class_check_sql);

            if ($class_check_result->num_rows > 0) {
                echo "<script>alert('This class is already assigned to another teacher!'); window.location.href = 'register_teacher.php';</script>";
                exit();
            }
        }

        // Handle class assignment
        if ($class_id == 0) {
            $class_id = 'NULL'; // Set class_id to NULL if 'None' is selected
        } else {
            $class_id = "'" . $class_id . "'"; // Otherwise, wrap it in quotes
        }

        // Insert teacher data into the database
        $sql = "INSERT INTO teachers 
                (first_name, middle_name, last_name, dob, gender, education_level, email, phone, house_number, hometown, emergency_contact_name, emergency_contact_phone, emergency_contact_relationship, class_id, password)
                VALUES ('$first_name', '$middle_name', '$last_name', '$dob', '$gender', '$education_level', '$email', '$phone', '$house_number', '$hometown', '$emergency_contact_name', '$emergency_contact_phone', '$emergency_contact_relationship', $class_id, '$password')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Teacher registered successfully!'); window.location.href = 'register_teacher.php';</script>";
        } else {
            echo "<script>alert('Error: " . $conn->error . "'); window.location.href = 'register_teacher.php';</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Teacher</title>
    <?php include '../cdn.php'; ?>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/register_teacher.css">
</head>
<body>
<?php include 'sidebar.php' ?>
    <div class="register_teacher_all">
      <div class="forms_title">
      <h2>Register Teacher</h2>
      </div>
      <form method="POST" action="register_teacher.php">
        <div class="forms_groups">
        
        <div class="forms">
          <label>First Name:</label>
          <input type="text" placeholder="Enter your first name" name="first_name" required>
        </div>

        <div class="forms">
          <label>Middle Name:</label>
          <input type="text" placeholder="Enter your middle name" name="middle_name">
        </div>

        <div class="forms">
          <label>Last Name:</label>
          <input type="text" placeholder="Enter your last name" name="last_name" required>
        </div>
        
        <div class="forms">
          <label>Date of Birth:</label>
          <input type="date" name="dob" required>
        </div>

        <div class="forms">
          <label>Gender:</label>
          <select name="gender" required>
          <option value="" selected hidden>Select Gender</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
          </select>
        </div>

        <div class="forms">
          <label>Level of Education:</label>
          <select name="education_level" required>
          <option value="" selected hidden> Select Level of Education</option>
            <option value="SHS">SHS</option>
            <option value="Diploma">Diploma</option>
            <option value="HND">HND</option>
            <option value="Degree">Degree</option>
            <option value="Master">Master</option>
            <option value="PhD">PhD</option>
          </select>
        </div>

        <div class="forms">
          <label>Email:</label>
          <input type="email" placeholder="Enter your email address" name="email" required>
        </div>

        <div class="forms">
          <label>Phone Number:</label>
          <input type="text" placeholder="Enter your phone number" name="phone" required>
        </div>

        <div class="forms">
          <label>House Number:</label>
          <input type="text" placeholder="Enter your house number" name="house_number" required>
        </div>

        <div class="forms">
          <label>Hometown:</label>
          <input type="text" placeholder="Enter your hometown" name="hometown" required>
        </div>

        <div class="forms">
          <label>Emergency Contact Person Name:</label>
          <input type="text" placeholder="Enter your emergency contact person name" name="emergency_contact_name" required>
        </div>

        <div class="forms">
          <label>Emergency Contact Phone:</label>
          <input type="number" min="0" placeholder="Enter your emergency contact phone number" name="emergency_contact_phone" required>
        </div>

        <div class="forms">
          <label>Emergency Contact Relationship:</label>
          <select name="emergency_contact_relationship" required>
          <option value="" selected hidden>Select Emergency Contact Relationship</option>
            <option value="Parent">Parent</option>
            <option value="Sister">Sister</option>
            <option value="Brother">Brother</option>
            <option value="Friend">Friend</option>
            <option value="Family">Family</option>
          </select>
        </div>

        <div class="forms">
          <label>Assign Class:</label>
          <select name="class_id" required>
          <option value="" selected hidden>Assign Class</option>
            <option value="0">None</option> <!-- Add 'None' option -->
            <?php
            // Fetch classes from the database
            $sql = "SELECT * FROM classes";
            $result = $conn->query($sql);
            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . $row['id'] . "'>" . $row['class_name'] . "</option>";
            }
            ?>
          </select>
        </div>

        <div class="forms">
          <label>Password:</label>
          <input type="password" placeholder="Enter your password" name="password" required>
        </div>
        </div>
        <div class="forms">
          <button type="submit">Register Teacher</button>
        </div>
      </form>
    </div>
</body>
</html>
