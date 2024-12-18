<?php
// Ensure uploads directory exists (minimal safeguard)
$upload_dir = 'uploads/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// VULNERABLE: Intentionally weak file upload mechanism
if(isset($_POST['upload'])) {
    // CRITICAL VULNERABILITY: Direct file name usage without sanitization
    $filename = $_FILES['userfile']['name'];
    
    // VULNERABLE: No file type validation
    // VULNERABLE: No file size checking
    // VULNERABLE: No extension filtering
    $destination = $upload_dir . $filename;
    
    // VULNERABLE: Direct move_uploaded_file with no additional checks
    if (move_uploaded_file($_FILES['userfile']['tmp_name'], $destination)) {
        // VULNERABLE: Potential XSS in displaying uploaded file path
        echo "<div class=\"alert-box success\">Upload successful: <a href='$destination'>View File</a><a href=\"\" class=\"close\">&times;</a></div>";
    } else {
        echo "<div class=\"alert-box alert\">Upload failed. Check permissions and file size.<a href=\"\" class=\"close\">&times;</a></div>";
    }
}

// BONUS VULNERABILITY: File listing function
function list_uploaded_files($directory) {
    $files = glob($directory . '*');
    if ($files) {
        echo "<h3>Uploaded Files:</h3>";
        echo "<ul>";
        foreach ($files as $file) {
            // VULNERABLE: Direct file path display
            echo "<li><a href='" . htmlspecialchars($file) . "'>" . htmlspecialchars(basename($file)) . "</a></li>";
        }
        echo "</ul>";
    }
}
?>
<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width" />
    <title>OWASP Bricks File Upload #1</title>
    <link rel="stylesheet" href="../stylesheets/foundation.min.css">
    <link rel="stylesheet" href="../stylesheets/app.css">
    <link rel="icon" href="../favicon.ico" type="image/x-icon">
    <script src="../javascripts/modernizr.foundation.js"></script>
</head>
<body>
<div class="row">
    <div class="four columns centered">
        <br/><br/><a href="../index.php"><img src="../images/bricks.jpg" /></a><br/>
        <p>
            <!-- VULNERABLE: No client-side or server-side file type restrictions -->
            <form enctype="multipart/form-data" action="index.php" method="POST">
                <fieldset>
                    <legend>Upload (No Restrictions)</legend>
                    <input name="userfile" type="file" class="small button" /><br/><br/>
                    <input type="submit" name="upload" class="small button" id="upload" value="Upload" /><br/><br/>
                </fieldset>
            </form>

            <!-- Bonus: File Listing -->
            <?php list_uploaded_files($upload_dir); ?>
        </p><br/>
    </div>
</div>

<script src="../javascripts/jquery.js"></script>
<script src="../javascripts/foundation.min.js"></script>
<script src="../javascripts/app.js"></script>
</body>
</html>