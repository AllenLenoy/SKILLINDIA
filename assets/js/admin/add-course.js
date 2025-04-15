const AddCourse = {
    init() {
        this.setupFormSubmission();
        this.setupPlaylistValidation();
    },

    setupPlaylistValidation() {
        const playlistInput = document.getElementById('playlist_link');
        const preview = document.getElementById('youtubePreview');
        let typingTimer;

        playlistInput.addEventListener('input', () => {
            clearTimeout(typingTimer);
            preview.classList.remove('active');
            
            // Wait for user to stop typing
            typingTimer = setTimeout(() => {
                const url = playlistInput.value.trim();
                if (this.isValidYoutubePlaylist(url)) {
                    this.validatePlaylist(url);
                }
            }, 1000);
        });
    },

    isValidYoutubePlaylist(url) {
        return url.includes('youtube.com') && url.includes('list=');
    },

    async validatePlaylist(url) {
        try {
            const response = await fetch('add-course.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'validate_playlist',
                    playlist_link: url
                })
            });

            const data = await response.json();
            if (data.success) {
                const preview = document.getElementById('youtubePreview');
                const videoCount = document.getElementById('videoCount');
                videoCount.textContent = data.video_count;
                preview.classList.add('active');
            } else {
                this.showError(data.error || 'Invalid playlist URL');
            }
        } catch (error) {
            console.error('Error validating playlist:', error);
            this.showError('Failed to validate playlist URL');
        }
    },

    setupFormSubmission() {
        const form = document.getElementById('addCourseForm');
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            try {
                const formData = new FormData(form);
                const courseData = Object.fromEntries(formData.entries());

                // Validate YouTube playlist URL
                if (!this.isValidYoutubePlaylist(courseData.playlist_link)) {
                    this.showError('Please enter a valid YouTube playlist URL');
                    return;
                }

                const response = await fetch('add-course.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'add_course',
                        ...courseData
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.showSuccess('Course added successfully!');
                    // Store success message in sessionStorage for courses.php
                    sessionStorage.setItem('courseMessage', 'Course added successfully!');
                    // Redirect after a brief delay
                    setTimeout(() => {
                        window.location.href = 'courses.php';
                    }, 1500);
                } else {
                    this.showError(data.error || 'Failed to add course');
                }
            } catch (error) {
                console.error('Error adding course:', error);
                this.showError('Failed to add course. Please try again.');
            }
        });
    },

    showSuccess(message) {
        const alertContainer = document.getElementById('alertContainer');
        alertContainer.innerHTML = `
            <div class="alert alert-success">
                ${this.escapeHtml(message)}
            </div>`;
    },

    showError(message) {
        const alertContainer = document.getElementById('alertContainer');
        alertContainer.innerHTML = `
            <div class="alert alert-danger">
                ${this.escapeHtml(message)}
            </div>`;
    },

    escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }
};

// Initialize when the DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    AddCourse.init();
}); 