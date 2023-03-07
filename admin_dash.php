<?php 
  session_start();  

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
        <input type="button" class="btn btn-primary" onclick=logout() value="Logout Admin">  
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
            <label>Username</label>  
            <input type="text" name="username" class="form-control" />  
            <br />  
            <label>Email: <?php echo "<b> $_SESSION[email] </b> "; ?> </label>
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
              if( empty($_POST["password"]) || empty($_POST["username"]) )  
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
                $username = mysqli_real_escape_string($connect, $_POST["username"]);  
                $password = mysqli_real_escape_string($connect, $_POST["password"]); 
                // $email = mysqli_real_escape_string($connect, $_POST["email"]);
                // $password = md5($password);  
                $query = "UPDATE  admins  SET username='$username', password='$password' WHERE email = '$_SESSION[email]' ";  
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
            $cust_query = "SELECT * FROM users";
            $cust_list = mysqli_query($connect,$cust_query); 
            $time_stamp = "SELECT time_ref FROM  user_messages";
            $time_result = mysqli_query($connect,$time_stamp);  
          ?>
          <div class="table-wrapper ">
            <!-- Mysqli prepared statement for recursive fetch of rows of table "users" from database -->
            <?php 
              while($customers =  $cust_list->fetch_assoc())
              { 
                // Creating array from mysqli fetch in order to be able to utilize array data accordingly.
                $users[] = $customers;
                // $last_msg[] = $submit_time;
              }
            ?>
            <?php 
              for ($x = 0; $x <= count($users)-1; $x++) 
              {
            ?>
            <table class="customer  table-responsive">
              <!--  Making table header toggleable through js script and collapse toggle class -->
              <th data-toggle="collapse" data-target="#content" class="accordion-toggle"><?php echo "Customer ", $users[$x]['first_name']; ?>  </th>
              <th class="time_submit"> Last interacted on </th>
              </br>
              <tr>
                <td class="table">
                  <div>
                    <table class="accordian-body collapse" id="content">
                      <tr>
                        <td class="user_detail">
                          <ul >
                            <!-- Generating the user info that was retrieved with the above mysqli fetch and 
                          array initialization of 'users' table content from MySQL database. -->
                            <?php 
                                echo 'Name: ', $users[$x]['first_name'] , "</br>";
                                echo "Last Name: " ,  $users[$x]['last_name'], "</br>"; 
                                echo "email: ",  $users[$x]['email'];
                              ?>
                          </ul>
                        </td>
                        <!-- Use of BS3 nav-pills class to add functionality to the user management board -->
                        <td class="admin_func">
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
                    </table>
                  </div>
                </td>
              </tr>
            </table>
            <?php
              }
            ?>
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

<!-- JS Script to handle the incorrect resubmission of forms when the page is refreshed. -->
<script>
  if ( window.history.replaceState ) {
  window.history.replaceState( null, null, window.location.href);
}
</script>

<!-- JS script to promp user, in order to confirm logout -->
<script>
function logout() {
  const response = confirm("Are you sure you want to do logout?");
  if (response) {
    window.location.href='logoutA.php';
  }  
}
</script>

<!-- JS Handler for collapse functionality of user management board -->
<script>
  $('.accordian-body').on('show.bs.collapse', function () {
    $(this).closest("table")
        .find(".collapse.in")
        .not(this)
        //.collapse('toggle')
})
</script>