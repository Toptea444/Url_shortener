<?php
$host = 'localhost';
$dbname = 'url_shortener_db';
$user = 'root'; // Replace with your DB username
$pass = '';     // Replace with your DB password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
