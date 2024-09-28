<?php
session_start();
require 'db.php'; // Include database connection

// Fetch students from the database
$sql = "SELECT students.id, students.name, students.gender, students.dob, students.emergency_name, students.emergency_phone, 
                students.emergency_relation, students.class_name, students.index_number, students.profile_image, classes.class_name AS class_name
        FROM students
        JOIN classes ON students.class_name = classes.id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student List</title>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/student_list.css">
    <style>
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            width: 50%;
        }

        .close-modal {
            float: right;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        img.profile-pic {
            width: 50px;
            height: 50px;
            border-radius: 50%;
        }
    </style>
</head>
<body>

    <h1>Student List</h1>

    <table>
        <thead>
            <tr>
                <th>Profile Image</th>
                <th>Name</th>
                <th>Class</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><img class="profile-pic" src="uploads/students/<?php echo $row['profile_image']; ?>" alt="Profile Image"></td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['class_name']; ?></td>
                <td>
                    <button onclick="viewDetails(<?php echo $row['id']; ?>)">View All Details</button>
                    <a href="edit_student.php?id=<?php echo $row['id']; ?>">Edit</a>
                    <a href="delete_student.php?id=<?php echo $row['id']; ?>" 
       onclick="return confirm('Are you sure you want to delete this student?');">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Modal -->
    <div class="modal" id="studentModal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">X</span>
            <h2>Student Details</h2>
            <div id="modal-body">
                <!-- Details will be loaded here using JS -->
            </div>
        </div>
    </div>

    <script>
        function viewDetails(studentId) {
            const modal = document.getElementById('studentModal');
            const modalBody = document.getElementById('modal-body');

            // Fetch student details via AJAX
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_student_details.php?id=' + studentId, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    modalBody.innerHTML = xhr.responseText;
                    modal.style.display = 'flex';
                }
            };
            xhr.send();
        }

        function closeModal() {
            const modal = document.getElementById('studentModal');
            modal.style.display = 'none';
        }
    </script>

</body>
</html>
