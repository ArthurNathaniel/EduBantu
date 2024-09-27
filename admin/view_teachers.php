<?php
include 'db.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Fetch all teachers from the database
$sql = "SELECT t.id, t.name, t.dob, t.gender, t.education_level, t.email, t.phone, t.house_number, t.hometown, 
        t.emergency_contact_name, t.emergency_contact_phone, t.emergency_contact_relationship, 
        s.subject_name, c.class_name 
        FROM teachers t 
        JOIN subjects s ON t.subject_id = s.id 
        JOIN classes c ON t.class_id = c.id";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Teachers</title>
    <style>
        /* Basic CSS for table and modal */
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

        th {
            background-color: #f2f2f2;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            width: 50%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .modal-buttons {
            margin-top: 20px;
        }

        .modal-buttons button {
            padding: 10px 20px;
            margin-right: 10px;
        }
    </style>
</head>
<body>

<h2>Teacher List</h2>

<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Date of Birth</th>
            <th>Gender</th>
            <th>Education Level</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Subject</th>
            <th>Class</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['dob']; ?></td>
            <td><?php echo $row['gender']; ?></td>
            <td><?php echo $row['education_level']; ?></td>
            <td><?php echo $row['email']; ?></td>
            <td><?php echo $row['phone']; ?></td>
            <td><?php echo $row['subject_name']; ?></td>
            <td><?php echo $row['class_name']; ?></td>
            <td>
                <button onclick="openEditModal(<?php echo $row['id']; ?>)">Edit</button>
                <button onclick="openDeleteModal(<?php echo $row['id']; ?>)">Delete</button>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('editModal')">&times;</span>
        <h2>Edit Teacher</h2>
        <form id="editForm" method="POST" action="edit_teacher.php">
            <input type="hidden" name="teacher_id" id="editTeacherId">
            <label for="editName">Name:</label>
            <input type="text" id="editName" name="name" required><br><br>
            <label for="editDob">Date of Birth:</label>
            <input type="date" id="editDob" name="dob" required><br><br>
            <label for="editGender">Gender:</label>
            <select id="editGender" name="gender" required>
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select><br><br>
            <label for="editEducationLevel">Education Level:</label>
            <select id="editEducationLevel" name="education_level" required>
                <option value="shs">SHS</option>
                <option value="diploma">Diploma</option>
                <option value="hnd">HND</option>
                <option value="degree">Degree</option>
                <option value="master">Master</option>
                <option value="phd">PhD</option>
            </select><br><br>
            <label for="editEmail">Email:</label>
            <input type="email" id="editEmail" name="email" required><br><br>
            <label for="editPhone">Phone:</label>
            <input type="tel" id="editPhone" name="phone" required><br><br>
            <label for="editHouseNumber">House Number:</label>
            <input type="text" id="editHouseNumber" name="house_number" required><br><br>
            <label for="editHometown">Hometown:</label>
            <input type="text" id="editHometown" name="hometown" required><br><br>
            <label for="editEmergencyName">Emergency Contact Name:</label>
            <input type="text" id="editEmergencyName" name="emergency_contact_name" required><br><br>
            <label for="editEmergencyPhone">Emergency Contact Phone:</label>
            <input type="tel" id="editEmergencyPhone" name="emergency_contact_number" required><br><br>
            <label for="editEmergencyRelationship">Emergency Contact Relationship:</label>
            <select id="editEmergencyRelationship" name="emergency_contact_relationship" required>
                <option value="parent">Parent</option>
                <option value="sister">Sister</option>
                <option value="brother">Brother</option>
                <option value="friend">Friend</option>
                <option value="family">Family</option>
            </select><br><br>
            <label for="editPassword">Password:</label>
            <input type="password" id="editPassword" name="password"><br><br>
            <div class="modal-buttons">
                <button type="submit">Save Changes</button>
                <button type="button" onclick="closeModal('editModal')">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('deleteModal')">&times;</span>
        <h2>Are you sure you want to delete this teacher?</h2>
        <form id="deleteForm" method="POST" action="delete_teacher.php">
            <input type="hidden" name="teacher_id" id="deleteTeacherId">
            <div class="modal-buttons">
                <button type="submit">Yes, Delete</button>
                <button type="button" onclick="closeModal('deleteModal')">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Open Edit Modal
    function openEditModal(teacherId) {
        document.getElementById('editTeacherId').value = teacherId;
        // Here, you can fetch the teacher's current data via Ajax if needed and populate the form
        document.getElementById('editModal').style.display = 'block';
    }

    // Open Delete Modal
    function openDeleteModal(teacherId) {
        document.getElementById('deleteTeacherId').value = teacherId;
        document.getElementById('deleteModal').style.display = 'block';
    }

    // Close Modal
    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    }
</script>

</body>
</html>
