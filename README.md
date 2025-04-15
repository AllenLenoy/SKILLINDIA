
# Skill India App 🇮🇳

## 🌐 Overview

The **Skill India App** is a web application designed to empower individuals with access to **skill development programs**, **certifications**, and **job opportunities**, aiming to bridge the gap between education and employment. This user-friendly platform supports browsing courses, enrolling, viewing lessons, obtaining certificates, and applying for jobs.

---

## 🧰 Tech Stack

- **Frontend:** HTML, CSS, JavaScript
- **Backend:** PHP
- **Database:** MySQL (SQL Scripts Included)
- **Package Manager:** Composer

---

## 📁 Project Structure

```
SkillIndia/
│
├── admin/                  # Admin panel (if implemented)
├── api/                    # API endpoints (if used)
├── assets/                 # Static assets (images, styles, JS)
├── config/                 # Configuration files
│   └── config.php
├── css/                    # Custom CSS stylesheets
├── database/               # SQL database files and setups
├── images/                 # Image assets
├── includes/               # Included reusable PHP files (header, footer, etc.)
├── js/                     # JavaScript files
├── vendor/                 # Composer dependencies
│
├── auth.php                # Handles authentication
├── browse-courses.php      # Browse available courses
├── certificate.php         # Generate/view certificates
├── course-functions.php    # PHP functions related to course logic
├── course-details.php      # Individual course detail page
├── course-videos.php       # Course video lessons
├── courses.php             # Courses listing
├── dashboard.php           # User dashboard
├── enroll-course.php       # Enroll in courses
├── error.log               # Error logs
├── index.php               # Homepage
├── lesson.php              # Course lessons
├── login.php               # Login page
├── logout.php              # Logout endpoint
├── my-courses.php          # User's enrolled courses
├── navbar.php              # Navigation bar
├── new_skill_india.sql     # Main SQL schema
├── otp-verification.php    # OTP for user verification
├── setup_database.php      # Initial DB setup
├── setup_temp_users.php    # Temporary users table setup
├── sidebar.php             # Sidebar layout
├── signup.php              # Signup page
├── test.php                # For testing/debugging
├── view-jobs.php           # Browse job opportunities
├── youtube.php             # YouTube video integration
├── youtube2.php            # Secondary YouTube integration
├── .env                    # Environment variables (DB credentials etc.)
├── composer.json           # PHP dependency definitions
└── composer.lock           # Composer lock file
```

---

## ⚙️ Setup Instructions

### ✅ Prerequisites

- PHP 7.x or 8.x
- MySQL / MariaDB
- Apache / Nginx server (XAMPP, LAMP, or WAMP recommended for local setup)
- Composer installed (for managing PHP packages)

### 🖥️ Installation

1. **Clone the repository** (if using version control):

   ```bash
   git clone https://github.com/your-username/skill-india-app.git
   ```

2. **Set up the database:**

   - Import `database.sql` and `new_skill_india.sql` into your MySQL database using PHPMyAdmin or MySQL CLI.

3. **Configure environment:**

   - Rename `.env.example` to `.env` and update the database credentials.

   ```env
   DB_HOST=localhost
   DB_USER=root
   DB_PASS=
   DB_NAME=skill_india
   ```

4. **Install PHP dependencies (if any):**

   ```bash
   composer install
   ```

5. **Run the app:**

   Place the folder inside your web server directory (`htdocs/` in XAMPP), and open:

   ```
   http://localhost/skill-india-app/index.php
   ```

---

## 🧑‍💻 Key Features

- ✅ User Authentication (Login, Signup, OTP Verification)
- 📚 Course Browsing and Enrollment
- 🎥 Video Lessons Integration (YouTube)
- 🎓 Certificate Generation
- 📄 Dashboard & My Courses Section
- 💼 Job Listings
- 🔐 Admin/Setup Scripts for Initial Configuration

---

## 💡 Future Enhancements

- Admin panel for course and user management
- Resume builder tool
- Skill assessment quizzes
- Mobile-responsive UI improvements
- API integration for job postings

---

## 🤝 Contributors

- Allen Lenoy
- Adithya Padmanabhan
- Harsh
- Adwaith Ashokan
  

---

## 📜 License

This project is for educational purposes and is not under any commercial license yet.

---

## 🙌 Acknowledgements

Inspired by the **Skill India Mission** 🇮🇳 by the Government of India.

---

