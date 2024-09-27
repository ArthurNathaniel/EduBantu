<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'];

    $sql = "DELETE FROM teachers WHERE id='$id'";
    
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['message' => 'Teacher deleted successfully.']);
    } else {
        echo json_encode(['message' => 'Error deleting teacher: ' . $conn->error]);
    }
}
?>
