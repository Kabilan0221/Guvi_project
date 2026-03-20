<?php
require_once 'php/db_redis.php';

// Test set and get
$redis->set('test_key', 'Redis is Working!');
$value = $redis->get('test_key');

echo "✅ Redis Connected Successfully!";
echo "<br>✅ Test Value: " . $value;

// Clean up
$redis->del('test_key');
?>