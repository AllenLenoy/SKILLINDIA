<?php
require_once 'config.php';
require_once 'course_functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$course_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Check if user is enrolled in this course
if (!isUserEnrolled($user_id, $course_id)) {
    $_SESSION['error'] = "You are not enrolled in this course.";
    header("Location: dashboard.php");
    exit();
}

// Get course details
$course = getCourseById($course_id);
if (!$course) {
    $_SESSION['error'] = "Course not found.";
    header("Location: dashboard.php");
    exit();
}

// Get course lessons
$lessons = getCourseLessons($course_id);

// Get completed lessons for this user
$completedLessons = getCompletedLessons($user_id, $course_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($course['title']); ?> - Skill India</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/course.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Topbar -->
        <header class="topbar">
            <div class="topbar-left">
                <button class="sidebar-toggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h2><?php echo htmlspecialchars($course['title']); ?></h2>
            </div>
            
            <div class="topbar-right">
                <div class="search-box">
                    <input type="text" placeholder="Search course...">
                    <i class="fas fa-search"></i>
                </div>
                <div class="course-progress">
                    <span>
                        <?php 
                        $enrollment = getUserEnrollment($user_id, $course_id);
                        echo $enrollment['completion_percentage'] ?? 0; 
                        ?>% Complete
                    </span>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo $enrollment['completion_percentage'] ?? 0; ?>%"></div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Course Content -->
        <main class="course-content">
            <div class="course-header">
                <div class="course-info">
                    <h3><?php echo htmlspecialchars($course['title']); ?></h3>
                    <p><?php echo htmlspecialchars($course['description']); ?></p>
                    <div class="course-meta">
                        <span><i class="fas fa-chalkboard-teacher"></i> Instructor: <?php echo htmlspecialchars($course['instructor']); ?></span>
                        <span><i class="fas fa-calendar-alt"></i> Last updated: <?php echo date('M Y', strtotime($course['updated_at'])); ?></span>
                        <span><i class="fas fa-globe"></i> English</span>
                    </div>
                </div>
                <div class="course-actions">
                    <?php 
                    $nextLesson = getNextLesson($user_id, $course_id);
                    if ($nextLesson): 
                    ?>
                        <a href="lesson.php?id=<?php echo $nextLesson['lesson_id']; ?>" class="btn btn-primary">Continue Learning</a>
                    <?php else: ?>
                        <a href="#" class="btn btn-primary">Course Completed</a>
                    <?php endif; ?>
                    <a href="#" class="btn btn-outline">View Certificate</a>
                </div>
            </div>
            
            <div class="course-tabs">
                <button class="tab-btn active" data-tab="content">Content</button>
                <button class="tab-btn" data-tab="overview">Overview</button>
                <button class="tab-btn" data-tab="resources">Resources</button>
                <button class="tab-btn" data-tab="discussion">Discussion</button>
                <button class="tab-btn" data-tab="tests">Tests</button>
            </div>
            
            <div class="tab-content active" id="content">
                <div class="course-sections">
                    <?php if (!empty($lessons)): ?>
                    <div class="section">
                        <div class="section-header">
                            <h4>Course Lessons</h4>
                            <span><?php echo count($completedLessons); ?>/<?php echo count($lessons); ?> lessons completed</span>
                        </div>
                        <div class="lessons-list">
                            <?php foreach ($lessons as $lesson): 
                                $isCompleted = in_array($lesson['lesson_id'], $completedLessons);
                                $isCurrent = false; // You would implement logic to determine current lesson
                            ?>
                            <div class="lesson <?php echo $isCompleted ? 'completed' : ''; ?> <?php echo $isCurrent ? 'current' : ''; ?>">
                                <div class="lesson-info">
                                    <div class="lesson-status">
                                        <?php if ($isCompleted): ?>
                                            <i class="fas fa-check-circle"></i>
                                        <?php elseif ($isCurrent): ?>
                                            <i class="fas fa-play-circle"></i>
                                        <?php else: ?>
                                            <i class="far fa-circle"></i>
                                        <?php endif; ?>
                                    </div>
                                    <h5><?php echo htmlspecialchars($lesson['title']); ?></h5>
                                    <p><?php echo htmlspecialchars($lesson['description'] ?: 'No description available'); ?></p>
                                </div>
                                <div class="lesson-actions">
                                    <?php if ($isCompleted): ?>
                                        <a href="lesson.php?id=<?php echo $lesson['lesson_id']; ?>" class="btn btn-sm btn-outline">Review</a>
                                        <a href="https://www.youtube.com/watch?v=<?php echo $lesson['youtube_video_id']; ?>" target="_blank" class="btn btn-sm btn-primary">Watch Again</a>
                                    <?php elseif ($isCurrent): ?>
                                        <a href="lesson.php?id=<?php echo $lesson['lesson_id']; ?>" class="btn btn-sm btn-outline">Notes</a>
                                        <a href="lesson.php?id=<?php echo $lesson['lesson_id']; ?>" class="btn btn-sm btn-primary">Start Lesson</a>
                                    <?php else: ?>
                                        <span class="text-muted"><?php echo $lesson['duration']; ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="empty-state">
                        <p>No lessons available for this course yet.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="tab-content" id="overview">
                <div class="course-description">
                    <h4>Course Description</h4>
                    <p><?php echo htmlspecialchars($course['description']); ?></p>
                    
                    <h4>What You'll Learn</h4>
                    <ul class="learning-list">
                        <li><i class="fas fa-check"></i> Build responsive websites with HTML5 and CSS3</li>
                        <li><i class="fas fa-check"></i> Create interactive web applications with JavaScript</li>
                        <li><i class="fas fa-check"></i> Develop single-page applications with React</li>
                        <li><i class="fas fa-check"></i> Build RESTful APIs with Node.js and Express</li>
                        <li><i class="fas fa-check"></i> Work with databases like MongoDB</li>
                        <li><i class="fas fa-check"></i> Deploy applications to the cloud</li>
                    </ul>
                    
                    <h4>Requirements</h4>
                    <ul class="requirements-list">
                        <li><i class="fas fa-laptop"></i> A computer with internet access</li>
                        <li><i class="fas fa-code"></i> No prior programming experience needed</li>
                        <li><i class="fas fa-clock"></i> Dedication to complete the course</li>
                    </ul>
                </div>
            </div>
            
            <div class="tab-content" id="resources">
                <div class="resources-list">
                    <h4>Downloadable Resources</h4>
                    
                    <div class="resource-card">
                        <div class="resource-icon">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <div class="resource-info">
                            <h5>HTML & CSS Cheat Sheet</h5>
                            <p>PDF • 2.4 MB</p>
                        </div>
                        <div class="resource-actions">
                            <a href="#" class="btn btn-sm btn-outline">Download</a>
                        </div>
                    </div>
                    
                    <div class="resource-card">
                        <div class="resource-icon">
                            <i class="fas fa-file-code"></i>
                        </div>
                        <div class="resource-info">
                            <h5>Starter Project Files</h5>
                            <p>ZIP • 5.1 MB</p>
                        </div>
                        <div class="resource-actions">
                            <a href="#" class="btn btn-sm btn-outline">Download</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="tab-content" id="tests">
                <div class="tests-section">
                    <h4>Course Assessments</h4>
                    <p>Test your knowledge and earn certificates by completing these assessments.</p>
                    
                    <div class="test-card">
                        <div class="test-info">
                            <h5>HTML & CSS Fundamentals Quiz</h5>
                            <p>20 Questions • 30 Minutes</p>
                            <div class="test-progress">
                                <span>Your score: 85%</span>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 85%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="test-actions">
                            <a href="#" class="btn btn-primary">Retake Test</a>
                        </div>
                    </div>
                    
                    <div class="test-card locked">
                        <div class="test-info">
                            <h5>JavaScript Basics Test</h5>
                            <p>25 Questions • 45 Minutes</p>
                            <div class="test-progress">
                                <span>Locked - Complete previous sections</span>
                            </div>
                        </div>
                        <div class="test-actions">
                            <button class="btn btn-disabled" disabled>Start Test</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="js/course.js"></script>
    <script src="js/script.js"></script>
</body>
</html>