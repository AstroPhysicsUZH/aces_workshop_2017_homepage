<?php

require_once "lib/settings.inc.php";

/**
    function to open a database handle (using PDO)
**/
function open_db($dba = NULL) {

#    global $db_address;
#    if (! $dba){ $dba = $db_address; }
    $db = NULL;

    try {
        // Create (connect to) SQLite database in file
        $db = new PDO($dba);
        // Set errormode to exceptions
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch(PDOException $e) {
        // Print PDOException message
        echo $e->getMessage();
        echo '<br />';
        var_dump($e->getTraceAsString());

        die(1);
    }
    return $db;
}


/**
  Open Database
**/
try {
    // Create (connect to) SQLite database (creates if not exists)
    if (file_exists($db_address_rel)) {$dba = $db_address_rel;}
    else {$dba = $db_address_abs;}

    $db = open_db($dba);
}
catch(PDOException $e) {
    // Print PDOException message
    echo $e->getMessage();
    echo '<br />';
    var_dump($e->getTraceAsString());
    die();
}


/**
 * Get participants
 *
 * @return all participants
 **/
function get_participants() {
    global $db;
    global $tableName;
    return $db->query("SELECT * FROM {$tableName} ORDER BY LOWER(lastname) ASC, firstname ASC;", PDO::FETCH_ASSOC);
}

function get_participant_id($id) {
    global $db;
    global $tableName;
    return $db->query("SELECT * FROM {$tableName} WHERE id={$id} ORDER BY LOWER(lastname) ASC, firstname ASC;", PDO::FETCH_ASSOC)->fetch();
}

?>
