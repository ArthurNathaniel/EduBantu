<?php
session_start();
require 'db.php'; // Include your database connection

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Fetch accountants from the database
$sql = "SELECT * FROM accountants";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Accountants</title>
    <?php include '../cdn.php'; ?>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/accountant.css">
</head>

<body>
    <?php include 'sidebar.php'; ?>
    <div class="view_accountant_all">
        <div class="forms_title">
            <h2>Accountants</h2>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Gender</th>
                    <th>Date of Birth</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['full_name']; ?></td>
                        <td><?php echo $row['gender']; ?></td>
                        <td><?php echo $row['date_of_birth']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td class='actions_btn'>
                            <button class="editBtn"
                                data-id="<?php echo $row['id']; ?>"
                                data-full_name="<?php echo $row['full_name']; ?>"
                                data-gender="<?php echo $row['gender']; ?>"
                                data-date_of_birth="<?php echo $row['date_of_birth']; ?>"
                                data-email="<?php echo $row['email']; ?>"><i class="fa-solid fa-user-pen"></i></button>
                            <form action="delete_accountant.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this accountant?');" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Edit Accountant Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="forms_title">
                <h2>Edit Accountant</h2>
            </div>
            <form id="editForm" action="edit_accountant.php" method="POST">
                <input type="hidden" name="id" id="editId">

                <div class="forms">
                    <label for="editFullName">Full Name:</label>
                    <input type="text" name="full_name" id="editFullName" required>
                </div>

                <div class="forms">
                    <label for="editGender">Gender:</label>
                    <select name="gender" id="editGender" required>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>

                <div class="forms">
                    <label for="editDateOfBirth">Date of Birth:</label>
                    <input type="date" name="date_of_birth" id="editDateOfBirth" required>
                </div>

                <div class="forms">
                    <label for="editEmail">Email:</label>
                    <input type="email" name="email" id="editEmail" required>
                </div>

                <div class="forms">
                    <label for="editPassword">Password:</label>
                    <input type="password" name="password" id="editPassword" placeholder="Leave blank to keep current password">
                </div>

                <div class="forms">
                    <button type="submit">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Get modal element
        var modal = document.getElementById("editModal");

        // Get close button
        var span = document.getElementsByClassName("close")[0];

        // Loop through all edit buttons to open the modal with corresponding data
        document.querySelectorAll(".editBtn").forEach(button => {
            button.onclick = function() {
                // Open the modal
                modal.style.display = "block";

                // Populate the modal with the clicked accountant's data
                document.getElementById("editId").value = this.getAttribute("data-id");
                document.getElementById("editFullName").value = this.getAttribute("data-full_name");
                document.getElementById("editGender").value = this.getAttribute("data-gender");
                document.getElementById("editDateOfBirth").value = this.getAttribute("data-date_of_birth");
                document.getElementById("editEmail").value = this.getAttribute("data-email");
            }
        });

        // Close the modal when the close button is clicked
        span.onclick = function() {
            modal.style.display = "none";
        }

        // Close the modal when clicking anywhere outside the modal
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>

</html>