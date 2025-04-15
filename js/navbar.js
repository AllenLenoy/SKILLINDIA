document.addEventListener('DOMContentLoaded', function() {
    const navbarToggle = document.querySelector('.navbar-toggle');
    const navbarMenu = document.querySelector('.navbar-menu');
    
    if (navbarToggle && navbarMenu) {
        navbarToggle.addEventListener('click', function() {
            navbarToggle.classList.toggle('active');
            navbarMenu.classList.toggle('active');
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!navbarMenu.contains(event.target) && !navbarToggle.contains(event.target)) {
                navbarToggle.classList.remove('active');
                navbarMenu.classList.remove('active');
            }
        });
    }
}); 