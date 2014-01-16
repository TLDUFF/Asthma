<?php

    $host="localhost"; // Host name 
    $username="otisuser"; // Mysql username 
    $password="PASSWORD"; // Mysql password 
    $db_name="asthmadb"; // Database name 
    $tbl_name="users"; // Table name 

    // Connect to server and select databse.
    mysql_connect("$host", "$username", "$password")or die("cannot connect"); 
    mysql_select_db("$db_name")or die("cannot select DB");

    // username and password sent from form 
    $myusername=$_POST['myusername']; 
    $mypassword=$_POST['mypassword']; 

    // To protect MySQL injection (more detail about MySQL injection)
    $myusername = stripslashes($myusername);
    $mypassword = stripslashes($mypassword);
    $myusername = mysql_real_escape_string($myusername);
    $mypassword = mysql_real_escape_string($mypassword);
    $sql="SELECT username, pass FROM $tbl_name WHERE username='$myusername' and pass='$mypassword'";
    $result=mysql_query($sql);

    // Mysql_num_row is counting table row
    $count=mysql_num_rows($result);

    // If result matched $myusername and $mypassword, table row must be 1 row
    if($count==1)
    {
        session_start();
        // Register $myusername, $mypassword and redirect to file "login_success.php"
        $_SESSION["myusername"] = $myusername;
        $_SESSION["mypassword"] = $mypassword; 
        header("location:login_success.php");
    } else {
        echo "Wrong Username or Password";
    }
?>