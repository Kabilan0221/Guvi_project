<?php
require_once __DIR__ . '/../vendor/autoload.php';

try {
    $mongoClient = new MongoDB\Client("mongodb://127.0.0.1:27017");
    $mongoDb = $mongoClient->auth_app;
    $usersCollection = $mongoDb->users;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'MongoDB connection failed: ' . $e->getMessage()]);
    exit;
}
?>