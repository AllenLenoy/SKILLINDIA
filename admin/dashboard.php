<?php
require_once '../config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Skill India</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body style="margin: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <div class="dashboard-container" style="display: flex; min-height: 100vh;">
        <?php include './sidebar.php'; ?>

        <main class="main-content" style="flex: 1; padding: 30px;">
            <div class="dashboard-header" style="margin-bottom: 20px;">
                <h1 id="welcomeMessage" style="margin: 0; font-size: 28px; color: #333;">Welcome, Admin</h1>
                <p style="margin: 5px 0; color: #777;">Admin Dashboard Overview</p>
            </div>

            <div class="dashboard-stats" style="display: flex; gap: 20px; margin-bottom: 30px;">
                <div class="stat-card" style="flex: 1; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-align: center;">
                    <i class="fas fa-users" style="font-size: 24px; color: #007bff;"></i>
                    <h3 id="totalUsers" style="margin: 10px 0;">0</h3>
                    <p style="margin: 0; color: #555;">Total Users</p>
                </div>

                <div class="stat-card" style="flex: 1; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-align: center;">
                    <i class="fas fa-book" style="font-size: 24px; color: #28a745;"></i>
                    <h3 id="totalCourses" style="margin: 10px 0;">0</h3>
                    <p style="margin: 0; color: #555;">Total Courses</p>
                </div>

                <div class="stat-card" style="flex: 1; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-align: center;">
                    <i class="fas fa-graduation-cap" style="font-size: 24px; color: #ffc107;"></i>
                    <h3 id="totalEnrollments" style="margin: 10px 0;">0</h3>
                    <p style="margin: 0; color: #555;">Total Enrollments</p>
                </div>
            </div>

            <div class="dashboard-sections">
                <div class="section" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <h2 style="margin-bottom: 20px; color: #333;">Recent Users</h2>
                    <div id="recentUsers">
                        <p>Loading users...</p>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../js/auth.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (!Auth.checkAuth()) {
                window.location.href = '../login.php';
                return;
            }

            const user = Auth.getUser();

            if (user.role !== 'admin') {
                window.location.href = '../dashboard.php';
                return;
            }

            document.getElementById('welcomeMessage').textContent = `Welcome, ${user.name}`;

            loadAdminDashboardData();
        });

        function loadAdminDashboardData() {
            const user = Auth.getUser();

            fetch('api/admin-dashboard.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    user_id: user.user_id,
                    role: user.role
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    document.getElementById('totalUsers').textContent = data.stats.users;
                    document.getElementById('totalCourses').textContent = data.stats.courses;
                    document.getElementById('totalEnrollments').textContent = data.stats.enrollments;

                    const recentUsersDiv = document.getElementById('recentUsers');
                    if (data.recentUsers.length === 0) {
                        recentUsersDiv.innerHTML = '<p>No users found.</p>';
                    } else {
                        recentUsersDiv.innerHTML = createUsersTable(data.recentUsers);
                    }
                } else {
                    console.error('Failed to load admin dashboard data:', data.message);
                    if (data.message === 'Unauthorized access') {
                        window.location.href = '../login.php';
                    }
                }
            })
            .catch(error => {
                console.error('Error loading admin dashboard data:', error);
            });
        }

        function createUsersTable(users) {
            return `
            <div style="overflow-x:auto;">
                <table style="width: 100%; border-collapse: collapse; background-color: #ffffff;">
                <thead>
                    <tr style="background-color: #007bff; color: white; font-size: 1.1em;">
                    <th style="padding: 12px; text-align: left;">Name</th>
                    <th style="padding: 12px; text-align: left;">Email</th>
                    <th style="padding: 12px; text-align: left;">Role</th>
                    <th style="padding: 12px; text-align: left;">Joined</th>
                    </tr>
                </thead>
                <tbody>
                    ${users.map(user => `
                    <tr style="border-bottom: 1px solid #ddd; font-size: 1em; color: #333;">
                        <td style="padding: 10px;">${user.name}</td>
                        <td style="padding: 10px;">${user.email}</td>
                        <td style="padding: 10px;">${user.role}</td>
                        <td style="padding: 10px;">${new Date(user.created_at).toLocaleDateString()}</td>
                    </tr>`).join('')}
                </tbody>
                </table>
            </div>
            `;
        }
    </script>
</body>
</html>
