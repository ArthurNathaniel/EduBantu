<?php
include 'db.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Fetch all teachers from the database
$sql = "SELECT * FROM teachers";
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
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
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
    </style>
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
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['first_name'] . ' ' . ($row['middle_name'] ? $row['middle_name'] . ' ' : '') . $row['last_name']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['phone']; ?></td>
                    <td>
                        <button class="view-btn" onclick="viewTeacher(<?php echo $row['id']; ?>)">View</button>
                        <button class="edit-btn" onclick="openEditModal(<?php echo $row['id']; ?>)">Edit</button>
                        <button class="delete-btn" onclick="openDeleteModal(<?php echo $row['id']; ?>)">Delete</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- View Modal -->
    <div id="viewModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeViewModal()">&times;</span>
            <h2>Teacher Details</h2>
            <div id="teacherDetails"></div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2>Edit Teacher</h2>
            <form id="editForm">
                <input type="hidden" name="id" id="editTeacherId">
                <label>First Name:</label>
                <input type="text" name="first_name" id="editFirstName" required>
                <label>Middle Name:</label>
                <input type="text" name="middle_name" id="editMiddleName">
                <label>Last Name:</label>
                <input type="text" name="last_name" id="editLastName" required>
                <label>Email:</label>
                <input type="email" name="email" id="editEmail" required>
                <label>Phone:</label>
                <input type="text" name="phone" id="editPhone" required>
                <button type="submit">Save Changes</button>
            </form>
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeDeleteModal()">&times;</span>
            <h2>Delete Teacher</h2>
            <p>Are you sure you want to delete this teacher?</p>
            <input type="hidden" id="deleteTeacherId">
            <button onclick="deleteTeacher()">Yes, Delete</button>
            <button onclick="closeDeleteModal()">Cancel</button>
        </div>
    </div>

    <script>
        function viewTeacher(id) {
            fetch(`get_teacher.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    let details = `<p><strong>Name:</strong> ${data.first_name} ${data.middle_name ? data.middle_name : ''} ${data.last_name}</p>
                                   <p><strong>Email:</strong> ${data.email}</p>
                                   <p><strong>Phone:</strong> ${data.phone}</p>
                                   <p><strong>Date of Birth:</strong> ${data.dob}</p>
                                   <p><strong>Gender:</strong> ${data.gender}</p>
                                   <p><strong>Education Level:</strong> ${data.education_level}</p>
                                   <p><strong>House Number:</strong> ${data.house_number}</p>
                                   <p><strong>Hometown:</strong> ${data.hometown}</p>
                                   <p><strong>Emergency Contact:</strong> ${data.emergency_contact_name} (${data.emergency_contact_phone}, ${data.emergency_contact_relationship})</p>`;
                    document.getElementById('teacherDetails').innerHTML = details;
                    document.getElementById('viewModal').style.display = "block";
                });
        }

        function closeViewModal() {
            document.getElementById('viewModal').style.display = "none";
        }

        function openEditModal(id) {
            fetch(`get_teacher.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('editTeacherId').value = data.id;
                    document.getElementById('editFirstName').value = data.first_name;
                    document.getElementById('editMiddleName').value = data.middle_name;
                    document.getElementById('editLastName').value = data.last_name;
                    document.getElementById('editEmail').value = data.email;
                    document.getElementById('editPhone').value = data.phone;
                    document.getElementById('editModal').style.display = "block";
                });
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = "none";
        }

        document.getElementById('editForm').onsubmit = function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('edit_teacher.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                location.reload();
            });
        }

        function openDeleteModal(id) {
            document.getElementById('deleteTeacherId').value = id;
            document.getElementById('deleteModal').style.display = "block";
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = "none";
        }

        function deleteTeacher() {
            const id = document.getElementById('deleteTeacherId').value;
            fetch('delete_teacher.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id })
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                location.reload();
            });
        }
    </script>
</body>
</html>
