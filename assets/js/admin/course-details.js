function loadCourseVideos() {
    const courseId = new URLSearchParams(window.location.search).get('id');
    if (!courseId) return;

    fetch(`course-details.php?action=get_videos&course_id=${courseId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const videoList = document.getElementById('videoList');
                videoList.innerHTML = data.videos.map((video, index) => `
                    <div class="video-item">
                        <div class="video-number">${index + 1}</div>
                        <div class="video-info">
                            <h3>${video.title}</h3>
                            <p>${video.description}</p>
                            <span class="video-duration">${video.duration}</span>
                        </div>
                    </div>
                `).join('');
            } else {
                throw new Error(data.error);
            }
        })
        .catch(error => {
            console.error('Error loading videos:', error);
            showAlert('error', 'Failed to load course videos');
        });
}

// Call this function when the page loads
document.addEventListener('DOMContentLoaded', loadCourseVideos);