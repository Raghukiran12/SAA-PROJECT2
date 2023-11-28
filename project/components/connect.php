<?php
$host = 'database-3.ceixpj0ratbh.us-east-1.rds.amazonaws.com';
$dbname = 'course_db';
$username = 'admin';
$password = 'Ramithy123';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Other PDO configurations if needed
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    die();
}
?>