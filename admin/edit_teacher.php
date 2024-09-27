<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
    $class_id = $_POST['class_id'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password

    $sql = "UPDATE teachers SET first_name='$first_name', middle_name='$middle_name', last_name='$last_name', dob='$dob', gender='$gender', education_level='$education_level', email='$email', phone='$phone', house_number='$house_number', hometown='$hometown', emergency_contact_name='$emergency_contact_name', emergency_contact_phone='$emergency_contact_phone', emergency_contact_relationship='$emergency_contact_relationship', class_id='$class_id', password='$password' WHERE id='$id'";
    
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['message' => 'Teacher updated successfully.']);
    } else {
        echo json_encode(['message' => 'Error updating teacher: ' . $conn->error]);
    }
}
?>
