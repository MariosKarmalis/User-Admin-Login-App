<?php
    $connect = mysqli_connect("127.0.0.1", "root", "", "login_app"); 
    $uid=$_GET["id"] ;
    if(isset($uid)){
        $query = "DELETE FROM users WHERE user_id='$uid' ";
        $result =mysqli_query($connect,$query);
        if($result){
            echo "<script>setTimeout(\"location.href = 'admin_dash.php';\",500);</script>";
            exit(0);
        }
                    
    }
?>