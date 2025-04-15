-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS new_skill_india;
USE new_skill_india;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    is_verified BOOLEAN DEFAULT FALSE,
    verification_token VARCHAR(100),
    verification_expires DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Instructors table
CREATE TABLE IF NOT EXISTS instructors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    bio TEXT,
    expertise VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Courses table
CREATE TABLE IF NOT EXISTS courses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    instructor_id INT,
    category_id INT,
    price DECIMAL(10,2) DEFAULT 0.00,
    duration VARCHAR(50),
    level ENUM('beginner', 'intermediate', 'advanced'),
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (instructor_id) REFERENCES instructors(id) ON DELETE SET NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Course videos table
CREATE TABLE IF NOT EXISTS course_videos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    course_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    video_url VARCHAR(255) NOT NULL,
    duration VARCHAR(50),
    sequence_number INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

-- Enrollments table
CREATE TABLE IF NOT EXISTS enrollments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completion_date TIMESTAMP NULL,
    status ENUM('active', 'completed', 'dropped') DEFAULT 'active',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    UNIQUE KEY unique_enrollment (user_id, course_id)
);

-- Insert default admin user (password: admin123)
INSERT INTO users (name, email, password, role, is_verified) VALUES 
('Admin', 'admin@skillindia.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1);

-- Insert some sample categories
INSERT INTO categories (name, description) VALUES 
('Web Development', 'Learn web development technologies and frameworks'),
('Data Science', 'Master data analysis and machine learning'),
('Digital Marketing', 'Learn digital marketing strategies and tools');

-- Insert some sample instructors
INSERT INTO instructors (name, email, bio, expertise) VALUES 
('John Doe', 'john@example.com', 'Experienced web developer with 10 years of experience', 'Web Development, JavaScript'),
('Jane Smith', 'jane@example.com', 'Data scientist with expertise in Python and ML', 'Python, Machine Learning'),
('Mike Johnson', 'mike@example.com', 'Digital marketing expert and consultant', 'SEO, Social Media Marketing'); 