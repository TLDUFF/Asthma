

<script type="text/javascript">
            function searchParticipants(){
                document.forms['asthma_participants'].action = 'searchParticipants.php';
                document.forms['asthma_participants'].submit();
            }
        </script>

 <input type="button" name="search" value="FIND RECORD" onclick="searchParticipants();" />
 
<?php

//require_once('dbLogin.php');
//
//// (1) Open the Database Connection
//$db_server = mysql_connect($db_hostname, $db_username, $db_password);
//
//if (!$db_server) die("Unable to connect to MySQL: " . mysql_error());
//
////(2) Select the asthmadb
//mysql_select_db($db_database, $db_server)
//        or die("Unable to select database: " . mysql_error());

// QUERY participants table  TDUFF CHANGE THIS LOGIC
if (isset($_POST['email']) &&
    isset($_POST['pregid']))
{
    $email = get_post('email');
    $pregid = get_post('pregid');
    
    $query = "SELECT email, pregid, survey_link FROM participants" .
            "WHERE email =  $email ".
            "AND pregid =  $pregid ";
    
    if (mysql_query($query, $db_server)) {
        echo '<div id="container">';
        echo '<br />';
        echo "The following link was found for $email with PregID $pregid";
        echo '<br /><br />';
        
        echo '<br /><br />';
        echo '</div>';
    } else {
        echo '<div id="container">';
        echo '<br />';
        echo '<div id="error"> QUERY FAILED: </div>' . " $query<br /><br />" .
            mysql_error() . "<br /><br />";
        echo '</div>';
    }// end if/else 
} else {
    echo "Please fill in both E-mail and PregID.";
}// end if isset

mysql_close($db_server);

function get_post($var)
{
    return mysql_real_escape_string($_POST[$var]);
}
?>
