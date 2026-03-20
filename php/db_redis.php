<?php
try {
    // Requires phpredis extension
    $redis = new Redis();
    $redis->connect('127.0.0.1', 6379);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Redis connection failed: ' . $e->getMessage()]);
    exit;
}
?>
