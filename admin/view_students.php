<?php
session_start();
require 'db.php'; // Include database connection

// Initialize class filter variable
$class_filter = isset($_POST['class_filter']) ? $_POST['class_filter'] : '';

// Fetch classes for the dropdown
$class_sql = "SELECT id, class_name FROM classes";
$class_result = $conn->query($class_sql);

// Fetch students from the database with filtering by class
$sql = "SELECT students.id, students.name, students.gender, students.dob, students.emergency_name, 
                students.emergency_phone, students.emergency_relation, students.class_name, 
                students.index_number, students.profile_image, classes.class_name AS class_name
        FROM students
        JOIN classes ON students.class_name = classes.id";

// Add filtering logic if class is selected
if ($class_filter) {
    $sql .= " WHERE students.class_name = ?";
}

$stmt = $conn->prepare($sql);
if ($class_filter) {
    $stmt->bind_param("i", $class_filter);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registered Students</title>
    <?php include '../cdn.php'; ?>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/view_students.css">
</head>

<body>
    <?php include 'sidebar.php'; ?>
    <div class="view_students_all">

        <div class="forms_title">
            <h1>Registered Students</h1>
        </div>
        <!-- Filter Form -->
        <form method="POST" action="">
            <div class="forms">
                <label for="class_filter">Filter by Class:</label>
                <select name="class_filter" id="class_filter">
                    <option value="">All Classes</option> <!-- Option for all classes -->
                    <?php while ($class_row = $class_result->fetch_assoc()): ?>
                        <option value="<?php echo $class_row['id']; ?>" <?php echo ($class_filter == $class_row['id']) ? 'selected' : ''; ?>>
                            <?php echo $class_row['class_name']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="forms">
                <button type="submit">Filter</button>
            </div>
        </form>
    
        <div class="forms">
            <label>Search by students name:</label>
            <input type="text" id="searchInput" placeholder="Search for students by name..." onkeyup="searchStudents()">
        </div>


        <table id="studentsTable">
    <thead>
        <tr>
            <th>Profile Image</th>
            <th>Name</th>
            <th>Class</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0): ?> <!-- Check if there are students -->
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr class="studentRow">
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
        <?php else: ?> <!-- Message when no students are found -->
            <tr>
                <td colspan="4">No students found.</td>
            </tr>
        <?php endif; ?>
        <tr id="noResultsRow" style="display: none;">
            <td colspan="4">No results found for your search.</td>
        </tr>
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

        function searchStudents() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase();
    const table = document.getElementById('studentsTable');
    const tr = table.getElementsByTagName('tr');
    let hasResults = false;

    // Loop through all table rows (except the first, which contains table headers)
    for (let i = 1; i < tr.length - 1; i++) { // -1 to skip the no results row
        const tdName = tr[i].getElementsByTagName('td')[1]; // Get the name cell
        if (tdName) {
            const txtValue = tdName.textContent || tdName.innerText;
            // Display rows that match the search input
            if (txtValue.toLowerCase().indexOf(filter) > -1) {
                tr[i].style.display = '';
                hasResults = true; // Match found
            } else {
                tr[i].style.display = 'none';
            }
        }
    }

    // Show or hide the "No results found" row based on matches
    const noResultsRow = document.getElementById('noResultsRow');
    noResultsRow.style.display = hasResults ? 'none' : '';
}

    </script>

</body>

</html>