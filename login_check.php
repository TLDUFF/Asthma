<?php

  require_once('./dbLogin.php');
        $db = new Database();
        $db->Connect();

        $tbl_name="users"; // Table name


    // username and password sent from form
    $myusername=$_POST['myusername'];
    $mypassword=$_POST['mypassword'];
    $myencrypted;

    // To protect MySQL injection (more detail about MySQL injection)
    $myusername = stripslashes($myusername);
    $mypassword = stripslashes($mypassword);
    $myusername = mysql_real_escape_string($myusername);
    $mypassword = mysql_real_escape_string($mypassword);
    $myencrypted = md5($mypassword);
    $sql="SELECT username, pass FROM $tbl_name WHERE username='$myusername' and pass='$myencrypted'";
    $result=$db->Query($sql);

    // Mysql_num_row is counting table rows
    $count=mysql_num_rows($result);

    // If result matched $myusername and $mypassword, table row must be 1 row
    if($count==1)
    {
        session_start();
        // Register $myusername, $mypassword and redirect to file "login_success.php"
        $_SESSION["myusername"] = $myusername;
        $_SESSION["mypassword"] = $myencrypted;
        header("Location:./login_success.php");
    } else {
        echo "Wrong Username or Password";
    }
?>
