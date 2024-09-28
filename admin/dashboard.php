<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    // If not logged in, redirect to login page
    header("Location: login.php");
    exit();
}

include 'db.php'; // Ensure you include your database connection

// Fetch total teachers count
$total_teachers_result = $conn->query("SELECT COUNT(*) as total FROM teachers");
$total_teachers = $total_teachers_result->fetch_assoc()['total'];

// Fetch gender distribution
$gender_distribution_result = $conn->query("SELECT gender, COUNT(*) as count FROM teachers GROUP BY gender");
$gender_distribution = [];
while ($row = $gender_distribution_result->fetch_assoc()) {
    $gender_distribution[$row['gender']] = $row['count'];
}

// Fetch education level distribution
$education_distribution_result = $conn->query("SELECT education_level, COUNT(*) as count FROM teachers GROUP BY education_level");
$education_distribution = [];
while ($row = $education_distribution_result->fetch_assoc()) {
    $education_distribution[$row['education_level']] = $row['count'];
}

// Prepare data for the chart
$genders = array_keys($gender_distribution);
$gender_counts = array_values($gender_distribution);
$education_levels = array_keys($education_distribution);
$education_counts = array_values($education_distribution);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <?php include '../cdn.php' ?>
    <link rel="stylesheet" href="../css/base.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<?php include 'sidebar.php' ?>
    <h2>Welcome to the Admin Dashboard</h2>
    
    <p>Hello, <?php echo $_SESSION['admin']; ?>! You are logged in as an admin.</p>
    <p><a href="logout.php">Logout</a></p>

    <div>
        <h3>Admin Actions</h3>
        <ul>
            <li><a href="add_student.php">Add Student</a></li>
            <li><a href="view_reports.php">View Student Reports</a></li>
            <!-- Add more admin functionality links as needed -->
        </ul>
    </div>

    <div>
        <h3>Statistics</h3>
        <p>Total Teachers: <?php echo $total_teachers; ?></p>
    </div>

    <div>
        <h3>Teacher Gender Distribution</h3>
        <canvas id="genderChart" width="400" height="200"></canvas>
    </div>

    <div>
        <h3>Teacher Education Level Distribution</h3>
        <canvas id="educationChart" width="400" height="200"></canvas>
    </div>

    <script>
        // Gender Distribution Chart
        const genderCtx = document.getElementById('genderChart').getContext('2d');
        const genderChart = new Chart(genderCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($genders); ?>,
                datasets: [{
                    label: 'Number of Teachers',
                    data: <?php echo json_encode($gender_counts); ?>,
                    backgroundColor: ['#FF6384', '#36A2EB'],
                    borderColor: ['#FF6384', '#36A2EB'],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Education Level Distribution Chart
        const educationCtx = document.getElementById('educationChart').getContext('2d');
        const educationChart = new Chart(educationCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($education_levels); ?>,
                datasets: [{
                    label: 'Number of Teachers',
                    data: <?php echo json_encode($education_counts); ?>,
                    backgroundColor: '#FFCE56',
                    borderColor: '#FFCE56',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
