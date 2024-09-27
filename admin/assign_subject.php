<?php
include 'db.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Handle subject assignment to a class
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ensure both class_id and subject_id are set before proceeding
    if (isset($_POST['class_id']) && isset($_POST['subject_id'])) {
        $class_id = $_POST['class_id'];
        $subject_id = $_POST['subject_id'];

        // Check if the subject is already assigned to the class
        $checkAssignment = "SELECT * FROM class_subjects WHERE class_id = $class_id AND subject_id = $subject_id";
        $result = $conn->query($checkAssignment);

        if ($result->num_rows > 0) {
            echo "<script>alert('This subject is already assigned to the selected class.'); window.location.href = 'assign_subject.php';</script>";
        } else {
            // Insert subject-class mapping
            $sql = "INSERT INTO class_subjects (class_id, subject_id) VALUES ($class_id, $subject_id)";
            if ($conn->query($sql) === TRUE) {
                echo "<script>alert('Subject assigned to class successfully!'); window.location.href = 'assign_subject.php';</script>";
            } else {
                echo "<script>alert('Error: " . $conn->error . "'); window.location.href = 'assign_subject.php';</script>";
            }
        }
    }
}

// Handle subject removal from a class
if (isset($_POST['remove_subject'])) {
    if (isset($_POST['class_id'])) {
        $class_id = $_POST['class_id'];
        $subject_id = $_POST['remove_subject']; // This is the ID of the subject to remove

        // Delete the subject-class mapping
        $removeSQL = "DELETE FROM class_subjects WHERE class_id = $class_id AND subject_id = $subject_id";
        if ($conn->query($removeSQL) === TRUE) {
            echo "<script>alert('Subject removed from class successfully!'); window.location.href = 'assign_subject.php';</script>";
        } else {
            echo "<script>alert('Error: " . $conn->error . "'); window.location.href = 'assign_subject.php';</script>";
        }
    }
}

// Fetch assigned subjects for a selected class using AJAX
if (isset($_GET['class_id'])) {
    $class_id = $_GET['class_id'];
    $assignedSubjectsQuery = "SELECT subject_id FROM class_subjects WHERE class_id = $class_id";
    $assignedSubjectsResult = $conn->query($assignedSubjectsQuery);
    
    $assignedSubjects = [];
    while ($row = $assignedSubjectsResult->fetch_assoc()) {
        $assignedSubjects[] = $row['subject_id'];
    }
    echo json_encode($assignedSubjects);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Subjects to Classes</title>
    <?php include '../cdn.php' ?>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/assign_class.css">
    <script>
        function fetchAssignedSubjects() {
            var classId = document.querySelector('select[name="class_id"]').value;
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "assign_subject.php?class_id=" + classId, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var assignedSubjects = JSON.parse(xhr.responseText);
                    var subjectSelect = document.querySelector('select[name="subject_id"]');
                    for (var i = 0; i < subjectSelect.options.length; i++) {
                        var option = subjectSelect.options[i];
                        if (assignedSubjects.includes(parseInt(option.value))) {
                            option.style.display = 'none'; // Hide assigned subjects
                        } else {
                            option.style.display = 'block'; // Show unassigned subjects
                        }
                    }
                }
            };
            xhr.send();
        }

        function showRemoveModal(classId, subjectId) {
            document.getElementById('removeClassId').value = classId;
            document.getElementById('removeSubjectId').value = subjectId;
            document.getElementById('removeModal').style.display = 'block';
        }

        function closeRemoveModal() {
            document.getElementById('removeModal').style.display = 'none';
        }
    </script>
   
</head>
<body>
<?php include 'sidebar.php' ?>
    <div class="assign_class_all">
  <div class="forms_title">
  <h2>Assign Subjects to Classes</h2>
  </div>
    <form method="POST" action="assign_subject.php">
       <div class="forms">
       <label>Select Class:</label>
        <select name="class_id" onchange="fetchAssignedSubjects()" required>
            <?php
            // Fetch all classes
            $sql = "SELECT * FROM classes";
            $result = $conn->query($sql);
            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . $row['id'] . "'>" . $row['class_name'] . "</option>";
            }
            ?>
        </select>
       </div>

       <div class="forms">
       <label>Select Subject:</label>
        <select name="subject_id" required>
            <?php
            // Fetch all subjects
            $sql = "SELECT * FROM subjects";
            $result = $conn->query($sql);
            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . $row['id'] . "'>" . $row['subject_name'] . "</option>";
            }
            ?>
        </select>
       </div>

       <div class="forms">
       <button type="submit">Assign Subject</button>
       </div>
    </form>

<div class="forms_title">
<h2>Assigned Subjects for Each Class</h2>
</div>
    <table>
        <thead>
            <tr>
                <th>Class Name</th>
                <th>Subjects</th>
                <th>Remove</th>
            </tr>
        </thead>
        <tbody>
        <?php
        // Fetch all classes and their subjects
        $sql = "SELECT classes.class_name, class_subjects.subject_id, subjects.subject_name, classes.id AS class_id
                FROM class_subjects
                JOIN classes ON class_subjects.class_id = classes.id
                JOIN subjects ON class_subjects.subject_id = subjects.id
                ORDER BY classes.class_name";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['class_name'] . "</td>";
                echo "<td>" . $row['subject_name'] . "</td>";
                echo "<td><button onclick='showRemoveModal(" . $row['class_id'] . ", " . $row['subject_id'] . ")'><i class='fa-solid fa-delete-left'></i></button></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No subjects assigned to any class.</td></tr>";
        }
        ?>
        </tbody>
    </table>
    </div>
    <!-- Remove Subject Modal -->
    <div id="removeModal" class="modal">
        <div class="modal-content">
            <span onclick="closeRemoveModal()" style="float:right;cursor:pointer;">&times;</span>
            <div class="forms">
            <h3>Remove Subject</h3>
         <p>Are you sure you want to remove this subject from the class?</p>
         </div>
            <form method="POST" action="assign_subject.php">
               <input type="hidden" name="class_id" id="removeClassId">
               <input type="hidden" name="remove_subject" id="removeSubjectId">
             <div class="remove_btns">
             <div class="forms red">
              <button type="submit">Yes, Remove</button>
              </div>
             <div class="forms">
             <button type="button" onclick="closeRemoveModal()">Cancel</button>
             </div>
             </div>
            </form>
        </div>
    </div>

</body>
</html>
