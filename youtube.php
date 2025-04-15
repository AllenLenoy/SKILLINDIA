<?php
class YouTubeAPI {
    private $apiKey;
    private $baseUrl = 'https://www.googleapis.com/youtube/v3';

    public function __construct() {
        // Load environment variables
        $envFile = __DIR__ . '/.env';
        if (file_exists($envFile)) {
            $env = parse_ini_file($envFile);
            $this->apiKey = $env['YOUTUBE_API_KEY'] ?? null;
        }

        if (!$this->apiKey) {
            $this->apiKey = getenv('YOUTUBE_API_KEY');
        }

        if (!$this->apiKey) {
            throw new Exception('YouTube API key not found in environment variables');
        }
    }

    public function getPlaylistDetails($playlistId) {
        $url = $this->baseUrl . '/playlists?part=snippet,contentDetails&id=' . $playlistId . '&key=' . $this->apiKey;
        $response = file_get_contents($url);
        return json_decode($response, true);
    }

    public function getPlaylistVideos($playlistId) {
        $videos = [];
        $nextPageToken = '';
        
        do {
            $url = $this->baseUrl . '/playlistItems?part=snippet,contentDetails&playlistId=' . $playlistId . 
                   '&maxResults=50&key=' . $this->apiKey . '&pageToken=' . $nextPageToken;
            
            $response = file_get_contents($url);
            $data = json_decode($response, true);
            
            if (isset($data['items'])) {
                foreach ($data['items'] as $item) {
                    $videoId = $item['contentDetails']['videoId'];
                    $videoDetails = $this->getVideoDetails($videoId);
                    
                    $videos[] = [
                        'video_id' => $videoId,
                        'title' => $item['snippet']['title'],
                        'description' => $item['snippet']['description'],
                        'position' => $item['snippet']['position'],
                        'duration' => $videoDetails['duration'] ?? '00:00'
                    ];
                }
            }
            
            $nextPageToken = $data['nextPageToken'] ?? '';
        } while ($nextPageToken);
        
        return $videos;
    }

    public function getVideoDetails($videoId) {
        $url = $this->baseUrl . '/videos?part=snippet,contentDetails,statistics&id=' . $videoId . '&key=' . $this->apiKey;
        $response = file_get_contents($url);
        $data = json_decode($response, true);
        
        if (isset($data['items'][0])) {
            $video = $data['items'][0];
            return [
                'title' => $video['snippet']['title'],
                'description' => $video['snippet']['description'],
                'duration' => $this->formatDuration($video['contentDetails']['duration']),
                'views' => $video['statistics']['viewCount']
            ];
        }
        
        return [
            'title' => 'Video Title Unavailable',
            'description' => 'Description Unavailable',
            'duration' => '00:00',
            'views' => 0
        ];
    }

    private function formatDuration($duration) {
        $interval = new DateInterval($duration);
        $hours = $interval->h;
        $minutes = $interval->i;
        $seconds = $interval->s;
        
        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }
        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    public function extractPlaylistId($url) {
        $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/';
        if (preg_match($pattern, $url, $matches)) {
            return $matches[1];
        }
        return null;
    }
}