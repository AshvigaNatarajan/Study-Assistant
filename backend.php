<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: text/plain');

$apiKey = "Your_API_Key";  // Replace with your key

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $type = $_POST['type'] ?? '';
    $text = $_POST['text'] ?? '';

    if (!$text) {
        echo "Error: No text provided.";
        exit;
    }

    switch ($type) {
        case 'summary':
            $prompt = "Summarize this content clearly and briefly:\n\n$text";
            break;
        case 'quiz':
            $prompt = "Generate 5 multiple choice quiz questions with 4 options each and show answers at the end based on this content:\n\n$text";
            break;
        default:
            echo "Error: Unknown type.";
            exit;
    }

    $response = callGeminiAPI($prompt, $apiKey);
    echo $response;
}

function callGeminiAPI($prompt, $apiKey) {
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $apiKey;

    $postData = [
        "contents" => [
            ["parts" => [["text" => $prompt]]]
        ]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));

    $result = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($result, true);
    if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
        return $data['candidates'][0]['content']['parts'][0]['text'];
    } else {
        return "Error: " . json_encode($data);
    }
}
?>


