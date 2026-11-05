<?php
/**
 * Test Chatbot Product Recommendation
 */

$payload = [
    'message' => 'Muka saya banyak jerawat bruntusan dan komedo di hidung, ada rekomendasi skincare dari SkinCheck apa saja?',
    'history' => []
];

$ch = curl_init('http://127.0.0.1:5000/api/chat');
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    CURLOPT_TIMEOUT => 30
]);

$response = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: {$code}\n";
echo "Response:\n";
$data = json_decode($response, true);
if ($data && isset($data['reply'])) {
    file_put_contents(__DIR__ . '/last_response.txt', $data['reply']);
    echo "Saved to last_response.txt (" . strlen($data['reply']) . " bytes)\n";
    echo substr($data['reply'], 0, 500) . "...\n";
} else {
    echo $response . "\n";
}
