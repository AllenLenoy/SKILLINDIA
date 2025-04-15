<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Skill India</title>
    <!-- Fix CSS paths by removing leading slash -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/student_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <?php include 'sidebar.php'; ?>
        
        <div class="dashboard-content">
            <div class="dashboard-header">
                <h1>Dashboard</h1>
            </div>

            <div class="dashboard-stats">
                <div class="stat-card">
                    <i class="fas fa-book"></i>
                    <h3 id="courseCount">0</h3>
                    <p id="courseLabel">Available Courses</p>
                </div>
                
                <div class="stat-card">
                    <i class="fas fa-graduation-cap"></i>
                    <h3 id="studentCount">0</h3>
                    <p>My Courses</p>
                </div>
            </div>

            <div class="dashboard-courses">
                <h2>My Enrolled Courses</h2>
                <div id="courseList" class="courses-grid">
                    <!-- Courses will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Fix JavaScript path by removing leading slash -->
    <script src="js/auth.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const user = Auth.getUser();
            loadDashboardData(user.user_id, user.role);
        });

        function loadDashboardData(userId, role) {
            fetch(`api/dashboard-stats.php?user_id=${userId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('courseCount').textContent = data.stats.total_available;
                        document.getElementById('studentCount').textContent = data.stats.total_enrolled;
                    }
                })
                .catch(error => {
                    console.error('Error loading dashboard stats:', error);
                });
        }
    </script>
</body>
</html>