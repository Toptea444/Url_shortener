<?php/*
require 'db.php';

// Handle Ajax requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'shorten') {
        $long_url = trim($_POST['long_url']);
        $short_code = substr(md5(uniqid()), 0, 6);

        try {
            $stmt = $pdo->prepare("INSERT INTO urls (long_url, short_code) VALUES (:long_url, :short_code)");
            $stmt->execute(['long_url' => $long_url, 'short_code' => $short_code]);

            $short_url = "http://localhost:8080/url_shortener/process.php?code=" . $short_code;
            echo "Shortened URL: <a href='$short_url' target='_blank'>$short_url</a>";
        } catch (PDOException $e) {
            echo "Error: Could not shorten the URL.";
        }
    }
} elseif (isset($_GET['code'])) {
    // Handle redirection
    $short_code = $_GET['code'];

    try {
        $stmt = $pdo->prepare("SELECT long_url FROM urls WHERE short_code = :short_code LIMIT 1");
        $stmt->execute(['short_code' => $short_code]);
        $url = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($url) {
            header("Location: " . $url['long_url']);
            exit;
        } else {
            echo "Invalid short URL.";
        }
    } catch (PDOException $e) {
        echo "Error: Could not redirect.";
    }
}*/

?>
<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $long_url = trim($_POST['long_url']);
    $short_code = substr(md5(uniqid()), 0, 6); // Generate a unique short code

    try {
        // Insert the long URL and short code into the database
        $stmt = $pdo->prepare("INSERT INTO urls (long_url, short_code) VALUES (:long_url, :short_code)");
        $stmt->execute(['long_url' => $long_url, 'short_code' => $short_code]);

        // Generate the localhost-based short URL
        $short_url = "http://localhost:8080/url_shortener/redirect.php/" . $short_code;


        // Return the shortened URL as an anchor tag
        echo "Shortened URL: <a href='$short_url' target='_blank'>$short_url</a>";
    } catch (PDOException $e) {
        echo "Error: Could not shorten the URL.";
    }
}
?>
