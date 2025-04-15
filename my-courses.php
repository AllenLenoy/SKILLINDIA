<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Courses - Skill India</title>
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
                <h1>My Courses</h1>
                <div class="header-actions">
                    <a href="browse-courses.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Register New Course
                    </a>
                </div>
            </div>

            <div class="courses-section">
                <div id="enrolledCourses" class="courses-grid">
                    <!-- Enrolled courses will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script src="js/auth.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const user = Auth.getUser();
            loadEnrolledCourses(user.user_id);
        });

        function loadEnrolledCourses(userId) {

            fetch('api/enrolled-courses.php?user_id=' + userId)
                .then(response => response.json())
                .then(data => {
                    const coursesContainer = document.getElementById('enrolledCourses');
                    if (data.success && data.courses.length > 0) {
                        coursesContainer.innerHTML = '';
                        data.courses.forEach(course => {
                            coursesContainer.appendChild(createCourseCard(course));
                        });
                    } else {
                        coursesContainer.innerHTML = '<p class="no-courses">You are not enrolled in any courses yet.</p>';
                    }
                })
                .catch(error => {
                    console.error('Error loading courses:', error);
                    document.getElementById('enrolledCourses').innerHTML = 
                        '<p class="error-message">Failed to load your courses. Please try again later.</p>';
                });
        }

        function createCourseCard(course) {
            const courseImages = {
                'fullstack': 'images/img7.png',
                    'cloudcomputing' : 'images/cloudcomputing.jpg',
                    'datascience' : 'images/datascience.jpg',
                    'artificialintelligence' : 'images/ai.png',
                    'machinelearning' : 'images/machinelearning.png',
                    'cybersecurity' : 'images/cyber.jpg',
                // Add more as needed
            };
            const normalizedTitle = course.title.toLowerCase().replace(/\s+/g, '');
            const selectedImage = courseImages[normalizedTitle] || 'images/default-course.jpg';
            const card = document.createElement('div');
            card.className = 'course-card';
            card.innerHTML = `
                <img src="${selectedImage}" alt="${course.title}">
                <div class="course-info">
                    <h3 style="font-size: 18px; font-weight: bold; color: #fff;">${course.title}</h3>
                    <p>${course.description}</p>
                    <div class="course-progress">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: ${course.progress || 0}%"></div>
                        </div>
                        <span>${course.progress || 0}% Complete</span>
                    </div>
                    <div class="course-meta">
                        <span><i class="fas fa-clock"></i> ${course.duration || 'N/A'}</span>
                        <span><i class="fas fa-video"></i> ${course.video_count || 0} Videos</span>
                    </div>
                    <a href="course-details.php?id=${course.course_id}" class="btn btn-primary">Continue Learning</a>
                </div>
            `;
            return card;
        }
    </script>
</body>
</html>