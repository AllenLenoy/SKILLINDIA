<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once '../config/config.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Now you can access your environment variables
$dbHost = $_ENV['DB_HOST'];
$dbUser = $_ENV['DB_USER'];
$dbPass = $_ENV['DB_PASS'];

function getYoutubePlaylistDetails($playlistUrl) {
    // Load environment variables
    $envFile = '../.env';
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $_ENV[trim($key)] = trim($value);
            }
        }
    }

    $apiKey = $_ENV['YOUTUBE_API_KEY'];
    
    // Extract playlist ID from URL
    if (preg_match('/[?&]list=([^&]+)/', $playlistUrl, $matches)) {
        $playlistId = $matches[1];
    } else {
        throw new Exception('Invalid YouTube playlist URL');
    }

    // Get playlist items
    $url = "https://www.googleapis.com/youtube/v3/playlistItems?part=contentDetails&maxResults=50&playlistId={$playlistId}&key={$apiKey}";
    $response = file_get_contents($url);
    
    if ($response === false) {
        throw new Exception('Failed to fetch playlist data from YouTube');
    }

    $data = json_decode($response, true);
    if (isset($data['error'])) {
        throw new Exception('YouTube API Error: ' . $data['error']['message']);
    }

    return [
        'video_count' => $data['pageInfo']['totalResults'],
        'playlist_id' => $playlistId
    ];
}

// Handle AJAX request for adding course
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    // Get JSON input
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    // Validate playlist action
    if (isset($data['action']) && $data['action'] === 'validate_playlist') {
        try {
            if (empty($data['playlist_link'])) {
                throw new Exception('Playlist URL is required');
            }
            
            $playlistDetails = getYoutubePlaylistDetails($data['playlist_link']);
            echo json_encode([
                'success' => true,
                'video_count' => $playlistDetails['video_count']
            ]);
            exit;
        } catch (Exception $e) {
            error_log("Error validating playlist: " . $e->getMessage());
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
            exit;
        }
    }
    
    // Handle course addition
    if (isset($data['action']) && $data['action'] === 'add_course') {
        try {
            // Validate required fields
            $required_fields = ['title', 'description', 'duration', 'level', 'status', 'playlist_link'];
            foreach ($required_fields as $field) {
                if (empty($data[$field])) {
                    throw new Exception("Missing required field: $field");
                }
            }

            // Get YouTube playlist details
            $playlistDetails = getYoutubePlaylistDetails($data['playlist_link']);

            // Start transaction
            $pdo->beginTransaction();

            // Insert course
            $stmt = $pdo->prepare("
                INSERT INTO courses (
                    title, description, video_count, playlist_link,
                    duration, level, status, created_at, updated_at
                ) VALUES (
                    :title, :description, :video_count, :playlist_link,
                    :duration, :level, :status, NOW(), NOW()
                )
            ");

            $stmt->execute([
                'title' => $data['title'],
                'description' => $data['description'],
                'video_count' => $playlistDetails['video_count'],
                'playlist_link' => $data['playlist_link'],
                'duration' => $data['duration'],
                'level' => $data['level'],
                'status' => $data['status']
            ]);

            $courseId = $pdo->lastInsertId();
            
            $pdo->commit();
            echo json_encode([
                'success' => true, 
                'course_id' => $courseId,
                'video_count' => $playlistDetails['video_count']
            ]);
            exit;

        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log("Error adding course: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
            exit;
        }
    }
}

// Rest of the HTML code...
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Course - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* General Styles */
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
        }

        /* Content Area */
        .content {
            margin-left: 250px;
            padding: 20px;
            transition: margin-left 0.3s;
        }

        /* Form Styles */
        .form-container {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }

        .form-control {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            border-color: #007bff;
            outline: none;
        }

        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }

        /* Header */
        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            background: white;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .header-actions h1 {
            margin: 0;
            color: #333;
            font-size: 24px;
        }

        /* Button Styles */
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s ease;
        }

        .btn i {
            font-size: 14px;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        /* Alert Styles */
        #alertContainer {
            margin-bottom: 20px;
        }

        .alert {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 15px;
            border: 1px solid transparent;
        }

        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .content {
                margin-left: 0;
            }
            
            .header-actions {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
        }

        .youtube-preview {
            margin-top: 10px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 4px;
            display: none;
        }
        .youtube-preview.active {
            display: block;
        }
        .video-count {
            color: #28a745;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <?php include './sidebar.php'; ?>
    
    <div class="content">
        <div class="header-actions">
            <h1>Add New Course</h1>
            <a href="courses.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Courses
            </a>
        </div>

        <!-- Alert container for dynamic messages -->
        <div id="alertContainer"></div>

        <div class="form-container">
            <form id="addCourseForm">
                <div class="form-group">
                    <label for="title">Course Title</label>
                    <input type="text" id="title" name="title" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control" required></textarea>
                </div>

                <div class="form-group">
                    <label for="playlist_link">YouTube Playlist URL</label>
                    <input type="url" id="playlist_link" name="playlist_link" class="form-control" 
                           placeholder="https://www.youtube.com/playlist?list=..." required>
                    <div class="youtube-preview" id="youtubePreview">
                        <i class="fab fa-youtube"></i> 
                        Videos in playlist: <span class="video-count" id="videoCount">0</span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="duration">Duration</label>
                    <input type="text" id="duration" name="duration" class="form-control" placeholder="e.g., 2 hours" required>
                </div>

                <div class="form-group">
                    <label for="level">Level</label>
                    <select id="level" name="level" class="form-control" required>
                        <option value="beginner">Beginner</option>
                        <option value="intermediate">Intermediate</option>
                        <option value="advanced">Advanced</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Course
                </button>
            </form>
        </div>
    </div>

    <script>
        // Check if user is logged in and is admin
        document.addEventListener('DOMContentLoaded', function() {
            const user = JSON.parse(localStorage.getItem('userData'));
            if (!user || user.role !== 'admin') {
                window.location.href = '../login.php';
                return;
            }
        });
    </script>
    <script src="../js/auth.js"></script>
    <script src="../assets/js/admin/add-course.js"></script>
</body>
</html>