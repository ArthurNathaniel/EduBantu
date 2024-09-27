<?php
include 'db.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Fetch all teachers from the database
$teachers_sql = "SELECT * FROM teachers";
$teachers_result = $conn->query($teachers_sql);
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
    <style>
      
    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>
    <div class="view_teachers_all">
        <div class="forms_title">
            <h2>Registered Teachers</h2>
        </div>
        <table>
            <thead>
                <tr>
                    <th>First Name</th>
                    <th>Class Assigned</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($teachers_result->num_rows > 0) {
                    while ($teacher = $teachers_result->fetch_assoc()) {
                        // Fetch class name for the assigned class ID
                        $class_id = $teacher['class_id'];
                        $class_name = 'None'; // Default value if no class is assigned
                        if ($class_id != null) {
                            $class_sql = "SELECT class_name FROM classes WHERE id = '$class_id'";
                            $class_result = $conn->query($class_sql);
                            if ($class_result->num_rows > 0) {
                                $class = $class_result->fetch_assoc();
                                $class_name = $class['class_name'];
                            }
                        }

                        echo "<tr>
                        <td>{$teacher['first_name']}</td>
                        <td>$class_name</td>
                        <td class='actions_btn'>
                            <button onclick='openModal({$teacher['id']})'><i class='fa-solid fa-eye'></i></button>
                            <a href='edit_teacher.php?id={$teacher['id']}'><i class='fa-solid fa-user-pen'></i></a> 
                            <a href='delete_teacher.php?id={$teacher['id']}' onclick=\"return confirm('Are you sure you want to delete this teacher?');\"><i class='fa-solid fa-trash'></i></a>
                        </td>
                      </tr>";
                

                        // Modal for viewing teacher details
                        echo "
                        <div id='modal{$teacher['id']}' class='modal'>
                            <div class='modal-content'>
                                <span class='close' onclick='closeModal({$teacher['id']})'>&times;</span>
                                <h2>Teacher Details</h2>
                                <p>First Name: {$teacher['first_name']}</p>
                                <p>Middle Name: {$teacher['middle_name']}</p>
                                <p>Last Name: {$teacher['last_name']}</p>
                                <p>Date of Birth: {$teacher['dob']}</p>
                                <p>Gender: {$teacher['gender']}</p>
                                 <p>Education Level: {$teacher['education_level']}</p>
                                <p>Email: {$teacher['email']}</p>
                                <p>Phone Number: {$teacher['phone']}</p>
                                <p>House Number: {$teacher['house_number']}</p>
                                <p>Hometown: {$teacher['hometown']}</p>
                                <p>Emergency Contact Name: {$teacher['emergency_contact_name']}</p>
                                <p>Emergency Contact Phone: {$teacher['emergency_contact_phone']}</p>
                                <p>Emergency Contact Relationship: {$teacher['emergency_contact_relationship']}</p>
                                <p>Class Assigned: $class_name</p>
                            </div>
                        </div>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No teachers found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        function openModal(id) {
            document.getElementById('modal' + id).style.display = "block";
        }

        function closeModal(id) {
            document.getElementById('modal' + id).style.display = "none";
        }

        // Close the modal when the user clicks anywhere outside of it
        window.onclick = function(event) {
            var modals = document.getElementsByClassName('modal');
            for (let i = 0; i < modals.length; i++) {
                if (event.target == modals[i]) {
                    modals[i].style.display = "none";
                }
            }
        }
    </script>
</body>
</html>
