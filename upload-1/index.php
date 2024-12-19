<?php
// Ensure the uploads directory exists and is writable
$upload_dir = 'uploads/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Initialize error and success messages
$message = '';
$message_type = '';

if(isset($_POST['upload'])) {
    // Check if a file was actually uploaded
    if (!isset($_FILES['userfile']) || $_FILES['userfile']['error'] !== UPLOAD_ERR_OK) {
        $message = 'No file uploaded or upload error occurred.';
        $message_type = 'alert';
    } else {
        // Sanitize the filename
        $filename = basename($_FILES['userfile']['name']);
        
        // Generate a unique filename to prevent overwriting
        $unique_filename = uniqid() . '_' . $filename;
        
        // Full path for the destination
        $destination = $upload_dir . $unique_filename;
        
        // Validate file type (optional - add more checks as needed)
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
        $file_type = mime_content_type($_FILES['userfile']['tmp_name']);
        
        // Maximum file size (5MB)
        $max_file_size = 5 * 1024 * 1024; // 5 megabytes
        
        if (!in_array($file_type, $allowed_types)) {
            $message = 'Invalid file type. Only images and PDFs are allowed.';
            $message_type = 'alert';
        } elseif ($_FILES['userfile']['size'] > $max_file_size) {
            $message = 'File is too large. Maximum file size is 5MB.';
            $message_type = 'alert';
        } else {
            // Attempt to move the uploaded file
            if (move_uploaded_file($_FILES['userfile']['tmp_name'], $destination)) {
                $message = "Upload successful. File saved as: " . htmlspecialchars($unique_filename);
                $message_type = 'success';
            } else {
                $message = 'Upload failed. Please try again.';
                $message_type = 'alert';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width" />
    <title>Secure File Upload</title>
    <link rel="stylesheet" href="../stylesheets/foundation.min.css">
    <link rel="stylesheet" href="../stylesheets/app.css">
    <script src="../javascripts/modernizr.foundation.js"></script>
</head>
<body>
<div class="row">
    <div class="four columns centered">
        <br/><br/><a href="../index.php"><img src="../images/bricks.jpg" /></a><br/>
        
        <?php if (!empty($message)): ?>
            <div class="alert-box <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
                <a href="" class="close">&times;</a>
            </div>
        <?php endif; ?>
        
        <form enctype="multipart/form-data" action="" method="POST">
            <fieldset>
                <legend>Upload File</legend>
                <input name="userfile" type="file" class="small button" /><br/><br/>
                <input type="submit" name="upload" class="small button" id="upload" value="Upload" /><br/><br/>
            </fieldset>
        </form>
    </div>
</div>

<script src="../javascripts/jquery.js"></script>
<script src="../javascripts/foundation.min.js"></script>
<script src="../javascripts/app.js"></script>
</body>
</html>