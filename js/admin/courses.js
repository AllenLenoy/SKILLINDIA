// Course management functionality
const CourseManager = {
    // Load courses from API
    loadCourses: async function() {
        try {
            const response = await fetch('../api/admin/courses.php');
            const data = await response.json();
            
            if (data.success) {
                this.displayCourses(data.courses);
            } else {
                this.showError(data.message || 'Failed to load courses');
            }
        } catch (error) {
            console.error('Error loading courses:', error);
            this.showError('Failed to load courses');
        }
    },

    // Display courses in the table
    displayCourses: function(courses) {
        const tbody = document.querySelector('.table tbody');
        if (!courses || courses.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center">No courses found</td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = courses.map(course => {
            // Format the date
            const createdAt = course.created_at ? new Date(course.created_at).toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric'
            }) : '-';

            // Truncate description to 100 characters
            const description = course.description ? 
                (course.description.length > 100 ? 
                    course.description.substring(0, 100) + '...' : 
                    course.description) : 
                '-';

            return `
            <tr>
                <td title="${this.escapeHtml(course.title)}">${this.escapeHtml(course.title)}</td>
                <td title="${this.escapeHtml(course.description || '')}">${this.escapeHtml(description)}</td>
                <td class="text-center">${course.video_count || 0}</td>
                <td class="text-center">${this.escapeHtml(course.duration || '-')}</td>
                <td class="text-center">${this.capitalizeFirst(course.level || '-')}</td>
                <td class="text-center">
                    <span class="status-badge ${course.status?.toLowerCase() || 'draft'}">
                        ${this.capitalizeFirst(course.status || 'Draft')}
                    </span>
                </td>
                <td class="text-center">${createdAt}</td>
                <td class="actions">
                    <a href="edit-course.php?id=${course.course_id}" 
                       class="btn btn-sm btn-edit" title="Edit">
                       <i class="fas fa-edit"></i>
                    </a>
                    <button onclick="CourseManager.deleteCourse(${course.course_id})" 
                            class="btn btn-sm btn-delete" title="Delete">
                            <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
            `;
        }).join('');
    },

    // Delete a course
    deleteCourse: async function(courseId) {
        if (!confirm('Are you sure you want to delete this course?')) {
            return;
        }

        try {
            const response = await fetch('../api/admin/courses.php', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ course_id: courseId })
            });

            const data = await response.json();
            
            if (data.success) {
                sessionStorage.setItem('adminMessage', JSON.stringify({
                    type: 'success',
                    text: 'Course deleted successfully!'
                }));
                window.location.reload();
            } else {
                this.showError(data.message || 'Failed to delete course');
            }
        } catch (error) {
            console.error('Error deleting course:', error);
            this.showError('Failed to delete course');
        }
    },

    // Show success message
    showSuccess: function(message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-success';
        alertDiv.textContent = message;
        this.insertAlert(alertDiv);
    },

    // Show error message
    showError: function(message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger';
        alertDiv.textContent = message;
        this.insertAlert(alertDiv);
    },

    // Insert alert into the page
    insertAlert: function(alertDiv) {
        const content = document.querySelector('.content');
        const existingAlert = content.querySelector('.alert');
        if (existingAlert) {
            existingAlert.remove();
        }
        content.insertBefore(alertDiv, content.firstChild.nextSibling);
        setTimeout(() => alertDiv.remove(), 5000);
    },

    // Helper function to escape HTML
    escapeHtml: function(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    },

    // Helper function to capitalize first letter
    capitalizeFirst: function(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    },

    // Check for stored messages
    checkStoredMessages: function() {
        const storedMessage = sessionStorage.getItem('adminMessage');
        if (storedMessage) {
            const message = JSON.parse(storedMessage);
            if (message.type === 'success') {
                this.showSuccess(message.text);
            } else {
                this.showError(message.text);
            }
            sessionStorage.removeItem('adminMessage');
        }
    }
};

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    CourseManager.loadCourses();
    CourseManager.checkStoredMessages();
}); 