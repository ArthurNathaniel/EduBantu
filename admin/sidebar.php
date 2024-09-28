<?php
$current_page = basename($_SERVER['PHP_SELF']); // Get the current page name
?>

<div class="navbar_all">
    <button id="toggleButton">
        <i class="fa-solid fa-bars-staggered"></i>
    </button>
    <div class="logout">
            <a href="logout.php" class="<?= $current_page === 'logout.php' ? 'active' : '' ?>">Logout</a>
        </div>
    <div class="mobile">
        <div class="logo"></div>
        <a href="dashboard.php" class="<?= $current_page === 'dashboard.php' ? 'active' : '' ?>">Dashboard</a>
        <a href="add_class.php" class="<?= $current_page === 'add_class.php' ? 'active' : '' ?>">Add Class</a>
        <a href="add_subject.php" class="<?= $current_page === 'add_subject.php' ? 'active' : '' ?>">Add Subject</a>
        <a href="assign_subject.php" class="<?= $current_page === 'assign_subject.php' ? 'active' : '' ?>">Assign Subject</a>
        <a href="register_teacher.php" class="<?= $current_page === 'register_teacher.php' ? 'active' : '' ?>">Register Teacher</a>
        <a href="view_teachers.php" class="<?= $current_page === 'view_teachers.php' ? 'active' : '' ?>">View Teachers</a>
        <a href="register_student.php" class="<?= $current_page === 'register_student.php' ? 'active' : '' ?>">Register Student</a>


        
        
    </div>
</div>


<script>
    // Get the button and sidebar elements
    var toggleButton = document.getElementById("toggleButton");
    var sidebar = document.querySelector(".mobile");
    var icon = toggleButton.querySelector("i");

    // Add click event listener to the button
    toggleButton.addEventListener("click", function() {
        // Toggle the visibility of the sidebar
        if (sidebar.style.display === "none" || sidebar.style.display === "") {
            sidebar.style.display = "flex";
            sidebar.style.flexDirection = "column";
            icon.classList.remove("fa-bars-staggered");
            icon.classList.add("fa-xmark");
        } else {
            sidebar.style.display = "none";
            icon.classList.remove("fa-xmark");
            icon.classList.add("fa-bars-staggered");
        }
    });
</script>