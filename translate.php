<?php
// translate.php
header('Content-Type: application/json');
if (!isset($_POST['text']) || !isset($_POST['target'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing parameters']);
    exit;
}

$text = $_POST['text'];
$target = $_POST['target'];
$apiKey = 'AIzaSyCkGaVu9NFNb6y_A-Y7LZkq5WXFJEMKaJc'; // TODO: Replace with your actual API key

$url = 'https://translation.googleapis.com/language/translate/v2';
$data = [
    'q' => $text,
    'target' => $target,
    'format' => 'text',
    'key' => $apiKey
];

$options = [
    'http' => [
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data),
    ],
];
$context  = stream_context_create($options);
$result = @file_get_contents($url, false, $context);

if ($result === FALSE) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to contact Google Translate API']);
    exit;
}

$response = json_decode($result, true);
if (isset($response['error'])) {
    http_response_code(500);
    echo json_encode(['error' => $response['error']['message']]);
    exit;
}

echo $result;
