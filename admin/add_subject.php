<?php
include 'db.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Add a new subject
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $subject_name = $_POST['subject_name'];

    // Check if the subject already exists
    $checkSubject = "SELECT * FROM subjects WHERE subject_name = '$subject_name'";
    $result = $conn->query($checkSubject);

    if ($result->num_rows > 0) {
        echo "<script>alert('Subject already exists. Please add a different subject.');</script>";
    } else {
        // Insert new subject
        $sql = "INSERT INTO subjects (subject_name) VALUES ('$subject_name')";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Subject added successfully!');</script>";
        } else {
            echo "<script>alert('Error: " . $conn->error . "');</script>";
        }
    }
}

// Delete subject
if (isset($_GET['delete_id'])) {
    $subject_id = $_GET['delete_id'];
    $sql = "DELETE FROM subjects WHERE id = $subject_id";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Subject deleted successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Subject</title>
    <?php include '../cdn.php' ?>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/add_subject.css">


</head>

<body>
<?php include 'sidebar.php' ?>
    <div class="add_subject_all">
        <div class="forms_title">
            <h2>Add New Subject</h2>
        </div>
        <form method="POST" action="add_subject.php">
            <div class="forms">
                <label>Subject Name:</label>
                <input type="text" name="subject_name" required>
            </div>
            <div class="forms">
                <button type="submit">Add Subject</button>
            </div>
        </form>

       <div class="forms_title">
       <h2>Existing Subjects</h2>
       </div>
        <table>
            <thead>
                <tr>
                    <th>Subject Name</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch all subjects
                $sql = "SELECT * FROM subjects";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['subject_name'] . "</td>";
                        echo "<td><button onclick=\"openModal('editModal" . $row['id'] . "')\"><i class='fa-regular fa-pen-to-square'></i></button></td>";
                        echo "<td><a href='add_subject.php?delete_id=" . $row['id'] . "' onclick=\"return confirm('Are you sure you want to delete this subject?');\"><i class='fa-solid fa-trash'></i></a></td>";
                        echo "</tr>";

                        // Modal for editing the subject
                        echo "
                <div id='editModal" . $row['id'] . "' class='modal'>
                    <div class='modal-content'>
                        <span class='close' onclick=\"closeModal('editModal" . $row['id'] . "')\">&times;</span>
                      <div class='forms_title'>
                        <h2>Edit Subject</h2>
                      </div>
                        <form method='POST' action='edit_subject.php'>
                           
                      <div class='forms'>
                       <input type='hidden' name='subject_id' value='" . $row['id'] . "'>
                            <label>Subject Name:</label>
                            <input type='text' name='subject_name' value='" . $row['subject_name'] . "' required></div>
                           <div class='forms'> 
                           <button type='submit'>Update Subject</button>
                           </div>
                        </form>
                    </div>
                </div>
                ";
                    }
                } else {
                    echo "<tr><td colspan='3'>No subjects available</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <p><a href="dashboard.php">Back to Dashboard</a></p>

        <script>
            // Function to open the modal
            function openModal(modalId) {
                document.getElementById(modalId).style.display = "block";
            }

            // Function to close the modal
            function closeModal(modalId) {
                document.getElementById(modalId).style.display = "none";
            }

            // Close the modal if the user clicks outside of it
            window.onclick = function(event) {
                var modals = document.getElementsByClassName('modal');
                for (var i = 0; i < modals.length; i++) {
                    if (event.target == modals[i]) {
                        modals[i].style.display = "none";
                    }
                }
            }
        </script>
</body>

</html>