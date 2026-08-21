<?php
echo "=== Testing Login ===\n\n";

$ch = curl_init('http://localhost:8000/api/auth/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'email' => 'admin@bitchest.com',
    'password' => 'admin123'
]));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: $httpCode\n";
$data = json_decode($response, true);

echo "Response structure:\n";
echo "- status: {$data['status']}\n";
echo "- data.token exists: " . (isset($data['data']['token']) ? 'YES' : 'NO') . "\n";
echo "- data.user exists: " . (isset($data['data']['user']) ? 'YES' : 'NO') . "\n";

if (isset($data['data']['token']) && isset($data['data']['user'])) {
    echo "\n✅ LOGIN WORKS\n";
    echo "Token: " . substr($data['data']['token'], 0, 20) . "...\n";
    echo "User: {$data['data']['user']['email']}\n";
} else {
    echo "\n❌ LOGIN FAILED\n";
    echo "Full response:\n";
    print_r($data);
}
