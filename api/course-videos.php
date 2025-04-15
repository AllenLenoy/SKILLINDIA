<?php
require_once '../config.php';
require_once '../youtube.php';

header('Content-Type: application/json');

if (!isset($_GET['course_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required parameters'
    ]);
    exit;
}

$course_id = $_GET['course_id'];
// $user_id = $_GET['user_id'];

function getPlaylistVideos($playlistUrl, $apiKey, $start, $end) {
    try {
        // Extract playlist ID from URL
        if (preg_match('/[?&]list=([^&]+)/', $playlistUrl, $matches)) {
            $playlistId = $matches[1];
        } else {
            throw new Exception('Invalid YouTube playlist URL');
        }

        error_log("Fetching videos for playlist: " . $playlistId);

        // Step 1: Get playlist video IDs
        $url = "https://www.googleapis.com/youtube/v3/playlistItems?part=contentDetails&maxResults=100&playlistId={$playlistId}&key={$apiKey}";
        $response = file_get_contents($url);

        if ($response === false) {
            throw new Exception('Failed to fetch playlist data from YouTube');
        }

        $data = json_decode($response, true);

        // Step 2: Extract video IDs
        $videoIds = [];
        if (isset($data['items'])) {
            foreach ($data['items'] as $item) {
                if (isset($item['contentDetails']['videoId'])) {
                    $videoIds[] = $item['contentDetails']['videoId'];
                }
            }
        }

        // Step 3: Fetch details for each video
        $videosJson = [];
        $count = 1;

        foreach ($videoIds as $videoId) {
            if ($count < $start) {
                $count++;
                continue;
            }
            if($count > $end){
                break;
            }
            $videoUrl = "https://www.googleapis.com/youtube/v3/videos?part=snippet,contentDetails&id=$videoId&key=$apiKey";
            $videoResponse = file_get_contents($videoUrl);
            $videoData = json_decode($videoResponse, true);

            if (!empty($videoData['items'])) {
                $video = $videoData['items'][0];

                $title = $video['snippet']['title'];

                // Convert ISO 8601 duration to minutes
                $durationISO = $video['contentDetails']['duration'];
                $interval = new DateInterval($durationISO);
                $durationMinutes = ($interval->h * 60) + $interval->i + ($interval->s / 60);

                $thumbnail = $video['snippet']['thumbnails']['default']['url'];
                $videoLink = "https://www.youtube.com/watch?v=$videoId";

                $videosJson["video$count"] = [
                    "title" => $title,
                    "duration_minutes" => round($durationMinutes, 2),
                    "thumbnail" => $thumbnail,
                    "url" => $videoLink
                ];

                $count++;
            }
        }

        return [
            'success' => true,
            'videos' => $videosJson,
            'total_videos' => count($videosJson)
        ];
    } catch (Exception $e) {
        error_log("YouTube API Error: " . $e->getMessage());
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

try {
    if (!isset($_GET['course_id'])) {
        throw new Exception('Course ID is required');
    }

    // Debug log
    error_log("Processing request for course ID: " . $_GET['course_id']);

    // Get course details from database
    $stmt = $pdo->prepare("SELECT course_id, title, playlist_link FROM courses WHERE course_id = ?");
    $stmt->execute([$_GET['course_id']]);
    $course = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Debug log
    error_log("Course details: " . print_r($course, true));

    if (!$course) {
        throw new Exception('Course not found');
    }

    $result = getPlaylistVideos($course['playlist_link'], 'AIzaSyBaWtPLV5GpuPOE44zGsZ7No_T5aYharDc', $_GET['start'], $_GET['end']);
    
    // Debug log
    error_log("API result: " . print_r($result, true));

    if ($result['success']) {
        $response = [
            'success' => true,
            'course' => [
                'id' => $course['id'],
                'title' => $course['title']
            ],
            'videos' => $result['videos'],
            'total_videos' => $result['total_videos']
        ];
        
        ob_end_clean();
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    } else {
        throw new Exception($result['error']);
    }
} catch (Exception $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}



?>