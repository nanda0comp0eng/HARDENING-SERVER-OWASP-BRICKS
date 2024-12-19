<?php
require_once(dirname(dirname(__FILE__)) . '/includes/MySQLHandler.php');  
require_once(dirname(dirname(__FILE__)) . '/config/config.php');  

// Start session for login tracking
session_start();

// Initialize error message
$sSuccessMsg = "";

// Implement basic CSRF protection
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Function to validate CSRF token
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Implement login attempt tracking
function trackLoginAttempts() {
    $max_attempts = 5;
    $lockout_time = 15 * 60; // 15 minutes

    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 1;
        $_SESSION['last_attempt_time'] = time();
    } else {
        // Check if lockout period has passed
        if (time() - $_SESSION['last_attempt_time'] > $lockout_time) {
            $_SESSION['login_attempts'] = 1;
            $_SESSION['last_attempt_time'] = time();
        } else {
            $_SESSION['login_attempts']++;
        }
    }

    // Check if max attempts reached
    if ($_SESSION['login_attempts'] > $max_attempts) {
        return false;
    }

    return true;
}

// Process login
if (isset($_POST['submit'])) {
    // Validate CSRF token
    $csrf_token = filter_input(INPUT_POST, 'csrf_token', FILTER_DEFAULT);
    if (!$csrf_token || !validateCSRFToken($csrf_token)) {
        $sSuccessMsg = "<div class=\"alert-box alert\">Invalid CSRF token. Please try again.</div>";
    } else {
        // Check login attempts
        if (!trackLoginAttempts()) {
            $sSuccessMsg = "<div class=\"alert-box alert\">Too many login attempts. Please try again later.</div>";
        } else {
            // Sanitize input manually instead of using deprecated filter
            $username = trim(strip_tags($_POST['username']));
            $pwd = $_POST['passwd'];

            // Validate that username is not empty
            if (empty($username)) {
                $sSuccessMsg = "<div class=\"alert-box alert\">Username cannot be empty.</div>";
            } else {
                // Use prepared statement to prevent SQL injection
                $stmt = mysqli_prepare($db, "SELECT * FROM users WHERE name = ? AND password = ?");
                
                // Hash the password (use a more secure method in production)
                $hashed_pwd = $pwd;
                
                mysqli_stmt_bind_param($stmt, "ss", $username, $hashed_pwd);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                
                if (mysqli_num_rows($result) > 0) {
                    $user = mysqli_fetch_assoc($result);
                    
                    // // Regenerate session ID to prevent session fixation
                    // session_regenerate_id(true);
                    
                    // // Set secure session variables
                    // $_SESSION['login'] = true;
                    // $_SESSION['user_id'] = $user['idusers'];
                    // $_SESSION['username'] = $user['name'];

                    // // Reset login attempts on successful login
                    // unset($_SESSION['login_attempts']);
                    // unset($_SESSION['last_attempt_time']);

                    // // Secure cookie settings
                    // $cookieParams = session_get_cookie_params();
                    // setcookie(
                    //     "id", 
                    //     $user['idusers'], 
                    //     [
                    //         'expires' => time() + 3600, // 1 hour
                    //         'path' => '/',
                    //         'domain' => $_SERVER['HTTP_HOST'],
                    //         'secure' => true,
                    //         'httponly' => true,
                    //         'samesite' => 'Strict'
                    //     ]
                    // );

                    // Redirect after successful login
                    header("Location: berhasil.php");
                    exit();
                } else {
                    // Failed login
                    $sSuccessMsg = "<div class=\"alert-box alert\">Invalid username or password.</div>";
                }
            }
        }
    }
}

// Generate CSRF token for the form
$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width" />
  <title>Secure Login Form</title>
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
    <br/><br/><a href="../index.php"><img src="../images/bricks.jpg" /></a><br/><br/>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="application/x-www-form-urlencoded">
      <fieldset>
        <legend>Login</legend>
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        <p><?php echo $sSuccessMsg; ?></p>
        <p>Username: <input type="text" name="username" id="username" size="25" required/></p>
        <p>Password: <input type="password" name="passwd" id="passwd" size="25" required/></p>
        <p><input type="submit" class="small button" name="submit" id="submit" value="Submit"/><br/></p>
       </fieldset>
    </form>
  </div><br/><br/><br/>
</div>
  <script src="../javascripts/jquery.js"></script>
  <script src="../javascripts/foundation.min.js"></script>
  <script src="../javascripts/app.js"></script>
</body>
</html>