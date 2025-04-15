<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Courses - Skill India</title>
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
                <h1>Available Courses</h1>
            </div>

            <div class="courses-section">
                <div id="availableCourses" class="courses-grid">
                    <!-- Available courses will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script src="js/auth.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const user = Auth.getUser();
            loadAvailableCourses(user.user_id);
        });

        function loadAvailableCourses(userId) {
            fetch('api/available-courses.php?user_id=' + userId)
                .then(response => response.json())
                .then(data => {
                    const coursesContainer = document.getElementById('availableCourses');
                    if (data.success && data.courses.length > 0) {
                        coursesContainer.innerHTML = '';
                        data.courses.forEach(course => {
                            coursesContainer.appendChild(createCourseCard(course));
                        });
                    } else {
                        coursesContainer.innerHTML = '<p class="no-courses">No courses available at the moment.</p>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('availableCourses').innerHTML = 
                        '<p class="error-message">Failed to load courses. Please try again later.</p>';
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
            const card = document.createElement('div');
            const normalizedTitle = course.title.toLowerCase().replace(/\s+/g, '');
            const selectedImage = courseImages[normalizedTitle] || 'images/default-course.jpg';
            card.className = 'course-card';
            card.innerHTML = `
                <img src="${selectedImage}" alt="${course.title}">
                <div class="course-info">
                    <h3 style="font-size: 18px; font-weight: bold; color: #fff;">${course.title}</h3>
                    <p>${course.description}</p>
                    <div class="course-meta">
                        <span><i class="fas fa-clock"></i> ${course.duration || 'N/A'}</span>
                        <span><i class="fas fa-video"></i> ${course.video_count || 0} Videos</span>
                    </div>
                    <button onclick="showEnrollmentPopup(${JSON.stringify(course).replace(/"/g, '&quot;')})" class="btn btn-primary">Enroll Now</button>
                </div>
            `;
            return card;
        }

        function showEnrollmentPopup(course) {

            // Define your image dictionary
            const courseImages = {
                'fullstack': 'images/img7.png',
                    'cloudcomputing' : 'images/cloudcomputing.jpg',
                    'datascience' : 'images/datascience.jpg',
                    'artificialintelligence' : 'images/ai.png',
                    'machinelearning' : 'images/machinelearning.png',
                    'cybersecurity' : 'images/cyber.jpg',
                // Add more as needed
            };
            const popup = document.createElement('div');

            // Image logic
            const normalizedTitle = course.title.toLowerCase().replace(/\s+/g, '');
            const selectedImage = courseImages[normalizedTitle] || 'images/default-course.jpg';

            popup.className = 'enrollment-popup';
            popup.innerHTML = `
                <div style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: #fff; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.2); width: 90%; max-width: 500px; z-index: 1000; font-family: 'Segoe UI', sans-serif;">
                    <div style="padding: 16px 24px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                        <h2 style="margin: 0; font-size: 20px;">Course Enrollment</h2>
                        <button onclick="closePopup()" style="background: none; border: none; font-size: 18px; cursor: pointer;"><i class="fas fa-times"></i></button>
                    </div>
                    <div style="padding: 20px; text-align: center;">
                        <img src="${selectedImage}" alt="${course.title}" style="width: 100%; max-height: 180px; object-fit: cover; border-radius: 8px; margin-bottom: 15px;">
                        <h3 style="margin: 10px 0; font-size: 18px;">${course.title}</h3>
                        <p style="color: #555; font-size: 14px;">${course.description}</p>
                        <div style="margin-top: 15px; color: #333;">
                            <p><i class="fas fa-video"></i> Total Videos: ${course.video_count}</p>
                            <p><i class="fas fa-clock"></i> Duration: ${course.duration || 'N/A'}</p>
                        </div>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 16px 24px; border-top: 1px solid #eee;">
                        <button onclick="confirmEnrollment(${course.course_id}, ${course.video_count})" style="background: #007bff; border: none; padding: 10px 16px; color: #fff; border-radius: 6px; cursor: pointer;">Confirm Enrollment</button>
                        <button onclick="closePopup()" style="background: #ddd; border: none; padding: 10px 16px; color: #333; border-radius: 6px; cursor: pointer;">Cancel</button>
                    </div>
                </div>
            `;
            document.body.appendChild(popup);


        }

        function closePopup() {
            const popup = document.querySelector('.enrollment-popup');
            if (popup) {
                popup.remove();
            }
        }

        function confirmEnrollment(courseId, video_count) {
            const user = Auth.getUser();
            fetch('api/enroll-course.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    user_id: user.user_id,
                    course_id: courseId,
                    video_count: video_count
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Successfully enrolled in the course!');
                    window.location.href = 'my-courses.php';
                } else {
                    alert(data.message || 'Failed to enroll in the course.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while enrolling in the course.');
            });
        }
    </script>
</body>
</html>