<?php
require_once '../config.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$course_title = $data['title'] ?? '';
$course_description = $data['description'] ?? '';

// Log incoming data
error_log("Course Title: " . $course_title);
error_log("Course Description: " . $course_description);

$api_key = "AIzaSyDt3seXagdwpBxGqbZ4Gy25ZfgddEqGYig";
$url = "https://generativelanguage.googleapis.com/v1/models/gemini-1.5-flash-8b:generateContent?key=" . $api_key;

$prompt = "You are a quiz generator. Create a quiz with 10 multiple choice questions about {$course_title}.
Base the questions on this description: {$course_description}

Output format must be valid JSON like this:
{
    \"questions\": [
        {
            \"question\": \"What is the primary purpose of {$course_title}?\",
            \"options\": [
                \"First option\",
                \"Second option\",
                \"Third option\",
                \"Fourth option\"
            ],
            \"correct_answer\": 0
        }
    ]
}

Important: Return ONLY the JSON object, no other text. Ensure all questions have 4 options and correct_answer is 0-3.";

$payload = [
    'contents' => [
        [
            'parts' => [
                ['text' => $prompt]
            ]
        ]
    ],
    'generationConfig' => [
        'temperature' => 0.7,
        'topK' => 40,
        'topP' => 0.95
    ]
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);

// Log the request
error_log("API Request: " . json_encode($payload));

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

// Log the response
error_log("HTTP Code: " . $http_code);
error_log("Curl Error: " . $curl_error);
error_log("API Response: " . $response);

if ($response === false) {
    echo json_encode([
        'success' => false,
        'error' => 'API Connection failed: ' . $curl_error
    ]);
    exit;
}

$data = json_decode($response, true);

if (!$data) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid JSON response: ' . json_last_error_msg()
    ]);
    exit;
}

if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
    $response_text = $data['candidates'][0]['content']['parts'][0]['text'];
    error_log("Generated Text: " . $response_text);
    
    // Remove JSON code block markers if present
    $response_text = preg_replace('/```(?:json|JSON)?\s*(.*?)\s*```/s', '$1', $response_text);
    
    // Try to parse the response as JSON
    $questions_data = json_decode($response_text, true);
    
    if ($questions_data && isset($questions_data['questions'])) {
        echo json_encode([
            'success' => true,
            'questions' => $questions_data['questions']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Invalid question format in response',
            'raw_response' => $response_text // For debugging
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'error' => 'No content in API response',
        'response' => $data
    ]);
}