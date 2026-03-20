<?php
header('Content-Type: application/json');
require_once 'db_mysql.php';
require_once 'db_redis.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing email or password']);
    exit;
}

// Check MySQL for user
$stmt = $pdo->prepare("SELECT id, password FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
    exit;
}

// Generate token
$token = bin2hex(random_bytes(32));

// Store in Redis (key: token, value: user_id), expire in 2 hours (7200 seconds)
try {
    $redis->setex('session:' . $token, 7200, $user['id']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to create session storage']);
    exit;
}

echo json_encode([
    'success' => true,
    'message' => 'Login successful',
    'token' => $token
]);
?>
