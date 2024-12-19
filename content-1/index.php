<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

require_once(dirname(dirname(__FILE__)) . '/includes/MySQLHandler.php');
require_once(dirname(dirname(__FILE__)) . '/config/config.php');

// Validate and sanitize user input
$requested_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Ensure user can only access their own profile
if (!$requested_id || $requested_id != $_SESSION['user_id']) {
    // Redirect to user's own profile or show an error
    header("Location: index.php?id=" . $_SESSION['user_id']);
    exit;
}

// Prepare statement to prevent SQL injection
$stmt = mysqli_prepare($db, "SELECT * FROM users WHERE idusers = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $requested_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Check if user exists
if (!$result) {
    die("Database query failed");
}
?>
<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width" />
  <title>User Profile</title>
  <link rel="stylesheet" href="../stylesheets/foundation.min.css">
  <link rel="stylesheet" href="../stylesheets/app.css">
  <link rel="icon" href="../favicon.ico" type="image/x-icon">
  <script src="../javascripts/modernizr.foundation.js"></script>
  <!--[if lt IE 9]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
  <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
</head>
<body>
<div class="row">
  <div class="four columns centered">
    <br/><br/><a href="../index.php"><img src="../images/bricks.jpg" /></a><p>
    <fieldset>
      <legend>Details</legend>
        <?php
        // Fetch and display user details
        if ($content = mysqli_fetch_array($result)) {
            // Escape output to prevent XSS
            echo '<br/>User ID: <b>'. htmlspecialchars($content['idusers']) .'</b><br/><br/>';
            echo 'User name: <b>'. htmlspecialchars($content['name']) .'</b><br/><br/>';
            echo 'E-mail: <b>'. htmlspecialchars($content['email']) .'</b><br/><br/>';
        } else {
            echo 'Error! User does not exist';
        }
        ?><br/>
    </fieldset></p><br/>
  </div><br/><br/><br/>
</div>  
  <script src="../javascripts/jquery.js"></script>
  <script src="../javascripts/foundation.min.js"></script>  
  <script src="../javascripts/app.js"></script>  
</body>
</html>