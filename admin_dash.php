<?php 
  session_start();  
  // Include config file
  require_once "config.php";
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
          <a id="pill" class="nav-link active" data-toggle="pill" href="#Prof" role="tab">My Profile</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="customers" data-toggle="pill" href="#Customers" role="tab">My Customers</a>
        </li>
        <li class="nav-item">
          <a id="pill" class="nav-link" data-toggle="pill" href="#Messages" role="tab">Message Board</a>
        </li>
      </ul>
      <br/> <br/>
      <div class="tab-content">
        <div class="tab-pane container active" id="Prof" >
          <form method="post" class="form-group">  
            <label>Username</label>  
            <input type="text" name="username" class="form-control" />  
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
              if(empty($_POST["email"]) || empty($_POST["password"]) || empty($_POST["username"]) )  
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
                $email = mysqli_real_escape_string($connect, $_POST["email"]);
                // $password = md5($password);  
                $query = "UPDATE  admins  SET username='$username', password='$password', email='$email' WHERE email = '$_SESSION[email]' ";  
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
          <div class="table-wrapper">
            <!-- Mysqli prepared statement for recursive fetch of rows of table "users" from database -->
            <?php 
              $connect = mysqli_connect("127.0.0.1", "root", "", "login_app");  
              $cust_query = "SELECT * FROM users";
              $cust_list = mysqli_query($connect,$cust_query); 
              $time_stamp = "SELECT time_ref FROM  user_messages";
              $time_list = mysqli_query($connect,$time_stamp);  
              while($customers =  $cust_list->fetch_assoc())
              { 
                // Creating array from mysqli fetch in order to be able to utilize array data accordingly.
                $users[] = $customers;
              }
              // Check for null/undeclared value of above $users array to prevent variable from being undefined.
              if (!isset($users)){
                echo '<h4> You haven\'t got any customers yet. </h4>';
              }
              else
              {
              // Mysqli prepared statement for recursive fetch of rows of table "user_messages" from database.
                for ($x = 0; $x <= count($users)-1; $x++)
                {
            ?>
            <table class="customer table-responsive">
              <!--  Making table header toggleable through js script and collapse toggle class -->
              <th data-toggle="collapse" data-target="#content" class="accordion-toggle"><?php echo "Customer ", $users[$x]['first_name']; ?>  </th>
              <th class="time_submit"> Last interacted on 
                <?php
                  $id = $users[$x]['user_id'];
                  $sql_query = "SELECT max(time_ref) FROM  user_messages WHERE user_refid='$id' ";
                  $last_seen = mysqli_query($connect,$sql_query);  
                  /*  Checking if there are any messages submitted from the users,showing the most recent interaction 
                  through the contact form. Alternatively, show a blank date. */
                  if($last_seen){
                    $date = $last_seen->fetch_assoc();  
                    echo $date["max(time_ref)"];
                  }
                  else{
                    echo ''; 
                  }
               ?>
              </th>
              </br>
              <tr>
                <td class="table">
                  <div>
                    <table class="accordian-body collapse" id="content">
                      <tr>
                        <td class="user_detail">
                          <ul style="list-style-type: none;">
                            <!-- Generating the user info that was retrieved with the above mysqli fetch and 
                          array initialization of 'users' table content from MySQL database. -->
                            <li> <?php echo 'Name: ', $users[$x]['first_name']; ?> </li>
                            <li> <?php echo "Last Name: " , $users[$x]['last_name']; ?> <li>
                            <li> <?php echo "email: ", $users[$x]['email']; ?> </li>
                          </ul>
                        </td>
                        <!-- Link with redirect to edit user page for admin edit of user details -->
                        <td>
                          <a href="edit-user.php?id= <?= $users[$x]['user_id']?>" class="btn btn-info"> Edit User </a>
                        </td>
                        <td>             
                          <a id="del_user" class="btn btn-danger" onclick=del_prompt(<?= $users[$x]['user_id']?>)  href="#"> Delete User </a>
                        </td>
                        <td>
                          <a href="admin_dash.php?id=<?= $users[$x]['user_id']?>" onclick=active() class="btn btn-success"> Display Messages</button>
                        </td>
                      </tr>
                    </table>
                  </div>
                </td>
              </tr>
            </table>
          <?php 
              // IF ISNULL END POINT.
              }
            // FOR EACH END POINT.
            }
          ?>
          </div>
        </div>
        <div class="tab-pane container fade" id="Messages" >
          <?php
          /* Testing if admin has selected a user to display messages. If not show message below to
          direct admin to select a user. */
            if(!isset($_GET["id"])){
              echo '<h4> You have not selected a user through the "My Customers" section. </h4> </br>';
              $user_refid=0;
            }
            else{
              $user_refid = $_GET["id"];
            }
            $query = "SELECT * FROM  user_messages WHERE user_refid='$user_refid'";
            $user_name = "SELECT * FROM  users WHERE user_id = '$user_refid' ";
            // Storing the MySQL query to a variable for depiction of user submitted messages.
            $q_result = mysqli_query($connect,$query);
            $details = mysqli_query($connect,$user_name);
            if (mysqli_num_rows($q_result) > 0)
            {  
              $details = $details->fetch_assoc();
              $msg_id = 1;
              foreach($q_result as $content)
              {     
                
          ?>

          <table class="msg table">
            <th> Message <?php echo $msg_id; ?> </th>
            <th class="admin messages"> Submitted on <?php echo $content["time_ref"]; ?> </th>
            <tr>
              <td class="user_detail">
                <ul style="list-style-type: none;">
                  <!-- Generating the user info that was retrieved with the above mysqli fetch and 
                array initialization of 'users' table content from MySQL database. -->
                  <li> <?php echo 'Name: ', $details['first_name']; ?> </li>
                  <li> <?php echo "Last Name: " , $details['last_name']; ?> <li>
                  <li> <?php echo "email: ", $details['email']; ?> </li>
                </ul>
              </td>
              <td> Message: <?php echo $content['content']; ?> </td>
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

<!-- JS function  to prompt user, in order to confirm logout -->
<script>
  function logout() {
    const response = confirm("Are you sure you want to logout?");
    if (response) {
      window.location.href='logoutA.php';
    }  
  }
</script>

<!-- JS function  to prompt admin, in order to confirm deletion of selected user -->
<script>
  function del_prompt(id) {
    const agrees = confirm("Are you sure?");
    if (agrees) {
      var url = "delete.php?id= ";
      url = url.concat(id);
      // Replace href="#" with the page redirect with selected user's id as the $_GET variable.
      // var link=document.getElementById("del_user");
      // link.setAttribute("href",url);
      window.location.href=url;
    }  
  }
</script>

 <!-- JS Script to enable nav-pill "Messages" when admin select "Display Messages function in "My Customers"
     section of admin dashboard.  -->
<script>
  function active(){
    // let element = document.getElementById("Messages").classList;
    // document.getElementById("msg pill").classList.add("active");
    // document.getElementById("cust pill").classList.remove("active");
    // element.add("active")
    alert("Choose the \"Message Board \" section to see the selected user's messages.");
  }
</script>

<!-- JQuery Handler for collapse functionality of user management board -->
<script>
  $('.accordian-body').on('show.bs.collapse', function () {
    $(this).closest("table")
        .find(".collapse.in")
        .not(this)
        //.collapse('toggle')
  })
</script>
