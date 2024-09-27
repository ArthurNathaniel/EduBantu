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
    $name = $_POST['name'];
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
    $subject_id = $_POST['subject_id'];
    $class_id = $_POST['class_id'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Insert teacher data into the database
    $sql = "INSERT INTO teachers 
            (name, dob, gender, education_level, email, phone, house_number, hometown, emergency_contact_name, emergency_contact_phone, emergency_contact_relationship, subject_id, class_id, password)
            VALUES ('$name', '$dob', '$gender', '$education_level', '$email', '$phone', '$house_number', '$hometown', '$emergency_contact_name', '$emergency_contact_phone', '$emergency_contact_relationship', $subject_id, $class_id, '$password')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Teacher registered successfully!'); window.location.href = 'register_teacher.php';</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "'); window.location.href = 'register_teacher.php';</script>";
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
          <label>Name:</label>
          <input type="text" name="name" required>
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
          <option value="" selected hidden>Level of Education</option>
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
          <input type="email" name="email" required>
        </div>

        <div class="forms">
          <label>Phone Number:</label>
          <input type="text" name="phone" required>
        </div>

        <div class="forms">
          <label>House Number:</label>
          <input type="text" name="house_number" required>
        </div>

        <div class="forms">
          <label>Hometown:</label>
          <input type="text" name="hometown" required>
        </div>

        <div class="forms">
          <label>Emergency Contact Person Name:</label>
          <input type="text" name="emergency_contact_name" required>
        </div>

        <div class="forms">
          <label>Emergency Contact Phone:</label>
          <input type="text" name="emergency_contact_phone" required>
        </div>

        <div class="forms">
          <label>Emergency Contact Relationship:</label>
          <select name="emergency_contact_relationship" required>
          <option value="" selected hidden>Emergency Contact Relationship</option>
            <option value="Parent">Parent</option>
            <option value="Sister">Sister</option>
            <option value="Brother">Brother</option>
            <option value="Friend">Friend</option>
            <option value="Family">Family</option>
          </select>
        </div>

        <div class="forms">
          <label>Assign Subject:</label>
          <select name="subject_id" required>
            <?php
            // Fetch subjects from the database
            $sql = "SELECT * FROM subjects";
            $result = $conn->query($sql);
            while ($row = $result->fetch_assoc()) {
                echo'<option value="" selected hidden>Assign Subject</option>';
                echo "<option value='" . $row['id'] . "'>" . $row['subject_name'] . "</option>";
            }
            ?>
          </select>
        </div>

        <div class="forms">
          <label>Assign Class:</label>
          <select name="class_id" required>
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
          <input type="password" name="password" required>
        </div>
        </div>
        <div class="forms">
          <button type="submit">Register Teacher</button>
        </div>
      </form>
    </div>
</body>
</html>
