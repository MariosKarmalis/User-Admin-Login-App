<?php 
  session_start();  
  // Setting the php.ini file to the correct timezone without altering the configuration file activele.
  date_default_timezone_set('Europe/Athens');
  // Checking if the user is already logged in, thus avoiding to login a 2nd time.
  if(!isset($_SESSION["email"]) || !isset($_SESSION['user'] ) )
  {  
    session_destroy();
    header("location:login_page.php");  
    exit();
  }   
  
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="X-UA-Compatible" content="IE=mozilla">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://code.jquery.com/jquery-3.6.3.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script> 
  <link rel="stylesheet" type="text/css" href="style.css" />
  <!-- Imported BS 4.6.2 JS because BS 5.3 had issues with modal.js in tab-content section -->
  <title>Your dashboard</title>
</head>
<body>
    <div class="redirect">
        <input type="button" class="btn btn-primary" onclick=logout() value="Logout User">  
    </div>
    <br/>
    <div class="user container">
      <ul class="nav nav-pills" role="tablist">
        <li class="nav-item">
          <a class="nav-link active" data-toggle="pill" href="#Prof" role="tab">My Profile</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-toggle="pill" href="#Submit" role="tab">Submit Form</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-toggle="pill" href="#Messages" role="tab">Messages</a>
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
            <label>Password</label>  
            <input type="password" name="password" class="form-control" />  
            <br />
            <label>Email</label>
            <input type="email" name="email" class="form-control" />  
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
                $query = "UPDATE  users  SET first_name='$firstname', last_name='$lastname', password='$password', email='$email' WHERE email = '$_SESSION[email]' ";  
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
        <div class="tab-pane container fade" id="Submit" >
          <form id="msg" method="post" class="form-group">
            <p><label for="message">Message:</label> </p>
            <textarea id="message" name="message" rows="10" cols="65" form="msg">Enter your message here.</textarea>
            <br/>
            <input  type="submit" name="send" value="Send" class=" send btn btn-info"/>  
          </form>
          <?php
            $connect = mysqli_connect("127.0.0.1", "root", "", "login_app");  
            if (isset($_POST["send"]))
            {
              $message = mysqli_real_escape_string($connect,$_POST["message"]);
              // Get the current date from the php date() function
              $curr_date = date('Y-m-d H:i:s');
              //  MySQL Query to insert message content to database table, while also inserting the timestamp of when the message was submitted.
              $query = "INSERT INTO user_messages (user_refid,content,time_ref) VALUES ('$_SESSION[uid]','$message', '$curr_date' )";
              if (mysqli_query($connect,$query))
              {
                echo '<script> alert("The message has been submitted succesfully!!") </script>';
              }
              else
              {
                echo '<script> alert("The mysqli_query failed to execute succesfully.") </script>'; 
              }
            }
          ?>
        </div>
        <div class="tab-pane container fade" id="Messages" >
          <?php
            $uid= $_SESSION["uid"];
            $connect = mysqli_connect("127.0.0.1", "root", "", "login_app");  
            // $message = mysqli_real_escape_string($connect,$_POST["message"]);
            $msg_content = "SELECT content FROM  user_messages WHERE user_refid = '$uid' ";
            $time_stamp = "SELECT time_ref FROM  user_messages WHERE user_refid = '$uid' ";
            // Storing the MySQL query to a variable for depiction of user submitted messages.
            $msg_result = mysqli_query($connect,$msg_content);
            $time_result = mysqli_query($connect,$time_stamp);
            if (mysqli_num_rows($msg_result) > 0)
            {  
              $msg_id = 1;
              // while($content = $msg_result->fetch_assoc())
              foreach($msg_result as $content)
              {
                $submit_time = $time_result->fetch_assoc();
          ?>
          <table class="table-responsive">
            <th>  
              <button class="btn btn-light" onclick="table_show()"> Message <?php echo $msg_id; ?> </button>  
            </th> 
            <!-- Formatting the date to European time format while accessing the sql query fetch -->
            <th class="time_submit"> Submitted on: <?php echo $submit_time['time_ref']; ?> </th>
            <tr>
                <td id="toggle"> <?php echo $content['content']; ?> </td>
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
<!-- JS Script to handle message toggles for user message history function -->
<script>
 function table_show() {
  var x = document.getElementById("toggle");
  if (x.style.display === "none") {
    // x.classList.toggle('toggle-active');
    x.style.display = "block";
  } else {
    // x.classList.toggle('toggle');
    x.style.display = "none";
  }
}
</script>

<!-- JS script to prompt user, in order to confirm logout -->
<script>
function logout() {
  const response = confirm("Are you sure you want to logout?");
  if (response) {
    window.location.href='logout.php';
  }  
}
</script>