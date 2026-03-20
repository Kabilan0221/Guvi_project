<?php
require_once __DIR__ . '/../vendor/autoload.php';

try {
    $mongoUrl = getenv('MONGO_URL') ?: 'mongodb://127.0.0.1:27017';
    $mongoClient = new MongoDB\Client($mongoUrl);
    $mongoDb = $mongoClient->auth_app;
    $usersCollection = $mongoDb->users;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'MongoDB connection failed: ' . $e->getMessage()]);
    exit;
}
?>