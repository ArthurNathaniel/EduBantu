<?php
include 'db.php';

$id = $_GET['id'];

// Modify the SQL query to join with the classes table
$sql = "SELECT teachers.*, classes.class_name 
        FROM teachers 
        LEFT JOIN classes ON teachers.class_id = classes.id 
        WHERE teachers.id = $id";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $teacher = $result->fetch_assoc();
    echo json_encode($teacher);
} else {
    // Return an error message or an empty object if no teacher found
    echo json_encode([]);
}
?>
