<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar">
    <div class="container">
        <div class="navbar-brand">
            <a href="index.php">
                <img src="assets/images/logo.png" alt="Skill India Logo">
                <span>Skill India</span>
            </a>
        </div>
        
        <div class="navbar-menu">
            <ul class="navbar-nav">
                <li class="nav-item <?php echo $current_page === 'index.php' ? 'active' : ''; ?>">
                    <a href="index.php">Home</a>
                </li>
                <li class="nav-item <?php echo $current_page === 'course.php' ? 'active' : ''; ?>">
                    <a href="course.php">Courses</a>
                </li>
                <li class="nav-item <?php echo $current_page === 'lesson.php' ? 'active' : ''; ?>">
                    <a href="lesson.php">Lessons</a>
                </li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item <?php echo $current_page === 'dashboard.php' ? 'active' : ''; ?>">
                        <a href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a href="?logout=1">Logout</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item <?php echo $current_page === 'login.php' ? 'active' : ''; ?>">
                        <a href="login.php">Login</a>
                    </li>
                    <li class="nav-item <?php echo $current_page === 'signup.php' ? 'active' : ''; ?>">
                        <a href="signup.php" class="btn btn-primary">Sign Up</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
        
        <div class="navbar-toggle">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
</nav> 