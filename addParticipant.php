<!--
/* addParticipant.php
 *
 * Date: 11 Dec. 2013
 * Author: tduff@sdsc.edu
 * About: Displays the form to add a participant to the asthma survey,
 * Generates the unique survey link for the participant, based on their
 * PregID. Click on "Send Email" to send email message to participant with unique survey link.
 *
 * Survey Monkey link is: http://www.surveymonkey.com/s/PAAQ
 * append to that link
 */-->
<?php
if(session_id() == '') {
        session_start();
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" type="text/css" href="participant.css" />

        <div id="logout">
            Welcome, <?php echo "{$_SESSION["myusername"]}"; ?>
            <form name="logout" method="post" action="logout.php">
                <input type="submit" name="Logout" value="Logout">
            </form>
        </div>

    </head>
    <body><center>



    <table id="wrapper">
    <th class="header" colspan="3" >Enter the Email and PregID for the Asthma Survey Participant</th>
        <tr></tr>
        <tr></tr>
        <tr></tr>

        <tr>
        <form name="asthma_participants" action="addParticipant.php" method="POST">

                <tr>
                    <td></td>
                    <td>E-mail :  <input type="text" name="email" autocomplete="off" required>
                        <span class="error">* </span>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td>PregID: <input type ="int" name="pregid" autocomplete="off"
                               oncopy="return false;" onpaste="return false;"
                               oncut="return false;" required>
                        <span class="error">* </span>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                     <td>PregID: <input type ="int" name="pregid2" autocomplete="off"
                               oncopy="return false;" onpaste="return false;"
                               oncut="return false;" required>
                        <span class="error">* </span>
                    </td>
                    <td></td>
                </tr>
                <tr></tr><tr></tr><tr></tr>
                <tr><td></td>
                    <td><input type="submit" name="add" value="ADD RECORD" />
                    </td>
                    <td></td>
                </tr><tr></tr><tr></tr>
                    <td></td>
                    <td><input type="submit" name="search" value="SEARCH PARTICIPANTS"  />
                    </td>
                    <td></td>
                </tr>
        </form>

    </table>
  <br /><br /><br />

  </center></body>

<?php
require_once('./dbLogin.php');
//Database Connection
$db = new Database();
$db -> Connect();

if (isset ($_POST['add'])){

    // if all three fields are set
    if (isset ($_POST['email']) && isset ($_POST['pregid']) && isset ($_POST['pregid2']))
    {

        // Check that PregIDs match, are valid numbers, then insert
        if (($_POST['pregid']) === ($_POST['pregid2']))
        {
            // pregid is only numbers, and no more than 11 digits.
            if (preg_match( "/^[0-9]{1,11}$/", $_POST['pregid']))
            {
                $pregid = get_post('pregid');
            } else {
                echo '<div id="container">';
                echo '<br /><br />';
                echo '<div class="error">PregID is one to eleven digits, numbers only. </div>';
                echo '<br /><br /><br />';
                echo '</div>';
                // clear vars for re-entry
                $pregid = '';
                exit;
            }

            // Validate email address
 if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
            {
                $email = get_post('email');
            } else {
                echo '<div id="container">';
                echo '<br /><br />';
                echo '<div class="error">You must enter a valid email address. </div>';
                echo '<br /><br /><br />';
                echo '</div>';
                //clear vars for re-entry
                $email = $pregid = '';
                exit;
            }

            // Define Vars
            $link = "http://www.surveymonkey.com/s/PAAQ";
            $survey_link = $link . "?c=$pregid";
            $query = "INSERT INTO tracking VALUES" .
                    "('', '$email', NOW(), $pregid, '$survey_link', 1, NOW(), NULL);";
            $subject = "Asthma Survey";
            $body = '';

            //INSERT Record or throw ERROR
            if ($db->Query($query))
            {
                    echo '<div id="container">';
                    echo '<br />';
                    echo "Record successfully added to the Tracking table.";
                    echo '<br /><br />';
                    $body = "Please complete the survey, regarding your asthma medications.\n". " $survey_link";

                    echo '<br />';
                    echo "The link for $email, with PregID $pregid, is: ";
                    echo '<br>';
                    echo '<a href="'. $survey_link .'" target="_blank">' . $survey_link . '</a>';
                    echo '<br /><br />';

                    $sent = send_email($email,$subject,$body);
                    

                    $email = $pregid = $pregid2 = $survey_link = $query = '';
                    echo "$sent";
                    echo '<br /><br /><br />';
                    echo '</div>';
                    echo '<br /><br />';
            } else {
                    echo '<div id="container">';
                    echo '<br />';
                    echo '<div class="error"> INSERT FAILED! </div>';
                    echo  $db->Query($query);
                    echo "<br /><br /><br />";
                    $email = $pregid = $pregid2 = $survey_link = $query = '';
                    echo '</div>';
            }// end if/else INSERT

        } else {// PregIDs do not match
            echo '<div id="container">';
            echo '<br />';
            echo '<div class="error">PregIds do not match. Please re-enter. </div>';
            echo '<br /><br /><br />';
            echo '</div>';
            // clear vars for re-entry
            $email = $pregid = $pregid2 = $survey_link = $query = '';
        }// end if PREGIDs match
    }//end if isset

$db->Close();
}//end if add button selected

if (isset ($_POST['search'])) {
    // if all three fields are set
    if (isset ($_POST['email']) && isset ($_POST['pregid']) && isset ($_POST['pregid2']))
    {

        // Search by PregID

        if (($_POST['pregid']) === ($_POST['pregid2'])) {

            // pregid is only numbers, and no more than 11 digits.
            if (preg_match( "/^[0-9]{1,11}$/", $_POST['pregid']))
            {
                $pregid = get_post('pregid');

                $sql = "SELECT email, survey_link, date_completed, last_email_sent
                    FROM tracking
                    WHERE pregid = $pregid";

                $participant = $db->Query($sql);
                $rowCount = mysql_num_rows($participant);


                if ($rowCount <> 0) {
                    while ($searchResult = mysql_fetch_array($participant)) {
                        echo '<div id="container">';
                        echo '<br />';
                        echo "Your result returned: ";
                        echo '<br /><br />';
                        echo "The link for $searchResult[0], with PregID $pregid, is: ";
                        echo '<br />';
                        echo '<a href="'. $searchResult[1] .'" target="_blank">' .$searchResult[1]. '</a>';
                        echo '<br />';
                        echo '<br />';
                        if ($searchResult[2]=== null) {
                            echo "This survey has not been completed.";
                            echo '<br>';
                            echo "The last reminder email was sent: $searchResult[3]";
                            echo '<br /><br /><br />';
                            echo '</div>';
                            echo '<br /><br />';
                            $email = $pregid = $pregid2 = $survey_link = $query = '';
                        } else {echo "This survey was completed: $searchResult[2]";
                            echo '<br>';
                            echo '<br /><br /><br />';
                            echo '</div>';
                            echo '<br /><br />';
                            $email = $pregid = $pregid2 = $survey_link = $query = '';
                        }
                    } //end while
                } else {

                        echo '<div id="container">';
                        echo '<br />';
                        echo "No records match pregid $pregid in the Tracking table.";
                        echo '<br /><br /><br />';
                        echo '</div>';
                        echo '<br /><br />';
                        $email = $pregid = $pregid2 = $survey_link = $query = '';

                } // end if rowcount >0

            } else { //pregID formatting problem
                echo '<div id="container">';
                echo '<br />';
                echo '<div class="error">PregID is one to eleven digits, numbers only. </div>';
                echo '<br /><br /><br />';
                echo '</div>';
                echo '<br /><br />';
                // clear vars for re-entry
                $email = $pregid = $pregid2 = $survey_link = $query = '';
                exit;
            }
        } else { // PregIDs do not match
            echo '<div id="container">';
            echo '<br />';
            echo '<div class="error">PregIds do not match. Please re-enter. </div>';       
            echo '<br /><br /><br />';
            echo '</div>';
            echo '<br /><br />';
            // clear vars for re-entry
            $email = $pregid = $pregid2 = $survey_link = $query = '';
        }// end if PREGIDs match
    }//end if isset

 $db->Close();
} // end if search button selected

function get_post($var)
{
    return mysql_real_escape_string($_POST[$var]);
}

function send_email($to,$subject,$message) {
    $headers = 'From: pregnancystudies@ucsd.edu';
    if (mail($to,$subject,$message, $headers)) {
            return("<p> Email successfully sent!</p>");
    }else {
        return("<p>Email delivery failed...</p>");
    }
}
?>
