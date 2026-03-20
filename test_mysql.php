<?php
require_once 'php/db_mysql.php';

echo "✅ MySQL Connected Successfully!";

// Test create table if not exists
$pdo->exec("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

echo "<br>✅ Users table ready!";
?>
