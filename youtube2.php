<?php
class YouTube2API {
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
        $response = $this->makeApiRequest($url);
        return json_decode($response, true);
    }

    public function getPlaylistVideos($playlistId) {
        $videos = [];
        $nextPageToken = '';
        
        do {
            $url = $this->baseUrl . '/playlistItems?part=snippet,contentDetails&playlistId=' . $playlistId . 
                   '&maxResults=50&key=' . $this->apiKey . '&pageToken=' . $nextPageToken;
            
            $response = $this->makeApiRequest($url);
            $data = json_decode($response, true);
            
            if (!isset($data['items'])) {
                throw new Exception('Failed to fetch playlist items: ' . ($data['error']['message'] ?? 'Unknown error'));
            }
            
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
            
            $nextPageToken = $data['nextPageToken'] ?? '';
        } while ($nextPageToken);
        
        return $videos;
    }

    public function getVideoDetails($videoId) {
        $url = $this->baseUrl . '/videos?part=snippet,contentDetails,statistics&id=' . $videoId . '&key=' . $this->apiKey;
        $response = $this->makeApiRequest($url);
        $data = json_decode($response, true);
        
        if (isset($data['items'][0])) {
            $video = $data['items'][0];
            return [
                'title' => $video['snippet']['title'],
                'description' => $video['snippet']['description'],
                'duration' => $this->formatDuration($video['contentDetails']['duration']),
                'views' => $video['statistics']['viewCount'] ?? 0
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
        try {
            $interval = new DateInterval($duration);
            $hours = $interval->h;
            $minutes = $interval->i;
            $seconds = $interval->s;
            
            if ($hours > 0) {
                return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
            }
            return sprintf('%02d:%02d', $minutes, $seconds);
        } catch (Exception $e) {
            return '00:00';
        }
    }

    public function extractPlaylistId($url) {
        $patterns = [
            '/^https?:\/\/(?:www\.)?youtube\.com\/playlist\?list=([^&]+)/',
            '/^https?:\/\/(?:www\.)?youtube\.com\/watch\?.*v=([^&]+).*&list=([^&]+)/',
            '/^https?:\/\/youtu\.be\/([^?]+)\?.*list=([^&]+)/'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return end($matches); // Return the last match which should be the playlist ID
            }
        }
        
        return null;
    }

    private function makeApiRequest($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            throw new Exception('cURL error: ' . curl_error($ch));
        }
        
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode !== 200) {
            $error = json_decode($response, true);
            throw new Exception('API request failed with HTTP code ' . $httpCode . ': ' . 
                               ($error['error']['message'] ?? 'Unknown error'));
        }
        
        curl_close($ch);
        return $response;
    }
}?>