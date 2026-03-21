<?php
header('Content-Type: application/json');
require_once 'db_mongo.php';
require_once 'db_redis.php';

// Get token - works with PHP CLI server
$token = '';
$authHeader = '';

// Method 1: getallheaders()
if (function_exists('getallheaders')) {
    $allHeaders = getallheaders();
    foreach ($allHeaders as $key => $value) {
        if (strtolower($key) === 'authorization') {
            $authHeader = $value;
            break;
        }
    }
}

// Method 2: $_SERVER fallback
if (empty($authHeader)) {
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] 
        ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] 
        ?? '';
}

if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    $token = $matches[1];
}

if (!$token) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No token provided', 'debug' => 'AUTH:' . $authHeader]);
    exit;
}

// Validate token with Redis
try {
    $userId = $redis->get('session:' . $token);
    if (!$userId) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid or expired token']);
        exit;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Session error: ' . $e->getMessage()]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $profile = $usersCollection->findOne(['user_id' => (int)$userId]);
        $data = $profile ? (array)$profile : [];
        unset($data['_id'], $data['user_id']);
        echo json_encode(['success' => true, 'data' => $data]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Fetch error: ' . $e->getMessage()]);
    }

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $profileData = [
        'user_id'  => (int)$userId,
        'fullName' => trim($_POST['fullName'] ?? ''),
        'age'      => (int)($_POST['age'] ?? 0),
        'dob'      => trim($_POST['dob'] ?? ''),
        'contact'  => trim($_POST['contact'] ?? ''),
        'address'  => trim($_POST['address'] ?? ''),
        'bio'      => trim($_POST['bio'] ?? '')
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
        echo json_encode(['success' => false, 'message' => 'Update error: ' . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>