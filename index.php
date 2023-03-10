<?php
 $connect = mysqli_connect("127.0.0.1", "root", "", "login_app");  
 session_start();  
 if(isset($_SESSION["email"]))  
 {  
      header("location:user_dash.php");  
 }  
 
 if(isset($_POST["login"]))  
 {  
      if(empty($_POST["email"]) && empty($_POST["password"]))  
      {  
           echo '<script>alert("Both Fields are required")</script>';  
      }  
      else  
      {  
           $email = mysqli_real_escape_string($connect, $_POST["email"]);  
           $password = mysqli_real_escape_string($connect, $_POST["password"]);  
           $password = md5($password); 
           $query = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";  
           $result = mysqli_query($connect, $query);  
           if(mysqli_num_rows($result) > 0)  
           {  
                $_SESSION['email'] = $email; 
                $row = $result->fetch_assoc();
                $_SESSION['uid'] = $row["user_id"]; 
                header("location:user_dash.php");  
           }  
           else  
           {  
                echo '<script>alert("Wrong User Details")</script>';  
           }  
      }  
 } 
 if(isset($_POST["register"])) 
 {
	$ucl = preg_match('/[a-zA-Z]/', $_POST["password"]); // Uppercase Letter
	$dig = preg_match('/\d/', $_POST["password"]); // Numeral
	$nos = preg_match('/[^a-zA-Z\d]/', $_POST["password"]); // Non-alpha/num characters
     if(empty($_POST["email"]) || empty($_POST["password"]) || empty($_POST["first_name"]) || empty($_POST["last_name"]) )  
     {  
           echo '<script>alert("All Fields are required")</script>';  
     }  
     else if( strlen($_POST["password"])< 8 )
     {  
	     echo '<script>alert("Password must be at least 8 characters long.")</script>';
	}
	else if (!($ucl && $dig && $nos))
	{
	     echo '<script>alert("Please enter a password with at least one Capital letter, 1 number and 1 special character")</script>';
	 }
     else if( $_POST["password"] != $_POST["password2"] ){
          echo '<script>alert("Password must be Identical! Please type in the same password in both fields.")</script>';
     }
	else{
          $firstname = mysqli_real_escape_string($connect, $_POST["first_name"]);  
          $lastname = mysqli_real_escape_string($connect, $_POST["last_name"]);  
          $password = mysqli_real_escape_string($connect, $_POST["password"]); 
	     $email = mysqli_real_escape_string($connect, $_POST["email"]);
          $password = md5($password);  
          $query = "INSERT INTO users (first_name, last_name, password, email) VALUES('$firstname', '$lastname', '$password', '$email')";  
          $duplicate = "SELECT email FROM users where email='$email' ";
          $is_duplicate = mysqli_query($connect,$duplicate);
          // Checking if email being submitted has already been created by another user. Notify new user to change email on register page.
          if(mysqli_num_rows($is_duplicate)>0){
               echo '<script>alert("There is already a user with that email!! Please use a different email address.")</script>';
          }else
          {
               if(mysqli_query($connect, $query)){  
                    // echo '<script> alert("Registration Done!!") </script>';
                    // echo "<script>setTimeout(\"location.href = 'login_page.php';\",500);</script>";
                    $_SESSION["message"]="Registration Done!!!";
                    echo "<script>setTimeout(\"location.href = 'login_page.php';\",250);</script>";
               }
          } 
     }  
     mysqli_close($connect);
 } 
 ?> 
 
 <!DOCTYPE html>  
 <html>  
     <head >  
          <title>Sign up User</title> 
          <meta http-equiv="X-UA-Compatible" >
          <meta name="viewport" content="width=device-width, initial-scale=1">
          <script src="https://code.jquery.com/jquery-3.6.3.js"></script>
          <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
          <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script> 
          <link rel="stylesheet" type="text/css" href="style.css" />
     </head>  
     <body>  
          <br>
          <div class="redirect">
               <input type="button" class="btn btn-primary" onclick="window.location.href='login_page.php';" value="User Login">  
          </div>
          <br> <br>
          <div class="reg-form"> 
		     <h3 align="center">Registry Page</h3>  
		     <br/>  
               <form method="post" class="form-group">  
                    <label>Name</label>  
                    <input type="text" name="first_name" class="form-control" />  
                    <br />  
                    <label>Last Name</label>  
                    <input type="text" name="last_name" class="form-control" />  
                    <br />
                    <label>Email</label>
				<input type="email" name="email" class="form-control" />
				<br />
                    <label>Password</label>  
                    <input type="password" name="password" class="form-control" />  
                    <br />
                    <label>Retype Password</label>  
                    <input type="password" name="password2" class="form-control" />  
                    <br />
                    <input  type="submit" name="register" value="Register" class=" reg btn btn-info" />  
                    <br />  
               </form>  
          </div>  
      </body>  
 </html>