<?php
session_start();
require_once 'db.php';  // Include the database connection file

// Get the short code from the URL
$short_code = basename($_SERVER['REQUEST_URI']);  // Extract the last part of the URL

// Fetch the long URL from the database
$stmt = $pdo->prepare("SELECT long_url FROM urls WHERE short_url LIKE ?");
$stmt->execute(['%/' . $short_code]);
$entry = $stmt->fetch();

if ($entry) {
    // Redirect to the long URL
    header('Location: ' . $entry['long_url']);
    exit;
} else {
    // If the short code isn't found, show an error
    echo 'Short URL not found.';
}
?>
