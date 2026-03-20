<?php
header('Content-Type: application/json');
require_once 'db_mongo.php';
require_once 'db_redis.php';

// Extract token from Authorization header
$headers = function_exists('apache_request_headers') ? apache_request_headers() : [];
$authHeader = $headers['Authorization'] ?? ($_SERVER['HTTP_AUTHORIZATION'] ?? '');
$token = '';
if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    $token = $matches[1];
}

if (!$token) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized: No token provided']);
    exit;
}

// Validate token with Redis
try {
    $userId = $redis->get('session:' . $token);
    if (!$userId) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Unauthorized: Invalid or expired token']);
        exit;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Session service error: ' . $e->getMessage()]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Read Profile from MongoDB
    try {
        $profile = $usersCollection->findOne(['user_id' => (int)$userId]);
        $data = $profile ? (array)$profile : [];
        if(isset($data['_id'])) {
            unset($data['_id']);
        }
        if(isset($data['user_id'])) {
            unset($data['user_id']);
        }
        echo json_encode(['success' => true, 'data' => $data]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to fetch profile: ' . $e->getMessage()]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update/Create Profile in MongoDB
    $profileData = [
        'fullName' => trim($_POST['fullName'] ?? ''),
        'age' => (int)($_POST['age'] ?? 0),
        'dob' => trim($_POST['dob'] ?? ''),
        'contact' => trim($_POST['contact'] ?? ''),
        'address' => trim($_POST['address'] ?? ''),
        'bio' => trim($_POST['bio'] ?? '')
    ];

    try {
        $usersCollection->updateOne(
            ['user_id' => (int)$userId],
            ['$set' => $profileData],
            ['upsert' => true]
        );
        echo json_encode(['success' => true, 'message' => 'Profile updated']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to update profile: ' . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>
