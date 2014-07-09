<?php
//Update DB tables as $_POST vars are set.
    require_once('./dbLogin.php');

   $db = new Database();
   $db->Connect();

// Search by email
    if (isset ($_POST['email'])) {

       $email = trim($_POST['email']);
       $sql = "SELECT survey_link
               FROM participants
               WHERE email = $email";

           if (!($db->Query($sql))) {
             echo mysql_error($sql);
           }
        header('Location:./addParticipant.php');
    }


// Search by PregID
    if(isset($_POST['pregid']) && isset($_POST['pregid2'])){

        if (($_POST['pregid']) === ($_POST['pregid2'])) {

            // pregid is only numbers, and no more than 11 digits.
            if (preg_match( "/^[0-9]{1,11}$/", $_POST['pregid']))
            {
                $pregid = get_post('pregid');

                $sql = "SELECT survey_link
                    FROM participants
                    WHERE pregid = $pregid";

                if (!($db->Query($sql))) {
                     echo mysql_error($sql);
                }

                header('Location:./addParticipant.php');

            } else {
                echo '<div class="error">PregID is one to eleven digits, numbers only. </div>';
                // clear vars for re-entry
                $pregid = '';
                exit;
            }
        } else { // PregIDs do not match
            echo '<div class="error">PregIds do not match. Please re-enter. </div>';
            // clear vars for re-entry
            $email = $pregid = $pregid2 = $survey_link = $query = '';
        }// end if PREGIDs match

    }// end isset


function get_post($var)
{
    return mysql_real_escape_string($_POST[$var]);
}

?>
