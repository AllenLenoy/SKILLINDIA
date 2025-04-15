<?php
require_once 'config.php';
require_once 'vendor/autoload.php';

$course_id = $_GET['id'] ?? null;
if (!$course_id) {
    header('Location: my-courses.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Details - Skill India</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/student_dashboard.css">
    <link rel="stylesheet" href="css/course-details.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- <link rel="stylesheet" href="/assets/css/admin/course-details.css"> -->
    <!-- <script src="/assets/js/admin/course-details.js"></script> -->
</head>
<body>
    <script src="js/auth.js"></script>


    <div class="dashboard-container">
        <?php include 'sidebar.php'; ?>
        
        <div class="dashboard-content">
            <div id="courseHeader" class="course-header">
                <!-- Course details will be loaded here -->
            </div>

            <div class="video-list-container">
                <h2>Course Videos</h2>
                <div id="videoList" class="video-list">
                    <!-- Videos will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script>
        // Move all JavaScript code inside this script tag
        document.addEventListener('DOMContentLoaded', async function() {
            try {
                console.log("DOM Content Loaded");
                const courseId = <?php echo $course_id; ?>;
                if (!Auth.checkAuth()) {
                    window.location.href = './login.php';
                    return;
                }

                const user = Auth.getUser();
                
                if (!user) {
                    window.location.href = 'index.php';
                    return;
                }

                await loadCourseDetails(courseId, user);
                console.log("Course details loaded");
            } catch (error) {
                console.error("Error in initialization:", error);
            }
        });

        function loadCourseDetails(courseId, user) {
            fetch(`api/course-details.php?course_id=${courseId}&user_id=${user.user_id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayCourseHeader(data.course, courseId, user);
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        async function displayCourseHeader(course, courseId, user) {
            console.log(user.user_id);
            document.getElementById('courseHeader').innerHTML = `
                <div class="course-info">
                    <h1>${course.title}</h1>
                    <p>${course.description}</p>
                    <div class="course-meta">
                        <span><i class="fas fa-video"></i> ${course.total_video_count} Videos</span>
                        <span><i class="fas fa-check-circle"></i> ${course.completed_video_count} Completed</span>
                        <span><i class="fas fa-chart-line"></i> ${course.progress}% Progress</span>
                    </div>
                </div>
                <div class="course-progress">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: ${course.progress}%"></div>
                    </div>
                </div>
                <div class="course-actions">
                    <button onclick="markCourseCompleted('${courseId}', '${user.user_id}')" class="btn btn-primary">
                        <i class="fas fa-check"></i> Mark as Completed
                    </button>
                    <button onclick="takeTest('${courseId}', '${user.user_id}')" class="btn btn-primary">
                        <i class="fas fa-pencil-alt"></i> Take Test
                    </button>
                    <button onclick="downloadCertificate('${courseId}', '${user.user_id}')" class="btn btn-success">
                        <i class="fas fa-certificate"></i> Download Certificate
                    </button>
                </div>
            `;
            const vcount = course.total_video_count;
            console.log(vcount);
            let loadCount = 0;
            while(vcount-loadCount>0){
                if(vcount-loadCount>=5){

                    await loadCourseVideos(loadCount+1, loadCount+5, courseId);
                    loadCount+=5;
                }else{
                    await loadCourseVideos(loadCount+1, vcount, courseId);
                    loadCount+=vcount-loadCount;
                }
            }
        }

        async function loadCourseVideos(start, end, courseId) {
            try {
                const response = await fetch(`./api/course-videos.php?course_id=${courseId}&start=${start}&end=${end}`);
                const data = await response.json();
                if (data.success) {
                    const videoList = document.getElementById('videoList');
                    let index = start;
                    Object.keys(data.videos).forEach((videoKey) => {
                        const video = data.videos[videoKey];
                        videoList.appendChild(createVideoCard(video, index++));
                    });
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        function createVideoCard(video, index) {
            const card = document.createElement('div');
            card.className = 'video-card';
            card.style.cssText = `
                display: flex;
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
                background: #1e1e2f;
                border-radius: 12px;
                padding: 16px;
                margin-bottom: 20px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
                color: white;
            `;

            card.innerHTML = `
                <div class="video-thumbnail" style="flex-shrink: 0;">
                    <img src="${video.thumbnail}" alt="${video.title}" style="width: 120px; height: 80px; object-fit: cover; border-radius: 8px;">
                </div>
                <div class="video-info" style="flex-grow: 1; margin-left: 16px;">
                    <div class="video-number" style="font-size: 14px; color: #888;">#${index}</div>
                    <div class="video-details">
                        <h3 style="margin: 4px 0; font-size: 18px;">${video.title}</h3>
                        <p style="margin: 0; font-size: 14px; color: #ccc;">Duration: ${video.duration_minutes} minutes</p>
                    </div>
                </div>
                <div class="video-actions" style="margin-left: auto;">
                    <a href="${video.url}" target="_blank" style="
                        background: #00cec9;
                        color: #000;
                        padding: 8px 16px;
                        text-decoration: none;
                        border-radius: 6px;
                        font-size: 14px;
                        display: inline-flex;
                        align-items: center;
                        gap: 6px;
                        transition: background 0.3s;
                    " onmouseover="this.style.background='#00b4a8'" onmouseout="this.style.background='#00cec9'">
                        <i class="fas fa-play"></i> Watch Video
                    </a>
                </div>
            `;
            return card;
        }




        function markAsComplete(videoId) {
            fetch('api/mark-video-complete.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    user_id: user.user_id,
                    course_id: courseId,
                    video_id: videoId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadCourseDetails();
                    loadCourseVideos();
                }
            })
            .catch(error => console.error('Error:', error));
        }
        // Add these new functions
        function markCourseCompleted(courseId, userid) {
            fetch(`api/check-test-status.php?course_id=${courseId}&user_id=${userid}`)
                .then(response => response.json())
                .then(data => {
                    if (!data.test_given) {
                        alert('Please complete the test before marking the course as completed.');
                        return;
                    }
                    
                    fetch('api/mark-course-complete.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            user_id: userid,
                            course_id: courseId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Course marked as completed!');
                            loadCourseDetails();
                        }
                    })
                    .catch(error => console.error('Error:', error));
                })
                .catch(error => console.error('Error:', error));
        }

        function takeTest(courseId, userid) {
            console.log(userid);
            window.location.href = `test.php?course_id=${courseId}&user_id=${userid}`;
        }

        function downloadCertificate(courseId, userid) {
            fetch(`api/check-certificate-eligibility.php?course_id=${courseId}&user_id=${userid}`)
                .then(response => response.json())
                .then(data => {
                    if (!data.test_given) {
                        alert('Please complete the test first to get your certificate.');
                        return;
                    }
                    if (!data.course_completed) {
                        alert('Please complete the course to get your certificate.');
                        return;
                    }
                    window.location.href = `certificate.php?course_id=${courseId}&user_id=${userid}`;
                })
                .catch(error => console.error('Error:', error));
        }
    </script>
</body>
</html>