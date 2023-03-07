<?php
// Initialize the session
session_start();
$connect = mysqli_connect("127.0.0.1", "root", "", "login_app");  
 
// Check if the user is already logged in, if yes then redirect him to user dash page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: user_dash.php");
    exit();
}
 
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$email = $password = $email = "";
$email_err = $password_err = $email_err = "";

 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST")
{

    // Check if email is empty
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter your email.";
    } else{
        $email = trim($_POST["email"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($email_err) && empty($password_err)){

        $email = mysqli_real_escape_string($connect, $_POST["email"]);  
        $password = mysqli_real_escape_string($connect, $_POST["password"]);  
        $password = md5($password);
        $query = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";  
        $result = mysqli_query($connect, $query);
        // if(mysqli_num_rows($result) > 0)  
        if (mysqli_num_rows($result) > 0)
        {  
            $_SESSION["email"] = $email; 
            // echo $_SESSION["email"];
            $row = $result->fetch_assoc();           
            $_SESSION['uid'] = $row["user_id"]; 

            // Redirect user to dashboard page
            header("location: user_dash.php");
            exit();
            // echo "<script> setTimeout(\"location.href = 'user_dash.php';\",250);</script>";

        }
        else  
        {  
            echo ' <script> alert("Wrong Email or Password. Please try again!")</script> ';  
        }  
       
    }
    
    // Close connection */
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=opera">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.3.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script> 
    <link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
    <br/> 
    <div class="redirect">
        <input type="button" class="btn btn-primary" onclick="window.location.href='admin_login.php';" value="Admin Login">  
    </div>
    <br/><br/>
    <div class="user_log" >
        <h2 align="center">Login into your account</h2>
        <!-- <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"> -->
        <form  method="post">
            <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                <label>Email</label>
                <br/>
                <input type="text" name="email" class="form-control" value="<?php echo $email; ?>">
                <span class="help-block"><?php echo $email_err; ?></span>
            </div>    
            <br/>
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Password</label>
                <br/>
                <input type="password" name="password" class="form-control">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <br/>
            <div class="user-login-btn">
                <input type="submit" class="btn btn-primary" value="OK">
            </div>
            <br/> <br/>
            <p > Don't have an account? <a href="index.php">Sign up now</a>.</p>
            <p class="lead"> Forgot your password? <a href="lost_pass.php"> Click here</a>.</p>
        </form>
    </div>    
</body>
</html>