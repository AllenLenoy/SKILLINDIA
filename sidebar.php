<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Sidebar - Skill India</title>
  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="css/sidebar.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
</head>
<body>

  <div class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <div class="logo">
      <a href="index.php" class="logo" id="logo" style="font-size: 25px; font-weight: 900; text-transform: uppercase; letter-spacing: 2px; background: linear-gradient(45deg, #6c5ce7, #00cec9); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Skill<span style="font-style: italic; -webkit-text-fill-color: #00cec9; text-shadow: 2px 2px 4px rgba(0,0,0,0.2);">India</span></a>
      </div>
      <button class="toggle-btn" id="toggleSidebar">
        <i class="fas fa-bars"></i>
      </button>
    </div>

    <div class="sidebar-content">
      <div class="user-profile">
        <div class="profile-image">
          <i class="fas fa-user"></i>
        </div>
        <div class="profile-info">
          <h3 id="sidebarUserName">Loading...</h3>
          <p id="sidebarUserEmail">Loading...</p>
        </div>
      </div>

      <nav class="sidebar-nav">
        <ul>
          <li>
            <a href="dashboard.php" class="nav-link">
              <i class="fas fa-home"></i>
              <span>Dashboard</span>
            </a>
          </li>
          <li>
            <a href="courses.php" class="nav-link">
              <i class="fas fa-book"></i>
              <span>Courses</span>
            </a>
          </li>
          <li>
            <a href="my-courses.php" class="nav-link">
              <i class="fas fa-graduation-cap"></i>
              <span>My Courses</span>
            </a>
          </li>

          <!-- View Job Opportunities (For All Users) -->
          <li>
            <a href="view-jobs.php" class="nav-link">
              <i class="fas fa-briefcase"></i>
              <span>View Job Opportunities</span>
            </a>
          </li>

          <!-- Admin Only: Create Job Vacancy -->
          <li class="admin-section" id="adminSection" style="display: none;">
            <a href="admin/create-job.php" class="nav-link">
              <i class="fas fa-plus-circle"></i>
              <span>Create Job Vacancy</span>
            </a>
          </li>

          <li id="logout">
            <a href="#" id="logoutBtn" class="nav-link">
              <i class="fas fa-sign-out-alt"></i>
              <span>Logout</span>
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </div>

  <!-- Auth Script -->
  <script src="./js/auth.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const user = Auth.getUser();

      if (!user) {
        window.location.href = 'login.php';
        return;
      }

      document.getElementById('sidebarUserName').textContent = user.name;
      document.getElementById('sidebarUserEmail').textContent = user.email;

      // Show admin section only if role is admin
      const adminSection = document.getElementById('adminSection');
      if (user.role === 'admin') {
        adminSection.style.display = 'block';
      }

      // Logout Logic
      document.getElementById('logoutBtn').addEventListener('click', function (e) {
        e.preventDefault();
        Auth.logout();
        alert("You have been logged out!");
        window.location.href = 'login.php';
      });

      // Toggle sidebar
      const toggleBtn = document.getElementById('toggleSidebar');
      const sidebar = document.getElementById('sidebar');
      toggleBtn.addEventListener('click', function () {
        sidebar.classList.toggle('collapsed');
      });

      // Highlight active link
      const currentPath = window.location.pathname.split('/').pop();
      const navLinks = document.querySelectorAll('.nav-link');
      navLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (href === currentPath) {
          link.classList.add('active');
        }
      });
    });
  </script>

</body>
</html>
