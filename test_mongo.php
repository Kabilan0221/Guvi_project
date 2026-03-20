<?php
require_once 'vendor/autoload.php';

$client = new MongoDB\Client("mongodb://127.0.0.1:27017");
$db = $client->auth_app;
$collection = $db->users;

$result = $collection->insertOne(['test' => 'PHP MongoDB Connected!']);
echo "✅ Connected! ID: " . $result->getInsertedId();
?>