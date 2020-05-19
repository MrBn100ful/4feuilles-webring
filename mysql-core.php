<?php

const SQLHOST = 'localhost';			//MySQL server address, usually localhost
const SQLUSER = 'user';			//MySQL user (must be changed)
const SQLPASS = 'password';			//MySQL user's password (must be changed)
const SQLDB = 'db';			//Database used by image board




if (! $con = mysqli_connect(SQLHOST, SQLUSER, SQLPASS)) {
    echo S_SQLCONF; // unable to connect to DB (wrong user/pass?)
    exit();
}


$db_id = mysqli_select_db($con, SQLDB);
if (! $db_id) {
    echo S_SQLDBSF;
}

function mysqli_call($query)
{
    global $con;
    $ret = mysqli_query($con, $query) or die(mysqli_error($con));
    if (! $ret) {
        echo $query . "<br />";
    }
    return $ret;
}



// check for table existance
function table_exist($table)
{
    $result = mysqli_call("show tables like '$table'");
    if (! $result) {
        return 0;
    }
    $a = mysqli_fetch_row($result);
    mysqli_free_result($result);
    return $a;
}
