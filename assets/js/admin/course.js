const CourseManager = {
    courseToDelete: null,

    init() {
        this.loadCourses();
        this.setupEventListeners();
    },

    setupEventListeners() {
        // Event delegation for delete buttons
        document.querySelector('.table').addEventListener('click', (e) => {
            if (e.target.matches('.btn-danger')) {
                e.preventDefault();
                const courseId = e.target.dataset.courseId;
                this.showDeleteConfirmation(courseId);
            }
        });
    },

    async loadCourses() {
        try {
            const response = await fetch('courses.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ action: 'get_courses' })
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new TypeError("Oops, we haven't got JSON!");
            }

            const data = await response.json();
            if (data.courses) {
                this.displayCourses(data.courses);
            } else if (data.error) {
                this.showError(data.error);
            } else {
                this.showError('No courses data received');
            }
        } catch (error) {
            console.error('Error loading courses:', error);
            this.showError('Failed to load courses. Please try again.');
        }
    },

    displayCourses(courses) {
        const tbody = document.querySelector('.table tbody');
        if (!courses || courses.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center">No courses found</td>
                </tr>`;
            return;
        }

        tbody.innerHTML = courses.map(course => `
            <tr>
                <td>${this.escapeHtml(course.title)}</td>
                <td>${this.escapeHtml(course.description || 'N/A')}</td>


                <td>${this.escapeHtml(course.video_count || 'N/A')}</td>
                <td>${this.capitalizeFirst(course.duration)}</td>
                <td>${this.capitalizeFirst(course.level)}</td>
                <td>${this.capitalizeFirst(course.status)}</td>
                <td>${new Date(course.created_at).toLocaleDateString()}</td>
                <td>
                    <a href="edit-course.php?id=${course.course_id}" class="btn btn-primary">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button class="btn btn-danger" data-course-id="${course.course_id}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');
    },

    showDeleteConfirmation(courseId) {
        this.courseToDelete = courseId;
        const modal = document.getElementById('confirmationModal');
        modal.style.display = 'block';
    },

    closeModal() {
        const modal = document.getElementById('confirmationModal');
        modal.style.display = 'none';
        this.courseToDelete = null;
    },

    async confirmDelete() {
        if (!this.courseToDelete) return;

        try {
            const response = await fetch('../api/admin/delete-course.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    course_id: this.courseToDelete
                })
            });

            const data = await response.json();
            
            if (data.success) {
                this.showSuccess('Course deleted successfully');
                this.loadCourses(); // Reload the courses list
            } else {
                this.showError(data.message || 'Failed to delete course');
            }
        } catch (error) {
            console.error('Error deleting course:', error);
            this.showError('Failed to delete course. Please try again.');
        }

        this.closeModal();
    },

    showSuccess(message) {
        const alertContainer = document.getElementById('alertContainer');
        alertContainer.innerHTML = `
            <div class="alert alert-success">
                ${this.escapeHtml(message)}
            </div>`;
        this.removeAlert();
    },

    showError(message) {
        const alertContainer = document.getElementById('alertContainer');
        alertContainer.innerHTML = `
            <div class="alert alert-danger">
                ${this.escapeHtml(message)}
            </div>`;
        this.removeAlert();
    },

    removeAlert() {
        setTimeout(() => {
            const alertContainer = document.getElementById('alertContainer');
            const alert = alertContainer.querySelector('.alert');
            if (alert) {
                alert.style.opacity = '0';
                setTimeout(() => {
                    alertContainer.innerHTML = '';
                }, 300);
            }
        }, 3000);
    },

    escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    },

    capitalizeFirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
    }
};

// Initialize the CourseManager when the DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    CourseManager.init();
}); 