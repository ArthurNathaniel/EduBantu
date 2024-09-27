<?php
include 'db.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Assigned Subjects</title>
    <style>
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 10px;
        }
    </style>
</head>
<body>
    <h2>Assigned Subjects for Each Class</h2>
    <table>
        <thead>
            <tr>
                <th>Class Name</th>
                <th>Subjects</th>
            </tr>
        </thead>
        <tbody>
        <?php
        // Fetch all classes and their subjects
        $sql = "SELECT classes.class_name, GROUP_CONCAT(subjects.subject_name SEPARATOR ', ') as subjects
                FROM class_subjects
                JOIN classes ON class_subjects.class_id = classes.id
                JOIN subjects ON class_subjects.subject_id = subjects.id
                GROUP BY classes.class_name";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['class_name'] . "</td>";
                echo "<td>" . $row['subjects'] . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='2'>No subjects assigned to any class.</td></tr>";
        }
        ?>
        </tbody>
    </table>

    <p><a href="dashboard.php">Back to Dashboard</a></p>
</body>
</html>
