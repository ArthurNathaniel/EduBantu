<?php
include 'db.php';

$id = $_GET['id'];
$sql = "SELECT * FROM teachers WHERE id = $id";
$result = $conn->query($sql);
$teacher = $result->fetch_assoc();

echo json_encode($teacher);
?>
