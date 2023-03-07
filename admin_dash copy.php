<?php 
  session_start();  
  $_SESSION["customer"] = false;
  if(!isset($_SESSION["email"])) 
  {  
    session_destroy();
    header("location:admin_login.php");  
    exit();
  }   
  
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="X-UA-Compatible" >
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://code.jquery.com/jquery-3.6.3.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script> 
  <link rel="stylesheet" type="text/css" href="style.css" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
  <!-- Imported BS 4.6.2 JS because BS 5.3 had issues with modal.js in tab-content section -->
  
  <title>Admin dashboard</title>
</head>
<body>
    <div class="redirect">
        <input type="button" class="btn btn-primary" onclick="window.location.href='logoutA.php';" value="Logout Admin">  
    </div>
    <br/>
    <div class="admin container">
      <ul class="nav nav-pills" role="tablist">
        <li class="nav-item">
          <a class="nav-link active" data-toggle="pill" href="#Prof" role="tab">My Profile</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="customers" data-toggle="pill" href="#Customers" role="tab" onclick="set_var()">My Customers</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-toggle="pill" href="#Messages" role="tab">Message Board</a>
        </li>
      </ul>
      <br/> <br/>
      <div class="tab-content">
        <div class="tab-pane container active" id="Prof" >
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
              <input  type="submit" name="update" value="Update" class=" update btn btn-info" />  
            <br /> 
          </form>
          <?php
            $connect = mysqli_connect("127.0.0.1", "root", "", "login_app");  
            if(isset($_POST["update"])) 
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
                $query = "UPDATE  admins  SET first_name='$firstname', last_name='$lastname', password='$password', email='$email' WHERE email = '$_SESSION[email]' ";  
                if(mysqli_query($connect, $query))  
                {  
                    echo '<script> alert("Changes were made succesfully!!") </script>';
                }
                else
                {
                  echo '<script> alert("The mysqli_query failed to execute succesfully.") </script>';
                } 
              }  
            } 
          ?>  
        </div>
        <div class="tab-pane container fade" id="Customers">
          <?php
            
            $connect = mysqli_connect("127.0.0.1", "root", "", "login_app");  
            // $cust_list = mysqli_real_escape_string($connect,$_POST["message"]);
            $query = "SELECT * FROM users";
            $cust_list = mysqli_query($connect,$query);
                 
          ?>
          <div class="table-wrapper">
            <div class="table row">
              <div class="col-xs-5">
                  <h2>User Management</h2>
              </div>
            </div>
            <table class="customer table-hover table-responsive">
              <thead>
                <tr>
                  <th class="col-xs-5" id="customer">
                    User Details
                  </th>
                  <th class="col-xs-3">
                    Actions
                  </th>
                </tr> 
              </thead>
              <?php 
                while($customers =  $cust_list->fetch_assoc())
                { // while loop start point
              ?>
              <tbody>
                <tr>
                  <td>
                    <ul>
                      <?php 
                          echo 'Name: ', $customers['first_name'] , "</br>";
                          echo "Last Name: " , $customers['last_name'], "</br>"; 
                          echo "email: ", $customers['email'];
                        ?>
                    </ul>
                  </td>
                  <td>
                  <ul class="nav nav-pills" role="tablist">
                    <li class="nav-item">
                      <a class="nav-link" data-toggle="pill" href="#Edit" role="tab">Edit</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="customers" data-toggle="pill" href="#Del" role="tab" >Delete</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" data-toggle="pill" href="#Show" role="tab">Show Messages</a>
                    </li>
                  </ul>
                  </td>
                </tr>
              </tbody>
              <?php 
              // while loop end point
                }
            ?>
            </table>
          </div>
          <!-- Tab content of user management pill section of table -->
          <div class="tab-content">
            <div id="Edit" class="tab-pane container" role="tablist">
                <h1> Edit </h1>
            </div>
            <div id="Del" class="tab-pane container" role="tablist">
              <h1> Delete </h1>
            </div>
            <div id="Show" class="tab-pane container" role="tablist">
              <h1> Show </h1>
            </div>
          </div>
        </div>
        <div class="tab-pane container fade" id="Messages" >
          <?php
            $connect = mysqli_connect("127.0.0.1", "root", "", "login_app");  
            // $message = mysqli_real_escape_string($connect,$_POST["message"]);
            $query = "SELECT content FROM  user_messages ";
            // Storing the MySQL query to a variable for depiction of user submitted messages.
            $q_result = mysqli_query($connect,$query);
            if (mysqli_num_rows($q_result) > 0)
            {  
              $msg_id = 1;
              while($content = $q_result->fetch_assoc())
              {
                
          ?>

          <table class="table">
            <th> Message <?php echo $msg_id; ?> </th>
            <tr>
                  <td> <?php echo $content['content']; ?> </td>
                  <br/>
            </tr>
          </table>
          <?php
              $msg_id += 1;
              }
            }
            else
            {
              echo '<h4> No messages have been submitted through the form yet. </h4>';
            }
          ?>
        </div>
      </div>
    </div>
</body>
</html>

<script>
 function set_var() {
  // var x = document.getElementById("customers");
  $_SESSION["customer"] = true;
 }
</script>

<!-- JQuery script to provide functionality to admin user management table -->
<!-- <script>
$(document).ready(function(){
	$('[data-toggle="pill"]').tooltip();
});
</script> -->

<!-- JS Script to handle the incorrect resubmission of forms when the page is refreshed. -->
<script>
  if ( window.history.replaceState ) {
  window.history.replaceState( null, null, window.location.href);
}
</script>