<!--
/* addParticipant.php
 * 
 * Date: 11 Dec. 2013
 * Author: tduff@sdsc.edu
 * About: Displays the form to add a participant to the asthma survey,
 * Generates the unique survey link for the participant, based on their 
 * PregID.
 * 
 * Survey Monkey link is: http://www.surveymonkey.com/s/PAAQ
 * append to that link
 */-->

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" type="text/css" href="participant.css" />
    </head>
    <body><center>

<?php
require_once('dbLogin.php');

// (1) Open the Database Connection
$db_server = mysql_connect($db_hostname, $db_username, $db_password);

if (!$db_server) die("Unable to connect to MySQL: " . mysql_error());

//(2) Select the asthmadb
mysql_select_db($db_database, $db_server)
        or die("Unable to select database: " . mysql_error());
?>

<h2>Enter the Email and PregID for the Participant</h2>

<form name="asthma_participants" action="addParticipant.php" method="POST">
    <table>
        <tr>
            <td>E-mail:  </td>
            <td><input type="text" name="email" autocomplete="off" required>
                <span class="error">* </span>
            </td>
        </tr>
        <tr>
            <td>PregID: </td>
            <td><input type ="int" name="pregid" autocomplete="off" 
                       oncopy="return false;" onpaste="return false;" 
                       oncut="return false;" required>
                <span class="error">* </span>
            </td>
        </tr>
        <tr>
             <td>PregID: </td>
            <td><input type ="int" name="pregid2" autocomplete="off" 
                       oncopy="return false;" onpaste="return false;" 
                       oncut="return false;" required>
                <span class="error">* </span>
            </td>
        </tr>
        <tr></tr>
    </table>
    
   <input type="submit" name="submit" value="ADD RECORD" />
</form>

<?php
// if all three fields are set
if (isset ($_POST['email']) && isset ($_POST['pregid']) && isset ($_POST['pregid2'])) 
{

    // Check that PregIDs match, are valid numbers, then insert
    if (($_POST['pregid']) === ($_POST['pregid2']))
    {
        
        // set vars
        $email = get_post('email');
        $pregid = get_post('pregid');   
        // DEBUG 
//        echo "pregid is $pregid" . '<br />';
//        $pregid2 = get_post('pregid2');
        //DEBUG 
//        echo "pregid2 is $pregid2". '<br />';
        $link = "http://www.surveymonkey.com/s/PAAQ";
        $survey_link = $link . "?c=$pregid";
        $query = "INSERT INTO participants VALUES" .
                "(NULL, '$email', '$pregid', '$survey_link')";
        //DEBUG
//        echo '<br />';
//        echo "$query";
//        
        // Check that pregid is only numbers, and 10 or 11 digits.
        if (!preg_match( "/^[0-9]{10,11}$/", $pregid)) {
            echo '<div class="error">PregID must be 10 or 11 digits long. (numbers only)</div>';
            // clear vars for re-entry
            $email = $pregid = $pregid2 = $survey_link = $query = '';
            //exit;
        } else {
            //DEBUG
//            echo "Inserting into db.";
     
            //INSERT or ERROR
            if (mysql_query($query, $db_server)) {
                echo '<div id="container">';
                echo '<br />';
                echo "Record successfully added to the participants table.";
                echo '<br /><br />';
                echo "The link for $email, with PregID $pregid, is: ";
                echo '<br>';
                echo '<a href="' . $survey_link . '">' . $survey_link . '</a>';
                echo '<br /><br />';
                echo '</div>';
            } else {
                echo '<div id="container">';
                echo '<br />';
                echo '<div class="error"> INSERT FAILED: </div>' . " $query<br /><br />" .
                    mysql_error() . "<br /><br />";  //TDUFF RELOAD HOME PAGE
                echo '</div>';
            }// end if/else INSERT
        } //end if pregid is numbers
    } else {
        //TDUFF RELOAD HOME PAGE
        echo '<div class="error">PregIds do not match. Please re-enter. </div>';
        // clear vars for re-entry
        $email = $pregid = $pregid2 = $survey_link = $query = '';
    }// end if PREGIDs match
}//end if isset


mysql_close($db_server);

function get_post($var)
{
    return mysql_real_escape_string($_POST[$var]);
}



?>
    </center></body>
