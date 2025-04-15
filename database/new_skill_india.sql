-- Drop and recreate database
DROP DATABASE IF EXISTS new_skill_india;
CREATE DATABASE new_skill_india;
USE new_skill_india;

-- Create users table
CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    is_verified BOOLEAN DEFAULT FALSE,
    otp VARCHAR(6),
    otp_expiry TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create fresh admin user
INSERT INTO users (name, email, password, role, is_verified) 
VALUES (
    'Admin',
    'admin@skillindia.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: admin123
    'admin',
    1
);

-- Categories table
CREATE TABLE categories (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Courses table
CREATE TABLE courses (
    course_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    instructor VARCHAR(100) NOT NULL,
    category_id INT,
    duration VARCHAR(50) NOT NULL,
    difficulty_level ENUM('beginner', 'intermediate', 'advanced') NOT NULL,
    youtube_playlist_id VARCHAR(100) NOT NULL,
    thumbnail_url VARCHAR(255),
    status ENUM('draft', 'published') NOT NULL DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE SET NULL
);

-- Course videos table
CREATE TABLE course_videos (
    video_id VARCHAR(50) PRIMARY KEY,
    course_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    duration VARCHAR(50),
    position INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(course_id) ON DELETE CASCADE
);

-- Enrollments table
CREATE TABLE enrollments (
    enrollment_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completion_status ENUM('not_started', 'in_progress', 'completed') DEFAULT 'not_started',
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(course_id) ON DELETE CASCADE
);

-- Progress tracking table
CREATE TABLE progress (
    progress_id INT PRIMARY KEY AUTO_INCREMENT,
    enrollment_id INT NOT NULL,
    video_id VARCHAR(50) NOT NULL,
    watched BOOLEAN DEFAULT FALSE,
    last_watched_at TIMESTAMP,
    FOREIGN KEY (enrollment_id) REFERENCES enrollments(enrollment_id) ON DELETE CASCADE,
    FOREIGN KEY (video_id) REFERENCES course_videos(video_id) ON DELETE CASCADE
);

-- Insert some default categories
INSERT INTO categories (name, description) VALUES
('Web Development', 'Learn web development technologies and frameworks'),
('Mobile Development', 'Mobile app development for iOS and Android'),
('Data Science', 'Data analysis, machine learning, and artificial intelligence'),
('Digital Marketing', 'Social media, SEO, and online marketing strategies'); 