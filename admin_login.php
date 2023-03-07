<?php

// Initialize the session
$connect = mysqli_connect("127.0.0.1", "root", "", "login_app");  
session_start();
 
// Check if the user is already logged in, if yes then redirect him to admin page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: admin_dash.php");
    exit;
}
 
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$email = $password = $email = "";
$email_err = $password_err = $email_err = "";

 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
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
        // Prepare a select statement
        $sql = "SELECT admin_id, email, password FROM admins WHERE email = ?";
        $email = mysqli_real_escape_string($connect, $_POST["email"]);  
        $password = mysqli_real_escape_string($connect, $_POST["password"]);  
        // $password = md5($password);
        $query = "SELECT * FROM admins WHERE email = '$email' AND password = '$password'";  
        $result = mysqli_query($connect, $query);  
        if(mysqli_num_rows($result) > 0)  
        {  
             $_SESSION['email'] = $email; 
            //  $row = $result->fetch_assoc();
            $_SESSION['admin_id'] = $row["admin_id"]; 
             header("location:admin_dash.php");  
        }  
        else  
        {  
             echo '<script>alert("Wrong User Details")</script>';  
        }  
    }
    
    // Close connection
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" >
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login</title>
    <script src="https://code.jquery.com/jquery-3.6.3.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script> 
    <link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
    <br/>
    <div class="redirect">
        <input type="button" class="btn btn-primary" onclick="window.location.href='login_page.php';" value="User Login">
    </div>
    <br/><br/>
    <div class="admin_log">
        <h2 align="center">Administrator Login</h2>
        <p>Please fill in your credentials to login.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="form-group" method="post">
            <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                <label>Email</label>
                <input type="text" name="email" class="form-control" value="<?php echo $email; ?>">
                <span class="help-block"><?php echo $email_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Password</label>
                <input type="password" name="password" class="form-control">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <br/>   <br/>
            <div class="admin-log-btn">
                <input type="submit" class="btn btn-primary" value="Login">
            </div>
        </form>
    </div>    
</body>
</html>