<!DOCTYPE html>  
<html lang="en">  
<head>  
     <title>Edit User</title> 
     <meta http-equiv="X-UA-Compatible" >
     <meta name="viewport" content="width=device-width, initial-scale=1">
     <script src="https://code.jquery.com/jquery-3.6.3.js"></script>
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script> 
     <link rel="stylesheet" type="text/css" href="style.css" />
</head>  
<body>  
     <div class="reg-form"> 
          <h3>Edit User Details</h3>  
          <br/> 
          <?php          
               $connect = mysqli_connect("127.0.0.1", "root", "", "login_app");      
               if (isset($_GET["id"]))
               {
                    $uid=$_GET["id"];
                    $query = "SELECT * FROM users WHERE user_id='$uid' ";
                    $result =mysqli_query($connect,$query);
                    
                    if(mysqli_num_rows($result)>0)
                    {
                         foreach($result as $user)
                         {
                    ?>       
          <form method="post" class="form-group">  
               <label>First Name</label>  
               <input type="text" name="first_name" value=<?=$user["first_name"]?> class="form-control" />  
               <br />  
               <label>Last Name</label>  
               <input type="text" name="last_name" value=<?=$user["last_name"]?> class="form-control" />  
               <br />
               <label>Email</label>
               <input type="email" name="email"value=<?=$user["email"]?> class="form-control" />
               <br />
               <label>Password</label>  
               <input type="password" name="password" class="form-control" />  
               <br />
               <input type="submit" name="update_user" value="Update" class=" reg btn btn-success" />  
               <br />  
          </form>  
          <?php
                    // foreach() end point
                         }                  
                    // if mysqli_num_rows() end point.
                    }
               // if isset() end point.
               }     
          ?>
     </div> 
     
     <?php
          if(isset($_POST["update_user"])) 
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
               else
               {
                    $firstname = mysqli_real_escape_string($connect, $_POST["first_name"]);  
                    $lastname = mysqli_real_escape_string($connect, $_POST["last_name"]);  
                    $password = mysqli_real_escape_string($connect, $_POST["password"]); 
                    $email = mysqli_real_escape_string($connect, $_POST["email"]);
                    $password = md5($password);  
                    $query = "UPDATE  users  SET first_name='$firstname', last_name='$lastname', password='$password', email='$email' WHERE user_id = '$uid' "; 
                    if(mysqli_query($connect,$query))
                    {  
                         echo "<script>setTimeout(\"location.href = 'admin_dash.php';\",500);</script>";
                    }
                    else{
                         echo '<script> alert("The mysqli_query failed to execute succesfully.") </script>';
                    } 
               } 
          } 
     ?>   
</body>  
</html>
