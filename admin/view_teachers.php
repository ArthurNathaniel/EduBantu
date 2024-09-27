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
                    <th>Phone</th>
                    <th>Assign Class</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>

                        <td><?php echo $row['first_name']; ?></td>
                        <!-- <td><?php echo $row['first_name'] . ' ' . ($row['middle_name'] ? $row['middle_name'] . ' ' : '') . $row['last_name']; ?></td> -->
                        <td><?php echo $row['phone']; ?></td>
                        <td><?php echo htmlspecialchars($row['class_name'] ?? 'N/A'); ?></td> <!-- Handle case where class_name might be null -->

                        <td class="actions_btn">
                            <button class="view-btn" onclick="viewTeacher(<?php echo $row['id']; ?>)"><i class="fa-solid fa-eye"></i></button>
                            <button class="edit-btn" onclick="openEditModal(<?php echo $row['id']; ?>)"><i class="fa-solid fa-user-pen"></i></button>
                            <button class="delete-btn" onclick="openDeleteModal(<?php echo $row['id']; ?>)"><i class="fa-solid fa-trash"></i></button>
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
            <div class="forms_title">
                <h2>Edit Teacher Details</h2>
            </div>
            <form id="editForm">
                <input type="hidden" name="id" id="editTeacherId">

                <div class="forms">
                    <label>First Name:</label>
                    <input type="text" placeholder="Enter your first name" name="first_name" id="editFirstName" required>
                </div>

                <div class="forms">
                    <label>Middle Name:</label>
                    <input type="text" placeholder="Enter your middle name" name="middle_name" id="editMiddleName">
                </div>

                <div class="forms">
                    <label>Last Name:</label>
                    <input type="text" placeholder="Enter your last name" name="last_name" id="editLastName" required>
                </div>

                <div class="forms">
                    <label>Date of Birth:</label>
                    <input type="date" name="dob" id="editDob" required>
                </div>

                <div class="forms">
                    <label>Gender:</label>
                    <select name="gender" id="editGender" required>
                        <option value="" selected hidden>Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>

                <div class="forms">
                    <label>Level of Education:</label>
                    <select name="education_level" id="editEducationLevel" required>
                        <option value="" selected hidden>Select Level of Education</option>
                        <option value="SHS">SHS</option>
                        <option value="Diploma">Diploma</option>
                        <option value="HND">HND</option>
                        <option value="Degree">Degree</option>
                        <option value="Master">Master</option>
                        <option value="PhD">PhD</option>
                    </select>
                </div>

                <div class="forms">
                    <label>Email:</label>
                    <input type="email" placeholder="Enter your email address" name="email" id="editEmail" required>
                </div>

                <div class="forms">
                    <label>Phone Number:</label>
                    <input type="text" placeholder="Enter your phone number" name="phone" id="editPhone" required>
                </div>

                <div class="forms">
                    <label>House Number:</label>
                    <input type="text" placeholder="Enter your house number" name="house_number" id="editHouseNumber" required>
                </div>

                <div class="forms">
                    <label>Hometown:</label>
                    <input type="text" placeholder="Enter your hometown" name="hometown" id="editHometown" required>
                </div>

                <div class="forms">
                    <label>Emergency Contact Person Name:</label>
                    <input type="text" placeholder="Enter your emergency contact person name" name="emergency_contact_name" id="editEmergencyContactName" required>
                </div>

                <div class="forms">
                    <label>Emergency Contact Phone:</label>
                    <input type="number" min="0" placeholder="Enter your emergency contact phone number" name="emergency_contact_phone" id="editEmergencyContactPhone" required>
                </div>

                <div class="forms">
                    <label>Emergency Contact Relationship:</label>
                    <select name="emergency_contact_relationship" id="editEmergencyContactRelationship" required>
                        <option value="" selected hidden>Select Emergency Contact Relationship</option>
                        <option value="Parent">Parent</option>
                        <option value="Sister">Sister</option>
                        <option value="Brother">Brother</option>
                        <option value="Friend">Friend</option>
                        <option value="Family">Family</option>
                    </select>
                </div>

                <div class="forms">
                    <label>Assign Class:</label>
                    <select name="class_id" id="editClassId" required>
                        <option value="" selected hidden>Assign Class</option>
                        <option value="0">None</option> <!-- Add 'None' option -->
                        <?php
                        // Fetch classes from the database
                        $sql = "SELECT * FROM classes";
                        $result = $conn->query($sql);
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['id'] . "'>" . $row['class_name'] . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="forms">
                    <label>Password:</label>
                    <input type="password" placeholder="Enter your password" name="password" id="editPassword">
                </div>

                <div class="forms">
                    <button type="submit">Save Changes</button>
                </div>
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
            <div class="modal_delete">
                <button onclick="deleteTeacher()" class="delete">Yes, Delete</button>
                <button onclick="closeDeleteModal()">Cancel</button>
            </div>
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
                           <p><strong>Emergency Contact:</strong> ${data.emergency_contact_name} (${data.emergency_contact_phone}, ${data.emergency_contact_relationship})</p>
                           <p><strong>Assigned Class:</strong> ${data.class_name}</p>`; // Added class name here
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
                    document.getElementById('editDob').value = data.dob; // Assuming you have 'dob' in your database
                    document.getElementById('editGender').value = data.gender;
                    document.getElementById('editEducationLevel').value = data.education_level;
                    document.getElementById('editEmail').value = data.email;
                    document.getElementById('editPhone').value = data.phone;
                    document.getElementById('editHouseNumber').value = data.house_number;
                    document.getElementById('editHometown').value = data.hometown;
                    document.getElementById('editEmergencyContactName').value = data.emergency_contact_name;
                    document.getElementById('editEmergencyContactPhone').value = data.emergency_contact_phone;
                    document.getElementById('editEmergencyContactRelationship').value = data.emergency_contact_relationship;
                    document.getElementById('editClassId').value = data.class_id; // Assuming you have 'class_id' in your database
                    document.getElementById('editPassword').value = ''; // Clear password field
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
                    body: JSON.stringify({
                        id
                    })
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    location.reload();
                });
        }

        const form = document.getElementById('updateTeacherForm'); // Make sure this is the ID of your form
        form.addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent the default form submission

            const formData = new FormData(form);

            fetch('edit_teacher.php', { // Adjust the URL if needed
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message); // Alert success message
                        location.reload(); // Reload the page on success
                    } else {
                        alert(data.message); // Alert the error message
                    }
                })
                .catch(error => {
                    console.error('Error:', error); // Log any errors to the console
                    alert('An unexpected error occurred.'); // Alert a general error message
                });
        });
    </script>
</body>

</html>