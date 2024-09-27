<?php
include 'db.php';

function respond($success, $message) {
    echo json_encode(['success' => $success, 'message' => $message]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize input data
    $id = $_POST['id'];
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
    
    // Handle class_id
    $class_id = ($_POST['class_id'] === 'none') ? NULL : $_POST['class_id'];
    
    $password = $_POST['password'];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if class_id is valid
    if ($class_id !== NULL) {
        $class_check_sql = "SELECT id FROM classes WHERE id = ?";
        $stmt = $conn->prepare($class_check_sql);
        $stmt->bind_param("i", $class_id);
        $stmt->execute();
        $class_check_result = $stmt->get_result();

        if ($class_check_result->num_rows === 0) {
            respond(false, 'Invalid class ID.');
        }
        $stmt->close();
    }

    // Prepare the update SQL statement
    $update_sql = "UPDATE teachers SET 
        first_name=?, 
        middle_name=?, 
        last_name=?, 
        dob=?, 
        gender=?, 
        education_level=?, 
        email=?, 
        phone=?, 
        house_number=?, 
        hometown=?, 
        emergency_contact_name=?, 
        emergency_contact_phone=?, 
        emergency_contact_relationship=?, 
        class_id=?, 
        password=? 
    WHERE id=?";

    $stmt = $conn->prepare($update_sql);
    
    // Bind parameters based on class_id being NULL or not
    if ($class_id === NULL) {
        $stmt->bind_param("ssssssssssssssi", 
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
            $hashed_password, 
            $id
        );
    } else {
        $stmt->bind_param("ssssssssssssssi", 
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
            $hashed_password, 
            $id
        );
    }

    // Execute the statement and handle response
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            respond(true, 'Teacher details updated successfully.');
        } else {
            respond(false, 'No changes made to the teacher details.');
        }
    } else {
        respond(false, 'Failed to update teacher details: ' . $stmt->error);
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>
