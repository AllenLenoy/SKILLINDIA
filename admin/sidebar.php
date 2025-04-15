<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar">
      <!-- Auth Script -->
  <script src="../js/auth.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
              document.getElementById('logoutBtn').addEventListener('click', function (e) {
                    e.preventDefault();
                    Auth.logout();
                    alert("You have been logged out!");
                    window.location.href = '../login.php';
                });
    });
    </script>
    <div class="sidebar-header">
        <h2>Admin Panel</h2>
    </div>
    <nav class="sidebar-nav">
        <ul>
            <li>
                <a href="dashboard.php" class="<?php echo $current_page === 'dashboard.php' ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="courses.php" class="<?php echo $current_page === 'courses.php' ? 'active' : ''; ?>">
                    <i class="fas fa-book"></i> Courses
                </a>
            </li>
            <li>
                <a href="create-job.php" class="<?php echo $current_page === 'courses.php' ? 'active' : ''; ?>">
                    <i class="fas fa-briefcase"></i> Create & View Jobs
                </a>
            </li>

            <li>
                <a id="logoutBtn" href="../signin.php" >
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </nav>
</aside>

<style>
.sidebar {
    width: 250px;
    background: #343a40;
    color: white;
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    box-shadow: 2px 0 5px rgba(0,0,0,0.1);
}

.sidebar-header {
    padding: 20px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    background: #2c3136;
}

.sidebar-header h2 {
    margin: 0;
    font-size: 1.2rem;
    color: #fff;
    font-weight: 500;
}

.sidebar-nav ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.sidebar-nav a {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    color: rgba(255,255,255,0.8);
    text-decoration: none;
    transition: all 0.3s;
    font-size: 0.9rem;
}

.sidebar-nav a:hover {
    background: rgba(255,255,255,0.1);
    color: white;
    padding-left: 25px;
}

.sidebar-nav a.active {
    background: rgba(255,255,255,0.1);
    color: white;
    border-left: 4px solid #007bff;
    font-weight: 500;
}

.sidebar-nav i {
    margin-right: 10px;
    width: 20px;
    text-align: center;
    font-size: 1rem;
}

.main-content {
    margin-left: 250px;
    padding: 20px;
    min-height: 100vh;
}
</style> 