// DOM Elements
const lessonLinks = document.querySelectorAll('.lesson a');
const completeButtons = document.querySelectorAll('.lesson-actions .btn-primary');
const testCards = document.querySelectorAll('.test-card');

// Lesson Navigation
lessonLinks.forEach(link => {
    link.addEventListener('click', (e) => {
        if (link.classList.contains('btn-primary')) {
            e.preventDefault();
            
            // Mark lesson as completed (in a real app, this would be saved to the backend)
            const lesson = link.closest('.lesson');
            lesson.classList.add('completed');
            lesson.classList.remove('current');
            
            // Update progress
            updateCourseProgress();
            
            // Show completion message
            alert('Lesson marked as completed!');
        }
    });
});

// Complete Lesson Buttons
completeButtons.forEach(button => {
    button.addEventListener('click', (e) => {
        e.preventDefault();
        
        // Mark lesson as completed (in a real app, this would be saved to the backend)
        const lesson = button.closest('.lesson');
        lesson.classList.add('completed');
        lesson.classList.remove('current');
        
        // Update progress
        updateCourseProgress();
        
        // Show completion message
        alert('Lesson marked as completed!');
    });
});

// Update Course Progress
function updateCourseProgress() {
    const sections = document.querySelectorAll('.section');
    let totalLessons = 0;
    let completedLessons = 0;
    
    sections.forEach(section => {
        const lessons = section.querySelectorAll('.lesson');
        totalLessons += lessons.length;
        
        lessons.forEach(lesson => {
            if (lesson.classList.contains('completed')) {
                completedLessons++;
            }
        });
    });
    
    const progressPercentage = Math.round((completedLessons / totalLessons) * 100);
    
    // Update progress display
    document.querySelector('.course-progress span').textContent = `${progressPercentage}% Complete`;
    document.querySelector('.course-progress .progress-fill').style.width = `${progressPercentage}%`;
    
    // Update each section progress
    sections.forEach(section => {
        const sectionLessons = section.querySelectorAll('.lesson');
        const sectionCompleted = section.querySelectorAll('.lesson.completed').length;
        const sectionPercentage = Math.round((sectionCompleted / sectionLessons.length) * 100);
        
        section.querySelector('.section-header span').textContent = `${sectionCompleted}/${sectionLessons.length} lessons completed`;
    });
}

// Test Card Click
testCards.forEach(card => {
    if (!card.classList.contains('locked')) {
        card.addEventListener('click', (e) => {
            if (e.target.tagName !== 'A' && e.target.tagName !== 'BUTTON') {
                alert('Test would start here in the full implementation');
            }
        });
    }
});

// Initialize course progress on page load
document.addEventListener('DOMContentLoaded', () => {
    updateCourseProgress();
});

// Resource Download
document.querySelectorAll('.resource-actions .btn-outline').forEach(button => {
    button.addEventListener('click', (e) => {
        e.preventDefault();
        alert('Resource would download here in the full implementation');
    });
});

// Discussion Tab (placeholder)
const discussionTab = document.getElementById('discussion');
if (discussionTab) {
    discussionTab.innerHTML = `
        <div class="discussion-placeholder">
            <i class="fas fa-comments" style="font-size: 3rem; color: var(--primary-color); margin-bottom: 20px;"></i>
            <h4>Course Discussion</h4>
            <p>Engage with other students and instructors in the course discussion forum.</p>
            <button class="btn btn-primary">Go to Discussion</button>
        </div>
    `;
}