// DOM Elements
document.addEventListener('DOMContentLoaded', () => {


    const navbar = document.querySelector('.navbar');
    const navLinks = document.querySelector('.nav-links');
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');

    // Mobile Menu Toggle


    // Sidebar Toggle
    sidebarToggle?.addEventListener('click', () => {
        sidebar.classList.toggle('sidebar-collapsed');
        mainContent.classList.toggle('sidebar-collapsed');
    });

    // Close mobile menu when clicking on a link
    document.querySelectorAll('.nav-links a').forEach(link => {
        link.addEventListener('click', () => {
            navLinks.classList.remove('active');
        });
    });



    // // Close mobile menu when clicking outside
    // document.addEventListener('click', function (event) {
    //     const navLinks = document.querySelector('.nav-links');

    //     if (!navLinks.contains(event.target) && !hamburger.contains(event.target)) {
    //         navLinks.classList.remove('active');
    //     }
    // });

    // Password Toggle
    document.querySelectorAll('.password-toggle i').forEach(icon => {
        icon.addEventListener('click', (e) => {
            const input = e.target.closest('.form-group').querySelector('input');
            if (input.type === 'password') {
                input.type = 'text';
                e.target.classList.remove('fa-eye');
                e.target.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                e.target.classList.remove('fa-eye-slash');
                e.target.classList.add('fa-eye');
            }
        });
    });

    // Modal Handling
    const modals = document.querySelectorAll('.modal');
    const closeModalButtons = document.querySelectorAll('.close-modal');

    modals.forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.remove('active');
            }
        });
    });

    closeModalButtons.forEach(button => {
        button.addEventListener('click', () => {
            button.closest('.modal').classList.remove('active');
        });
    });

    // Tab Switching
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Remove active class from all buttons and contents
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));

            // Add active class to clicked button and corresponding content
            button.classList.add('active');
            const tabId = button.getAttribute('data-tab');
            document.getElementById(tabId).classList.add('active');
        });
    });

    // OTP Input Navigation
    document.querySelectorAll('.otp-inputs input').forEach((input, index, inputs) => {
        input.addEventListener('input', (e) => {
            if (e.target.value.length === 1 && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && e.target.value.length === 0 && index > 0) {
                inputs[index - 1].focus();
            }
        });
    });

    // Form Submissions (for demo purposes)
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            alert('Form submitted successfully!');
        });
    });

    // Scroll Reveal Animation
    window.addEventListener('scroll', revealOnScroll);

    function revealOnScroll() {
        const reveals = document.querySelectorAll('.reveal');

        reveals.forEach(reveal => {
            const windowHeight = window.innerHeight;
            const revealTop = reveal.getBoundingClientRect().top;
            const revealPoint = 150;

            if (revealTop < windowHeight - revealPoint) {
                reveal.classList.add('active');
            } else {
                reveal.classList.remove('active');
            }
        });
    }

    // Initialize scroll reveal on page load



});