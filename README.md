Skill India App Documentation

Overview
The Skill India App is a web-based application designed to provide skill development programs, certifications, and job opportunities to bridge the gap between education and employment. This project utilizes HTML, CSS, JavaScript, and PHP for the backend, with a focus on creating a detailed, clean, and user-friendly multi-page website.
Project Structure
The project directory is organized as follows:

admin/: Contains admin-related files.
api/: API endpoints for the application.
assets/: Static assets like images and styles.
config/: Configuration files (e.g., config.php).
css/: CSS files for styling.
database/: Database-related files (e.g., database.sql, new_skill_india.sql).
images/: Image assets.
includes/: Reusable PHP includes.
js/: JavaScript files.
vendor/: Dependencies managed by Composer.
.env: Environment configuration.
auth.php: Authentication logic.
browse-courses.php: Page for browsing courses.
certificate.php: Certificate generation logic.
composer.json & composer.lock: Composer dependency files.
config.php: Main configuration file.
course_functions.php: Course-related functions.
course-details.php: Course details page.
course-videos.php: Course video content.
course.php & courses.php: Course listing pages.
dashboard.php: User dashboard.
enroll-course.php: Course enrollment logic.
error.log: Error logs.
index.php: Homepage.
lesson.php: Lesson content.
login.php: Login page.
my-courses.php: My courses page.
navbar.php: Navigation bar include.
otp-verification.php: OTP verification logic.
setup_database.php: Database setup script.
setup_temp_users.php: Temporary user setup.
sidebar.php: Sidebar include.
signup.php: Signup page.
test.php: Testing file.
view-jobs.php: Job listings page.
youtube.php & youtube2.php: YouTube integration files.

Features

Multi-Page Navigation: Includes homepage, login/signup, course browsing, dashboard, and more.
User Authentication: Login and signup functionality with OTP verification.
Course Management: Browse, enroll, and view course details and videos.
Certification: Generate certificates upon course completion.
Job Opportunities: View available job listings.
Responsive Design: Clean and user-friendly interface using CSS.

Technologies Used

HTML: Structure of the web pages.
CSS: Styling and layout.
JavaScript: Client-side interactivity.
PHP: Backend logic and database interaction.
MySQL: Database management (via database.sql).

Setup Instructions

Clone the Repository: Clone this project to your local machine.
Install Dependencies: Run composer install to install PHP dependencies.
Configure Environment: Update the .env file with your database credentials.
Set Up Database: Import database.sql and new_skill_india.sql into your MySQL server using a tool like phpMyAdmin or the command line.
Run the Application: Start your local server (e.g., XAMPP, WAMP) and access index.php via http://localhost/your_project_folder.

Usage

Navigate to index.php to start.
Use login.php or signup.php to authenticate.
Explore courses via browse-courses.php and enroll using enroll-course.php.
View your progress on dashboard.php and certificates on certificate.php.
Check job opportunities at view-jobs.php.

Contributing
Feel free to fork this repository, make improvements, and submit pull requests. Ensure to follow the existing code style and structure.
License
This project is licensed under the MIT License - see the LICENSE.md file for details.
Contact
For any queries, please open an issue on this repository or contact the maintainers.
