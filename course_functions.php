<?php
require_once 'config.php';

// Fetch all courses
function getAllCourses() {
    global $conn;
    $courses = [];
    
    $query = "SELECT * FROM courses ORDER BY title";
    $result = $conn->query($query);
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $courses[] = $row;
        }
    }
    
    return $courses;
}

// Fetch course by ID
function getCourseById($course_id) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT * FROM courses WHERE course_id = ?");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_assoc();
}

// Fetch user's enrolled courses
function getUserCourses($user_id) {
    global $conn;
    $courses = [];
    
    $stmt = $conn->prepare("SELECT c.*, e.completion_percentage 
                           FROM courses c 
                           JOIN enrollments e ON c.course_id = e.course_id 
                           WHERE e.user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $courses[] = $row;
        }
    }
    
    return $courses;
}

// Check if user is enrolled in a course
function isUserEnrolled($user_id, $course_id) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT enrollment_id FROM enrollments WHERE user_id = ? AND course_id = ?");
    $stmt->bind_param("ii", $user_id, $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->num_rows > 0;
}

// Enroll user in a course
function enrollUserInCourse($user_id, $course_id) {
    global $conn;
    
    // Check if already enrolled
    if (isUserEnrolled($user_id, $course_id)) {
        return false;
    }
    
    // Generate and send OTP
    $otp = generateOTP();
    $expires_at = date('Y-m-d H:i:s', strtotime('+15 minutes'));
    
    $stmt = $conn->prepare("INSERT INTO otps (user_id, otp_code, purpose, expires_at) VALUES (?, ?, 'course_enrollment', ?)");
    $stmt->bind_param("iss", $user_id, $otp, $expires_at);
    
    if ($stmt->execute()) {
        // Get user email
        $stmt = $conn->prepare("SELECT email FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        
        // Send OTP email
        $subject = "Course Enrollment OTP - Skill India";
        $body = "Your OTP for course enrollment is: <strong>$otp</strong>. This OTP is valid for 15 minutes.";
        
        if (sendEmail($user['email'], $subject, $body)) {
            $_SESSION['course_id'] = $course_id;
            $_SESSION['otp_purpose'] = 'course_enrollment';
            return true;
        }
    }
    
    return false;
}

// Fetch course lessons from YouTube playlist
function getCourseLessons($course_id) {
    global $conn;
    
    // First check if we have cached lessons in database
    $stmt = $conn->prepare("SELECT * FROM lessons WHERE course_id = ? ORDER BY position");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $lessons = [];
        while ($row = $result->fetch_assoc()) {
            $lessons[] = $row;
        }
        return $lessons;
    }
    
    // If no cached lessons, fetch from YouTube API
    $course = getCourseById($course_id);
    if (!$course || empty($course['youtube_playlist_id'])) {
        return [];
    }
    
    $playlistId = $course['youtube_playlist_id'];
    $apiKey = YOUTUBE_API_KEY;
    
    // Fetch playlist items
    $url = "https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&maxResults=50&playlistId=$playlistId&key=$apiKey";
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    
    if (!isset($data['items'])) {
        return [];
    }
    
    $lessons = [];
    $position = 1;
    
    foreach ($data['items'] as $item) {
        $videoId = $item['snippet']['resourceId']['videoId'];
        $title = $item['snippet']['title'];
        $description = $item['snippet']['description'];
        
        // Get video duration
        $videoUrl = "https://www.googleapis.com/youtube/v3/videos?part=contentDetails&id=$videoId&key=$apiKey";
        $videoResponse = file_get_contents($videoUrl);
        $videoData = json_decode($videoResponse, true);
        
        $duration = 'PT0S';
        if (isset($videoData['items'][0]['contentDetails']['duration'])) {
            $duration = $videoData['items'][0]['contentDetails']['duration'];
        }
        
        // Format duration (e.g., PT5M30S -> 5:30)
        $formattedDuration = formatYouTubeDuration($duration);
        
        // Save to database
        $stmt = $conn->prepare("INSERT INTO lessons (course_id, youtube_video_id, title, description, duration, position) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssi", $course_id, $videoId, $title, $description, $formattedDuration, $position);
        $stmt->execute();
        
        $lessons[] = [
            'lesson_id' => $stmt->insert_id,
            'course_id' => $course_id,
            'youtube_video_id' => $videoId,
            'title' => $title,
            'description' => $description,
            'duration' => $formattedDuration,
            'position' => $position
        ];
        
        $position++;
    }
    
    return $lessons;
}

// Format YouTube duration (PT5M30S -> 5:30)
function formatYouTubeDuration($duration) {
    $interval = new DateInterval($duration);
    $seconds = $interval->s;
    $minutes = $interval->i;
    $hours = $interval->h;
    
    if ($hours > 0) {
        return sprintf("%d:%02d:%02d", $hours, $minutes, $seconds);
    } else {
        return sprintf("%d:%02d", $minutes, $seconds);
    }
}

// Mark lesson as completed
function markLessonCompleted($user_id, $lesson_id) {
    global $conn;
    
    // Check if already completed
    $stmt = $conn->prepare("SELECT completion_id FROM completed_lessons WHERE user_id = ? AND lesson_id = ?");
    $stmt->bind_param("ii", $user_id, $lesson_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return true;
    }
    
    // Mark as completed
    $stmt = $conn->prepare("INSERT INTO completed_lessons (user_id, lesson_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $lesson_id);
    
    if ($stmt->execute()) {
        // Update course completion percentage
        updateCourseCompletion($user_id, $lesson_id);
        return true;
    }
    
    return false;
}

// Update course completion percentage
function updateCourseCompletion($user_id, $lesson_id) {
    global $conn;
    
    // Get course ID from lesson
    $stmt = $conn->prepare("SELECT course_id FROM lessons WHERE lesson_id = ?");
    $stmt->bind_param("i", $lesson_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $lesson = $result->fetch_assoc();
    $course_id = $lesson['course_id'];
    
    // Count total lessons in course
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM lessons WHERE course_id = ?");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $total = $result->fetch_assoc()['total'];
    
    // Count completed lessons by user
    $stmt = $conn->prepare("SELECT COUNT(*) as completed 
                           FROM completed_lessons cl 
                           JOIN lessons l ON cl.lesson_id = l.lesson_id 
                           WHERE cl.user_id = ? AND l.course_id = ?");
    $stmt->bind_param("ii", $user_id, $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $completed = $result->fetch_assoc()['completed'];
    
    // Calculate percentage
    $percentage = $total > 0 ? round(($completed / $total) * 100) : 0;
    
    // Update enrollment record
    $stmt = $conn->prepare("UPDATE enrollments SET completion_percentage = ? WHERE user_id = ? AND course_id = ?");
    $stmt->bind_param("iii", $percentage, $user_id, $course_id);
    $stmt->execute();
    
    return $percentage;
}

// Get completed lessons for a user in a course
function getCompletedLessons($user_id, $course_id) {
    global $conn;
    $completed = [];
    
    $stmt = $conn->prepare("SELECT l.lesson_id 
                           FROM lessons l 
                           JOIN completed_lessons cl ON l.lesson_id = cl.lesson_id 
                           WHERE cl.user_id = ? AND l.course_id = ?");
    $stmt->bind_param("ii", $user_id, $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $completed[] = $row['lesson_id'];
    }
    
    return $completed;
}
?>