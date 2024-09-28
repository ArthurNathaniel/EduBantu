<?php
require 'db.php';

if (isset($_GET['id'])) {
    $student_id = $_GET['id'];

    $sql = "SELECT students.name, students.gender, students.dob, students.emergency_name, students.emergency_phone, 
                    students.emergency_relation, students.class_name, students.index_number, students.profile_image, classes.class_name AS class_name
            FROM students
            JOIN classes ON students.class_name = classes.id
            WHERE students.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    
    if ($student) {
        echo "
        <img class='profile-pic' src='uploads/students/{$student['profile_image']}' alt='Profile Image'>
        <p><strong>Full Name:</strong> {$student['name']}</p>
        <p><strong>Gender:</strong> {$student['gender']}</p>
        <p><strong>Date of Birth:</strong> {$student['dob']}</p>
        <p><strong>Class:</strong> {$student['class_name']}</p>
        <h3>Parent Information</h3>
        <p><strong>Emergency Contact Name:</strong> {$student['emergency_name']}</p>
        <p><strong>Emergency Phone:</strong> {$student['emergency_phone']}</p>
        <p><strong>Relation:</strong> {$student['emergency_relation']}</p>
        <h3>Academic Information</h3>
        <p><strong>Index Number:</strong> {$student['index_number']}</p>";
    } else {
        echo "<p>No student found.</p>";
    }
}
?>
