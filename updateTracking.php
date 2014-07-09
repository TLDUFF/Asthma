<?php
require ('./dbLogin.php');

$db = new Database();
$db->Connect();

$resultsArray = array();
$resultsArray = runCURL();

$update_sql = '';
$custom_id = '';
$modified_date = '';
$status = '';

//Test that the mulitdimensional array came thru.
//Format of resultsArray is $resultsArray[0][status]=status,
//                        $resultsArray[0][custom_id]=custom_id,
//                        $resultsArray[0][date_modified]=mod_date,
//                        $resultsArray[0][respondent_id]=respondent_id

for( $i=0; $i< count($resultsArray) ; ++$i){

        $custom_id=$resultsArray[$i]['custom_id'];
//         echo "$custom_id\n";
        $modified_date=$resultsArray[$i]['date_modified'];
//         echo "$modified_date\n";
        $status=$resultsArray[$i]['status'];
//         echo "$status\n";

       if ($status === "completed") {

              $update_sql = "UPDATE tracking
                              SET date_completed = '$modified_date'
                              WHERE pregID = $custom_id
                              AND date_completed is NULL";

              $db->Query($update_sql) or die(mysql_error()."\n");

                   $rows = mysql_affected_rows();

                    if($rows === 0) { // WRITE THIS OUTPUT TO A FILE?

                      echo "NOTE: No Rows updated for pregID $custom_id in the tracking table.\n";
                      echo "Check if it was already updated.\n\n";

                    } else {

                        echo "Updated $custom_id with completed date: $modified_date \n";
                        echo "\n";

                    }//end if $rows



        }// end if $status

}//end outer for loop

$db->Close();
function runCURL() {

        $ch = curl_init();
        $url = 'https://api.surveymonkey.net/v2/surveys/get_respondent_list?api_key=_APIKEY_';
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //true
        $token = '_';
        $headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $token);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers );
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);

        $params = '{"survey_id":"_","fields":["custom_id","status","date_modified"]}';
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

        $response = curl_exec($ch);

        if ($response === false) {
                echo "CURL Error!";
        }

        curl_close($ch);

//      var_dump (json_decode($response, true));

        $myArray = json_decode($response, true);
        $assocArray = array();

        //Get to nested data we really want - assign to another associative array
        $assocArray = findData($myArray);

//        print_r($assocArray);

        return $assocArray;

} //end runCURL

function findData($array){

/* Loop through nested array, to get to data we want
*  assign that data to associative array
*  return associative array ($multiArray)
*/
    $multiArray = array();

    foreach($array as $foo=>$bar){

      if (is_array($bar)){

         if ($bar["respondents"]){

                foreach ($bar['respondents'] as $key => $value) {

                        $results = array();
                        $results['status'] = $value['status'];
                        $results['custom_id'] = $value['custom_id'];
                        $results['date_modified'] = $value['date_modified'];
                        $results['respondent_id'] = $value['respondent_id'];
                        $multiArray[] = $results;

                } // end foreach

                return $multiArray;

         }else{

            findData($bar);

         } //end if else

      } //end if is_array

    } //end foreach

} //end findData

?>
