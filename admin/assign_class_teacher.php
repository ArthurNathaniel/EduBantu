<?php
include 'db.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Fetch all teachers along with their class names from the database
$sql = "SELECT teachers.*, classes.class_name 
        FROM teachers 
        LEFT JOIN classes ON teachers.class_id = classes.id"; // Assuming teachers have class_id to join

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Teachers</title>
    <?php include '../cdn.php'; ?>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/view_teachers.css">
</head>
<body>
<?php include 'sidebar.php' ?>
    <div class="view_teachers_all">
        <div class="forms_title">
            <h2>View Teachers</h2>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Class Name</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['first_name'] . ' ' . ($row['middle_name'] ?? '') . ' ' . $row['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['class_name'] ?? 'N/A'); ?></td> <!-- Handle case where class_name might be null -->
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
