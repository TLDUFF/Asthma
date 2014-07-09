<?php
//Check if session is not registered, redirect back to main page. 
// Put this code in first line of web page. 

    session_start();
    
    if(!isset($_SESSION["myusername"]))
    {
        header("location:login_main.php");
    } 
?>

<html>
    <body>
        
        <?php
        require_once ('./addParticipant.php');
        ?>
        
    </body>
    
</html>
