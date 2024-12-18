<?php
require_once(dirname(dirname(__FILE__)) . '/includes/MySQLHandler.php');    
require_once(dirname(dirname(__FILE__)) . '/config/config.php');    

// Improved error handling and security
$sSuccessMsg = "";

// Use prepared statements to prevent SQL injection
if(isset($_POST['submit'])) {
    // Start session if not already started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Sanitize and validate input
    $username = filter_input(INPUT_POST, 'username');
    $pwd = $_POST['passwd'];

    // Use prepared statement to prevent SQL injection
    $stmt = mysqli_prepare($db, "SELECT * FROM users WHERE name = ? AND password = ?");
    
    // Hash the password before comparison (use password_hash() for new passwords)
    $hashed_pwd = $pwd;
    
    mysqli_stmt_bind_param($stmt, "ss", $username, $hashed_pwd);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);
        
        // Set secure session variables
        $_SESSION['login'] = true;
        $_SESSION['user_id'] = $user['idusers'];
        $_SESSION['username'] = $user['name'];

        // Use secure cookie settings
        $cookieParams = session_get_cookie_params();
        setcookie(
            "id", 
            $user['idusers'], 
            [
                'expires' => time() + 3600, // 1 hour
                'path' => '/',
                'domain' => $_SERVER['HTTP_HOST'],
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Strict'
            ]
        );

        // Redirect after successful login
        header("Location: index.php?id=". $user['idusers']);
        exit();
    } else {
        // Improved error handling
        $sSuccessMsg = "<div class=\"alert-box alert\">Invalid username or password.</div>";
    }
}
?>
<!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width" />
  <title>OWASP Bricks Login Form #1</title>
  <!-- Included CSS Files (Uncompressed) -->
  <!--
  <link rel="stylesheet" href="../stylesheets/foundation.css">
  -->
  <!-- Included CSS Files (Compressed) -->
  <link rel="stylesheet" href="../stylesheets/foundation.min.css">
  <link rel="stylesheet" href="../stylesheets/app.css">
  <link rel="icon" href="../favicon.ico" type="image/x-icon">
  <script src="../javascripts/modernizr.foundation.js"></script>
  <!-- IE Fix for HTML5 Tags -->
  <!--[if lt IE 9]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'></head>
<body>
<div class="row">
	<div class="four columns centered">
		<br/><br/><a href="../index.php"><img src="../images/bricks.jpg" /></a><br/><br/>
		<form method="POST" action="login.php" enctype="application/x-www-form-urlencoded">
			<fieldset>
				<legend>Login</legend>
				<p><?php echo $sSuccessMsg;?></p>
				<p>Username: <input type="text" name="username" id="username" size="25" required/></p>
				<p>Password: <input type="password" name="passwd" id="passwd" size="25" required/></p>
				<p><input type="submit" class="small button" name="submit" id="submit" value="Submit"/><br/></p>
			 </fieldset>
		</form>
	</div><br/><br/><br/>
</div>
  <!-- Included JS Files (Uncompressed) -->
  <!--
  <script src="../javascripts/jquery.js"></script>
  <script src="../javascripts/jquery.foundation.mediaQueryToggle.js"></script>
  <script src="../javascripts/jquery.foundation.forms.js"></script>
  <script src="../javascripts/jquery.foundation.reveal.js"></script>
  <script src="../javascripts/jquery.foundation.orbit.js"></script>
  <script src="../javascripts/jquery.foundation.navigation.js"></script>
  <script src="../javascripts/jquery.foundation.buttons.js"></script>
  <script src="../javascripts/jquery.foundation.tabs.js"></script>
  <script src="../javascripts/jquery.foundation.tooltips.js"></script>
  <script src="../javascripts/jquery.foundation.accordion.js"></script>
  <script src="../javascripts/jquery.placeholder.js"></script>
  <script src="../javascripts/jquery.foundation.alerts.js"></script>
  <script src="../javascripts/jquery.foundation.topbar.js"></script>
  <script src="../javascripts/jquery.foundation.joyride.js"></script>
  <script src="../javascripts/jquery.foundation.clearing.js"></script>
  <script src="../javascripts/jquery.foundation.magellan.js"></script>
  -->
  <!-- Included JS Files (Compressed) -->
  <script src="../javascripts/jquery.js"></script>
  <script src="../javascripts/foundation.min.js"></script>
  <!-- Initialize JS Plugins -->
  <script src="../javascripts/app.js"></script>
</body>
</html>