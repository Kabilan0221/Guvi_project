<?php
header('Content-Type: application/json');
require_once 'db_redis.php';

$headers = function_exists('apache_request_headers') ? apache_request_headers() : [];
$authHeader = $headers['Authorization'] ?? ($_SERVER['HTTP_AUTHORIZATION'] ?? '');
$token = '';
if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    $token = $matches[1];
}

if ($token) {
    try {
        $redis->del('session:' . $token);
    } catch (Exception $e) {
        // Ignore errors on logout
    }
}

echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
?>
