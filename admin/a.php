<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

require 'db.php'; // Include your database connection

// Fetch total number of students
$total_students_sql = "SELECT COUNT(*) as total FROM students";
$total_students_result = $conn->query($total_students_sql);
$total_students = $total_students_result->fetch_assoc()['total'];

// Fetch gender distribution
$gender_distribution_sql = "SELECT gender, COUNT(*) as count FROM students GROUP BY gender";
$gender_distribution_result = $conn->query($gender_distribution_sql);
$gender_distribution = [];
while ($row = $gender_distribution_result->fetch_assoc()) {
    $gender_distribution[$row['gender']] = $row['count'];
}

// Fetch total number of students in each class
$class_distribution_sql = "SELECT class_name, COUNT(*) as count FROM students GROUP BY class_name";
$class_distribution_result = $conn->query($class_distribution_sql);
$class_distribution = [];
while ($row = $class_distribution_result->fetch_assoc()) {
    $class_distribution[$row['class_name']] = $row['count'];
}

// Fetch gender distribution in each class
$gender_class_distribution_sql = "SELECT class_name, gender, COUNT(*) as count FROM students GROUP BY class_name, gender";
$gender_class_distribution_result = $conn->query($gender_class_distribution_sql);
$gender_in_class = [];
while ($row = $gender_class_distribution_result->fetch_assoc()) {
    $gender_in_class[$row['class_name']][$row['gender']] = $row['count'];
}

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
        </ul>
    </div>

    <div>
        <h3>Dashboard Statistics</h3>
        <p>Total Students: <?php echo $total_students; ?></p>

        <canvas id="genderChart"></canvas>
        <table border="1">
            <caption>Gender Distribution</caption>
            <thead>
                <tr>
                    <th>Gender</th>
                    <th>Number of Students</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($gender_distribution as $gender => $count): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($gender); ?></td>
                        <td><?php echo htmlspecialchars($count); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <canvas id="classChart"></canvas>
        <table border="1">
            <caption>Total Students in Each Class</caption>
            <thead>
                <tr>
                    <th>Class Name</th>
                    <th>Number of Students</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($class_distribution as $class_name => $count): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($class_name); ?></td>
                        <td><?php echo htmlspecialchars($count); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <canvas id="genderInClassChart"></canvas>
        <table border="1">
            <caption>Gender Distribution in Each Class</caption>
            <thead>
                <tr>
                    <th>Class Name</th>
                    <th>Male Students</th>
                    <th>Female Students</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($gender_in_class as $class_name => $genders): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($class_name); ?></td>
                        <td><?php echo htmlspecialchars($genders['Male'] ?? 0); ?></td>
                        <td><?php echo htmlspecialchars($genders['Female'] ?? 0); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Gender Distribution Chart
        const genderDistribution = <?php echo json_encode($gender_distribution); ?>;
        const genderChartCtx = document.getElementById('genderChart').getContext('2d');
        const genderChart = new Chart(genderChartCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(genderDistribution),
                datasets: [{
                    label: 'Number of Students',
                    data: Object.values(genderDistribution),
                    backgroundColor: ['#42a5f5', '#66bb6a'],
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                    },
                    title: {
                        display: true,
                        text: 'Gender Distribution of Students',
                        font: {
                            size: 20,
                        },
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.raw;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Total Students in Each Class Chart
        const classDistribution = <?php echo json_encode($class_distribution); ?>;
        const classChartCtx = document.getElementById('classChart').getContext('2d');
        const classChart = new Chart(classChartCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(classDistribution),
                datasets: [{
                    label: 'Number of Students',
                    data: Object.values(classDistribution),
                    backgroundColor: ['#ffa726', '#fb8c00', '#ffb300', '#ffd740', '#ffeb3b'],
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                    },
                    title: {
                        display: true,
                        text: 'Total Students in Each Class',
                        font: {
                            size: 20,
                        },
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.raw;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Gender Distribution in Each Class Chart
        const genderInClassDistribution = <?php echo json_encode($gender_in_class); ?>;
        const classLabels = Object.keys(genderInClassDistribution);
        const maleCounts = classLabels.map(className => genderInClassDistribution[className]['Male'] || 0);
        const femaleCounts = classLabels.map(className => genderInClassDistribution[className]['Female'] || 0);

        const genderInClassChartCtx = document.getElementById('genderInClassChart').getContext('2d');
        const genderInClassChart = new Chart(genderInClassChartCtx, {
            type: 'bar',
            data: {
                labels: classLabels,
                datasets: [
                    {
                        label: 'Male Students',
                        data: maleCounts,
                        backgroundColor: '#42a5f5',
                    },
                    {
                        label: 'Female Students',
                        data: femaleCounts,
                        backgroundColor: '#66bb6a',
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                    },
                    title: {
                        display: true,
                        text: 'Gender Distribution in Each Class',
                        font: {
                            size: 20,
                        },
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.raw;
                            }
                        }
                    }
                },
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
