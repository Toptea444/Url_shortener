<?php
session_start();
require_once 'db.php';  // Include the database connection file

// Check if the user has a session and if not, create one
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = uniqid('user_', true);  // Assign a unique user ID for the session
}

// Function to generate a short URL code (6 characters)
function generateShortCode() {
    return substr(md5(uniqid(rand(), true)), 0, 6);  // Generate a 6-character hash
}

// Handle form submission for URL shortening
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['url'])) {
    $long_url = $_POST['url'];
    $short_code = generateShortCode();  // Generate a unique short code for the URL
    $short_url = 'http://localhost:8080/url_shortener/redirect.php/' . $short_code;

    // Save the URL in the database
    $stmt = $pdo->prepare("INSERT INTO urls (user_id, long_url, short_url) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $long_url, $short_url]);

    // Return the shortened URL as a JSON response
    echo json_encode([
        'short_url' => $short_url
    ]);
    exit;
}

// Fetch the user's history from the database
$stmt = $pdo->prepare("SELECT long_url, short_url, created_at FROM urls WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$history = $stmt->fetchAll();

// Display user's history

$history_html = '<div style="max-height:400px; overflow-y auto">';
$history_html .= '<table id="history-table" class="table table-striped table-bordered table-responsive">';
$history_html .= '<thead>';
$history_html .= '<tr>';
$history_html .= '<th>Original URL</th>';
$history_html .= '<th>Short URL</th>';
$history_html .= '<th>Date</th>';
$history_html .= '</tr>';
$history_html .= '</thead>';
$history_html .= '<tbody id="history">'; // Updated to add the ID here


if ($history) {
foreach ($history as $entry) {
    $history_html .= '<tr>';
    $history_html .= '<td class="text-wrap">' . $entry['long_url'] . '</td>';
    $history_html .= '<td id="history"><a href="' . $entry['short_url'] . '" target="_blank">' . $entry['short_url'] . '</a></td>';
    $history_html .= '<td style="width:1000px">' .$entry["created_at"]. '</td>';
    $history_html .= '</tr>';
}

$history_html .= '</tbody>';
$history_html .= '</table>';
$history_html .= '</div>';

} else {
    $history_html = 'No history found.';
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <head>
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

  <title>Url Shortener</title>
  
  
  <style>
@import url('https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap');

.ubuntu-light {
  font-family: "Ubuntu", sans-serif;
  font-weight: 300;
  font-style: normal;
}

.ubuntu-regular {
  font-family: "Ubuntu", sans-serif;
  font-weight: 400;
  font-style: normal;
}

.ubuntu-medium {
  font-family: "Ubuntu", sans-serif;
  font-weight: 500;
  font-style: normal;
}

.ubuntu-bold {
  font-family: "Ubuntu", sans-serif;
  font-weight: 700;
  font-style: normal;
}

.ubuntu-light-italic {
  font-family: "Ubuntu", sans-serif;
  font-weight: 300;
  font-style: italic;
}

.ubuntu-regular-italic {
  font-family: "Ubuntu", sans-serif;
  font-weight: 400;
  font-style: italic;
}

.ubuntu-medium-italic {
  font-family: "Ubuntu", sans-serif;
  font-weight: 500;
  font-style: italic;
}

.ubuntu-bold-italic {
  font-family: "Ubuntu", sans-serif;
  font-weight: 700;
  font-style: italic;
}

label{
  font-size: 15px;
}

button{
  font-size: 15px !important;
}

.wrap-list {
  max-width: 300px;  /* Set the desired width */
  word-wrap: break-word;  /* Allow words to break and wrap to the next line */
  padding: 0;  /* Optionally remove padding if needed */
}

td{
  font-family: verdana;
  font-size: 15px;
  font-weight: 400;
}

</style>
</head>
<body>
  
  
  <div style="margin-top:70px" class="container text-center">
    <h1 class="ubuntu-bold">Mini Url  Shortener</h1>
  </div>

  
  <div style="margin-top:40px" class="container">
    
      <!-- HTML form to submit a URL -->
      
      
     <form id="urlForm" method="POST" class="">
      <label class="ubuntu-regular">Enter url to shorten</label>
       <div class="text-ceter">
      <input type="url" name="url" id="urlInput" class="form-control" required>
     <button type="submit" class="btn w-100 btn-primary mt-2 ubuntu-medium" width="100%">Shorten URL</button>
     
     <p class="mt-3">Generated Url: <span id="generated-link">No URL generated yet.</span></p>

       </div>
    </form>

    <!-- Display the shortened URLs -->
    <div class="contaner text-wrap">
    <h6 class="mt-5 ubuntu-medium">Your History(Session based)</h6>
    <div class="table-responsive">
      <?php echo $history_html; ?>
    </div>
      
      
      
    </div>

  </div>
  
  
  
  








<!-- Include jQuery (to simplify AJAX) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>


  <script>
// Handle form submission via AJAX
$('#urlForm').submit(function(event) {
    event.preventDefault();  // Prevent the default form submission

    var url = $('#urlInput').val();  // Get the URL from the input field

    // AJAX request to shorten the URL
    $.ajax({
        url: 'index.php',
        type: 'POST',
        data: { url: url },
        dataType: 'json',
        success: function(response) {
            if (response.short_url) {
                // Update the generated link
                $('p.mt-3').html('Generated Url: <a href="' + response.short_url + '" target="_blank">' + response.short_url + '</a>');

                // Append the new entry to the history table
                $('#history-table tbody').append(
                    '<tr>' +
                        '<td class="text-wrap">' + url + '</td>' +
                        '<td><a href="' + response.short_url + '" target="_blank">' + response.short_url + '</a></td>' +
                        '<td>' + new Date().toLocaleString() + '</td>' +
                    '</tr>'
                );

                // Clear the input field after successful submission
                $('#urlInput').val('');
            } else {
                alert('Failed to shorten the URL.');
            }
        },
        error: function() {
            alert('There was an error shortening the URL.');
        }
    });
});
</script>

  
</body>
</html>
