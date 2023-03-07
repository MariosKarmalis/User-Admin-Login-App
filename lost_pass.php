<?php
    // Initialize the session
    session_start();
    $connect = mysqli_connect("127.0.0.1", "root", "", "login_app");  
    // Initializing email error string variable for php to work in email form field of the below html doc.
    $email_err ="";
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        
        <meta http-equiv="X-UA-Compatible" content="IE=firefox">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Password Recovery</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
        <script src="https://code.jquery.com/jquery-3.6.3.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script> 
        <link rel="stylesheet" type="text/css" href="style.css" />
    </head>
    <body>
        <br>
        <div class="redirect">
            <input type="button" class="btn btn-dark" onclick="window.location.href='login_page.php';" value="User Login">  
        </div>
        <div class="lost-pass container">
            <form method="post">
                <div class="form-group">
                    <label class="label-default">Recovery Email</label>
                    <br>
                    <input type="email" name="recovery" class="form-control">
                    <?php
                        if($_SERVER["REQUEST_METHOD"] == "POST")
                        {
                            // Check if email is empty and strip front-end whitespaces.
                            if(empty(trim($_POST["recovery"]))){
                                $email_err = "Please enter your email.";
                            }
                        }   
                    ?>
                    <span class="help-block"><?php echo $email_err; ?></span>
                </div>
                <div class="lost-pass btn">
                    <input type="submit" class="btn btn-dark" name="send_mail" value="Send Email">
                </div>
            </form>
            <br> <br>
            <?php 
                // Set the parameters for php mail() function
                $rec_msg = "You have asked for a password change. Was that you?";
                $headers  = 'From: [your_gmail_account_username]@gmail.com' . "\r\n" .
                'MIME-Version: 1.0' . "\r\n" .
                'Content-type: text/html; charset=utf-8';
                if(isset($_POST['recovery']) && empty($email_err))
                {
                    $email_address = $_POST['recovery'];
                    //echo $email_address;
                    $flag = mail( $email_address, "Password recovery for user with mail: $email_address", $rec_msg, $headers);
                    if($flag)
                    {   
                        echo "<h5> Thank You! Your message has been sent. </h5>";
                    } else {
                        // http_response_code(500);
                        echo "<h5> Whoa! message could not be sent. </h5>";
                    }
                }
            ?>
        </div>
    </body>
</html>

<!-- JS Script to handle the incorrect resubmission of forms when the page is refreshed. -->
<script>
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href);
    }
</script>