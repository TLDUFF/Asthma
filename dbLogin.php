<?php

/* dbLogin.php
 *
 * Description of Database Class:
 *
 * Date: 11 Dec. 2013
 * Author: tduff@sdsc.edu
 * About: login information to connect to the database - for use
 * with the Asthma / Survey Monkey project.
 *
 * Required by addParticipant.php
 */
 class Database{
    private $host;
    private $username;
    private $password;
    private $database;

    public $result;

    private $dbConn = null;
    private $memorized_query = '';

    //-----------------------------------------------------------------------------
    //@Function: Database abstraction layer constructor
    //
    //@Description: Create a new instance of Database
    //
    //@Input: $link (Database connection, mysql resource from mysql_connect return)
    //
    //@Output: None
    //------------------------------------------------------------------------------
    function __construct($link = null){
        $this->dbConn = $link;
    }
    //------------------------------------------------------------------------------
    //@Function: Connect
    //
    //@Description: Create a new database connection
    //
    //@Input: $host, $database, $username, $password
   //
    //@Output: None
    //-------------------------------------------------------------------------------
    function Connect($host = '',$database = '',$username = '',$password = '')
    {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;

        //make the connection
        $this->dbConn = mysql_connect($this->host,$this->username,$this->password)or die(mysql_error()."\n");

        //select the db
        mysql_select_db($database,$this->dbConn);
    }
    //-------------------------------------------------------------------------------------
    //@Function: Close
    //
    //@Description: Terminate a connection.
    //@Input: None
    //
    //@Output: None
    //-------------------------------------------------------------------------------------
    function Close()
    {
        if(!mysql_close($this->dbConn))
        {
            printf("SQL error in %s(line %u) ");
            if(DEBUG_MODE)
            {
                print "<pre>$query</pre>";
            }
        }
    }
    //-------------------------------------------------------------------------------
    //@Function: Query
    //@Description: Process a Query. The result gets stored in $this->result.
    //@Input: $query
    //@Output: None
    //-------------------------------------------------------------------------------
    function Query( $query )
    {
        $this->result = mysql_query($query);
        if(!$this->result){
            //print out the error
            $error = mysql_error();
            echo "$error\n";
        } else {
            return $this->result;
        }
    }

    function Fetch_Array()
    {
        $rv = mysql_fetch_array( $this->result, MYSQL_ASSOC) or die (mysql_error($this->dbConn));
        return $rv;
    }

    function Num_Rows()
    {
        $intCount = mysql_num_rows($this->result)or die(mysql_error($this->dbConn));
        return $intCount;
    }



 } //end Class Database
?>
        
