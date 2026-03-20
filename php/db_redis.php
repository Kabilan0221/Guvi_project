<?php
try {
    $redis = new Redis();
    $host = getenv('REDIS_HOST') ?: '127.0.0.1';
    $port = getenv('REDIS_PORT') ?: 6379;
    $redis->connect($host, $port);
    $pass = getenv('REDIS_PASSWORD');
    if ($pass) {
        $redis->auth($pass);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Redis connection failed: ' . $e->getMessage()]);
    exit;
}
?>