<?php
require_once 'config.php';
require_once 'course_functions.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if lesson ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$lesson_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Get lesson details with course information
$stmt = $conn->prepare("SELECT l.*, c.course_id, c.title as course_title, c.youtube_playlist_id, 
                        c.instructor as course_instructor, c.thumbnail_url as course_thumbnail
                        FROM lessons l
                        JOIN courses c ON l.course_id = c.course_id
                        WHERE l.lesson_id = ?");
$stmt->bind_param("i", $lesson_id);
$stmt->execute();
$result = $stmt->get_result();

// Redirect if lesson doesn't exist
if ($result->num_rows !== 1) {
    $_SESSION['error'] = "Lesson not found";
    header("Location: dashboard.php");
    exit();
}

$lesson = $result->fetch_assoc();
$course_id = $lesson['course_id'];

// Check if user is enrolled in this course
if (!isUserEnrolled($user_id, $course_id)) {
    $_SESSION['error'] = "You are not enrolled in this course";
    header("Location: dashboard.php");
    exit();
}

// Mark lesson as completed if not in review mode
if (!isset($_GET['review'])) {
    markLessonCompleted($user_id, $lesson_id);
}

// Get navigation lessons (previous and next)
$navigation = [
    'previous' => getAdjacentLesson($course_id, $lesson['position'], 'previous'),
    'next' => getAdjacentLesson($course_id, $lesson['position'], 'next')
];

// Get course completion percentage
$completion = getCourseCompletionPercentage($user_id, $course_id);

// Function to get adjacent lessons
function getAdjacentLesson($course_id, $current_position, $direction = 'next') {
    global $conn;
    
    $operator = $direction === 'next' ? '>' : '<';
    $order = $direction === 'next' ? 'ASC' : 'DESC';
    
    $stmt = $conn->prepare("SELECT lesson_id, title FROM lessons 
                           WHERE course_id = ? AND position $operator ? 
                           ORDER BY position $order LIMIT 1");
    $stmt->bind_param("ii", $course_id, $current_position);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->num_rows > 0 ? $result->fetch_assoc() : null;
}

// Handle note submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_note'])) {
    $note_content = sanitizeInput($_POST['note_content']);
    
    // Save note to database (implementation depends on your notes table structure)
    $stmt = $conn->prepare("INSERT INTO lesson_notes (user_id, lesson_id, content) 
                           VALUES (?, ?, ?) 
                           ON DUPLICATE KEY UPDATE content = ?");
    $stmt->bind_param("iiss", $user_id, $lesson_id, $note_content, $note_content);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Note saved successfully";
    } else {
        $_SESSION['error'] = "Failed to save note";
    }
    
    // Redirect to avoid form resubmission
    header("Location: lesson.php?id=$lesson_id");
    exit();
}

// Get existing note if available
$user_note = '';
$stmt = $conn->prepare("SELECT content FROM lesson_notes WHERE user_id = ? AND lesson_id = ?");
$stmt->bind_param("ii", $user_id, $lesson_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user_note = $result->fetch_assoc()['content'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($lesson['title']); ?> - Skill India</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/lesson.css">
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
                <h2>
                    <a href="course.php?id=<?php echo $course_id; ?>" class="course-link">
                        <?php echo htmlspecialchars($lesson['course_title']); ?>
                    </a>
                </h2>
            </div>
            
            <div class="topbar-right">
                <div class="course-progress">
                    <span><?php echo $completion; ?>% Complete</span>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo $completion; ?>%"></div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Lesson Content -->
        <main class="lesson-content">
            <!-- Status Messages -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            
            <!-- Lesson Header -->
            <div class="lesson-header">
                <div class="breadcrumb">
                    <a href="dashboard.php">Dashboard</a> &gt;
                    <a href="course.php?id=<?php echo $course_id; ?>"><?php echo htmlspecialchars($lesson['course_title']); ?></a> &gt;
                    <span><?php echo htmlspecialchars($lesson['title']); ?></span>
                </div>
                
                <h1><?php echo htmlspecialchars($lesson['title']); ?></h1>
                
                <div class="lesson-meta">
                    <span class="instructor">
                        <i class="fas fa-chalkboard-teacher"></i> 
                        <?php echo htmlspecialchars($lesson['course_instructor']); ?>
                    </span>
                    <span class="duration">
                        <i class="fas fa-clock"></i> 
                        <?php echo htmlspecialchars($lesson['duration']); ?>
                    </span>
                    <span class="status">
                        <i class="fas fa-check-circle"></i> 
                        <?php echo isset($_GET['review']) ? 'Reviewing' : 'Completed'; ?>
                    </span>
                </div>
            </div>
            
            <!-- Video Player -->
            <div class="video-container">
                <div class="video-wrapper">
                    <iframe width="100%" height="500" 
                            src="https://www.youtube.com/embed/<?php echo htmlspecialchars($lesson['youtube_video_id']); ?>?rel=0&modestbranding=1" 
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                            allowfullscreen></iframe>
                </div>
                
                <div class="video-actions">
                    <button class="btn btn-outline" id="toggle-fullscreen">
                        <i class="fas fa-expand"></i> Fullscreen
                    </button>
                    <a href="https://youtu.be/<?php echo htmlspecialchars($lesson['youtube_video_id']); ?>" 
                       target="_blank" 
                       class="btn btn-outline">
                        <i class="fab fa-youtube"></i> Watch on YouTube
                    </a>
                </div>
            </div>
            
            <!-- Lesson Description -->
            <div class="lesson-description">
                <h3>About This Lesson</h3>
                <div class="description-content">
                    <?php echo nl2br(htmlspecialchars($lesson['description'] ?: 'No description available for this lesson.')); ?>
                </div>
            </div>
            
            <!-- Lesson Navigation -->
            <div class="lesson-navigation">
                <?php if ($navigation['previous']): ?>
                    <a href="lesson.php?id=<?php echo $navigation['previous']['lesson_id']; ?>" 
                       class="btn btn-outline prev-lesson">
                        <i class="fas fa-arrow-left"></i> 
                        <span class="nav-text">Previous: <?php echo htmlspecialchars($navigation['previous']['title']); ?></span>
                    </a>
                <?php else: ?>
                    <span class="btn btn-outline disabled">
                        <i class="fas fa-arrow-left"></i> No Previous Lesson
                    </span>
                <?php endif; ?>
                
                <?php if ($navigation['next']): ?>
                    <a href="lesson.php?id=<?php echo $navigation['next']['lesson_id']; ?>" 
                       class="btn btn-primary next-lesson">
                        <span class="nav-text">Next: <?php echo htmlspecialchars($navigation['next']['title']); ?></span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                <?php else: ?>
                    <a href="course.php?id=<?php echo $course_id; ?>" 
                       class="btn btn-primary">
                        Back to Course Overview
                        <i class="fas fa-arrow-right"></i>
                    </a>
                <?php endif; ?>
            </div>
            
            <!-- Lesson Notes -->
            <div class="lesson-notes">
                <h3>My Notes</h3>
                <form method="POST" action="lesson.php?id=<?php echo $lesson_id; ?>">
                    <textarea name="note_content" placeholder="Write your notes here..."><?php echo htmlspecialchars($user_note); ?></textarea>
                    <button type="submit" name="save_note" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Notes
                    </button>
                </form>
            </div>
            
            <!-- Course Resources -->
            <div class="lesson-resources">
                <h3>Resources for This Lesson</h3>
                <div class="resources-grid">
                    <div class="resource-card">
                        <div class="resource-icon">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <div class="resource-info">
                            <h4>Lesson Slides</h4>
                            <p>PDF • 2.1 MB</p>
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
                            <h4>Code Examples</h4>
                            <p>ZIP • 1.5 MB</p>
                        </div>
                        <div class="resource-actions">
                            <a href="#" class="btn btn-sm btn-outline">Download</a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="js/lesson.js"></script>
    <script src="js/script.js"></script>
</body>
</html>