<?php
/******************************
* Author: T. Duffield
* Date: 8 May 2014
* Info: run this script weekly, via Cron, to send reminder emails to participants
* who have not yet completed the survey, but have already
* received the survey link.
******Waiting for $body from Diana J.*********
**********************************/

require ('./dbLogin.php');

$db = new Database();
$db->Connect();

//find those who have not completed the survey.
$query = "SELECT pregid, email, survey_link
          FROM tracking
          WHERE date_completed is null";

$subject = "MothertoBaby Asthma Survey";
$body ='';


$incompletes = $db->Query($query);

while ($record = mysql_fetch_array($incompletes)) {

        //send email to participant
        $email = $record[1];
        $body = "Get text for this email from Diana.\n  Please click on the link to start the survey \n     $record[2] ";
        $sent = send_email($email,$subject,$body);

//      echo "\n";
//      echo "$sent";
//      echo "Pregid: $record[0], Email: $record[1], Link: $record[2]\n";

        $pregid = $record[0];
        $update = update_tracking($pregid);
//      echo "$update\n";

}//end while loop

function send_email($to,$subject,$message) {
    $headers = 'From: pregnancystudies@ucsd.edu';
    if (mail($to,$subject,$message, $headers)) {
            return("Email successfully sent!\n");
    }else {
        return("Email delivery failed...\n");
    }
}//end send_email

function update_tracking($pregID) {

        $db = new Database();
        $db->Connect();

        //update num_email_sent & last_email_sent
        $update_sql = "UPDATE tracking
                SET num_email_sent=num_email_sent+1, last_email_sent=NOW()
                WHERE pregID = $pregID";

        if($db->Query($update_sql)) {
                return( "yay! Update to tracking table succeeded! for $pregID\n");
        } else {
                return( "There was a problem updating the last_email_sent column in the tracking table...for $pregID \n");
        }//end if update_sql succeeds
        $db->Close();

}

?>

