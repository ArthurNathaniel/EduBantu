<?php
include 'db.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $class_name = $_POST['class_name'];

    // Check if the class already exists
    $checkClass = "SELECT * FROM classes WHERE class_name = '$class_name'";
    $result = $conn->query($checkClass);

    if ($result->num_rows > 0) {
        // Class already exists
        echo "<script>alert('Class already exists. Please add a different class.'); window.location.href = 'add_class.php';</script>";
    } else {
        // Insert new class
        $sql = "INSERT INTO classes (class_name) VALUES ('$class_name')";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Class added successfully!'); window.location.href = 'add_class.php';</script>";
        } else {
            echo "<script>alert('Error: " . $conn->error . "'); window.location.href = 'add_class.php';</script>";
        }
    }
}

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $sql = "DELETE FROM classes WHERE id = $delete_id";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Class deleted successfully!'); window.location.href = 'add_class.php';</script>";
    } else {
        echo "<script>alert('Error deleting class: " . $conn->error . "'); window.location.href = 'add_class.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Class</title>
    <?php include '../cdn.php' ?>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/add_class.css">
    <style>

    </style>
</head>

<body>
    <div class="add_class_all">
        <div class="forms_title">
            <h2>Add New Class</h2>
        </div>

        <form method="POST" action="add_class.php">
            <div class="forms">
                <label>Class Name:</label>
                <input type="text" placeholder="Enter the class name" name="class_name" required>
            </div>

            <div class="forms">
                <button type="submit">Add Class</button>
            </div>
        </form>

        <div class="forms_title">
            <h3>Available Classes:</h3>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Class Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch all classes
                $sql = "SELECT * FROM classes";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['class_name'] . "</td>";
                        echo "<td class='actions'>
                    <button onclick=\"openEditModal('" . $row['id'] . "', '" . $row['class_name'] . "')\"><i class='fa-regular fa-pen-to-square'></i></button>
                    <button onclick=\"confirmDelete('" . $row['id'] . "')\"><i class='fa-solid fa-trash'></i></button>
                  </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='2'>No classes found.</td></tr>";
                }
                ?>
            </tbody>
        </table>

    </div>
    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <div class="forms_title">
                <h3>Edit Class</h3>
            </div>

            <form method="POST" action="edit_class.php">
                <input type="hidden" id="edit_class_id" name="class_id">
                <div class="forms">
                    <label>Class Name:</label>
                    <input type="text" id="edit_class_name" name="class_name" required>
                </div>
                <div class="forms">
                    <button type="submit">Update Class</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Open Edit Modal
        function openEditModal(id, name) {
            document.getElementById('edit_class_id').value = id;
            document.getElementById('edit_class_name').value = name;
            document.getElementById('editModal').style.display = 'block';
        }

        // Close Modal
        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Confirm Delete
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this class?')) {
                window.location.href = 'add_class.php?delete_id=' + id;
            }
        }

        // Close modal when clicked outside
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>

    <p><a href="dashboard.php">Back to Dashboard</a></p>
</body>

</html>