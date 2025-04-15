// DOM Elements
const enrollButtons = document.querySelectorAll('.enroll-btn');
const enrollModal = document.getElementById('enrollModal');
const enrollmentForm = document.getElementById('enrollmentForm');
const otpModal = document.getElementById('otpModal');

// Course Enrollment Modal
enrollButtons.forEach(button => {
    button.addEventListener('click', (e) => {
        e.preventDefault();

        // Get course data from button attributes
        const courseId = button.getAttribute('data-course');
        let courseData = {};

        // Simple course data for demo
        switch (courseId) {
            case 'flutter':
                courseData = {
                    title: 'Mobile App Development with Flutter',
                    instructor: 'Jane Smith',
                    rating: '4.8',
                    students: '1.2k',
                    duration: '8',
                    description: 'Learn to build cross-platform mobile applications using Flutter framework and Dart programming language.',
                    image: 'https://via.placeholder.com/300x200'
                };
                break;
            case 'aws':
                courseData = {
                    title: 'Cloud Computing with AWS',
                    instructor: 'Robert Johnson',
                    rating: '4.9',
                    students: '2.5k',
                    duration: '10',
                    description: 'Master Amazon Web Services and learn to deploy scalable cloud applications with this comprehensive course.',
                    image: 'https://via.placeholder.com/300x200'
                };
                break;
            case 'uiux':
                courseData = {
                    title: 'UI/UX Design Fundamentals',
                    instructor: 'Sarah Williams',
                    rating: '4.7',
                    students: '850',
                    duration: '6',
                    description: 'Learn the principles of user interface and user experience design to create beautiful and functional digital products.',
                    image: 'https://via.placeholder.com/300x200'
                };
                break;
        }

        // Populate modal with course data
        document.getElementById('modalCourseTitle').textContent = `Enroll in ${courseData.title}`;
        document.getElementById('modalCourseName').textContent = courseData.title;
        document.getElementById('modalCourseInstructor').textContent = `Instructor: ${courseData.instructor}`;
        document.getElementById('modalCourseRating').textContent = courseData.rating;
        document.getElementById('modalCourseStudents').textContent = courseData.students;
        document.getElementById('modalCourseDuration').textContent = courseData.duration;
        document.getElementById('modalCourseDescription').textContent = courseData.description;
        document.getElementById('modalCourseImage').src = courseData.image;

        // Show enrollment modal
        enrollModal.classList.add('active');
    });
});

// Enrollment Form Submission
if (enrollmentForm) {
    enrollmentForm.addEventListener('submit', (e) => {
        e.preventDefault();

        const password = document.getElementById('enrollPassword').value;

        if (!password) {
            alert('Please enter your password');
            return;
        }

        // Close enrollment modal
        enrollModal.classList.remove('active');

        // Show OTP verification modal
        otpModal.classList.add('active');
    });
}

// Progress Bar Animation
document.querySelectorAll('.progress-fill').forEach(bar => {
    const width = bar.style.width;
    bar.style.width = '0';

    setTimeout(() => {
        bar.style.width = width;
    }, 100);
});

// Section Accordion
document.querySelectorAll('.section-header').forEach(header => {
    header.addEventListener('click', () => {
        const section = header.closest('.section');
        const lessonsList = section.querySelector('.lessons-list');

        // Toggle section collapse
        if (lessonsList.style.maxHeight) {
            lessonsList.style.maxHeight = null;
            section.classList.remove('expanded');
        } else {
            lessonsList.style.maxHeight = lessonsList.scrollHeight + 'px';
            section.classList.add('expanded');
        }
    });
});

// Initialize sections to be expanded by default
document.querySelectorAll('.lessons-list').forEach(list => {
    list.style.maxHeight = list.scrollHeight + 'px';
    list.closest('.section').classList.add('expanded');
});

// Notification Dropdown
const notifications = document.querySelector('.notifications');
if (notifications) {
    notifications.addEventListener('click', () => {
        // In a real app, you would show a dropdown with notifications
        alert('Notifications dropdown would appear here');
    });
}

// User Menu Dropdown
const userMenu = document.querySelector('.user-menu');
if (userMenu) {
    userMenu.addEventListener('click', () => {
        // In a real app, you would show a dropdown with user options
        alert('User menu dropdown would appear here');
    });
}