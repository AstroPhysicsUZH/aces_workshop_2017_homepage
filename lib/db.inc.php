<?php

/**
    function to open a database handle (using PDO)
**/
function open_db($dba = NULL) {

    global $DB;
    global $db_address_rel, $db_address_abs, $BASEDIR, $db_proto, $db_path;

    if (!file_exists($dba)) {
        $dba = $db_proto . $BASEDIR . $db_path;
    }
#        elseif (file_exists($db_address_rel)) {$dba = $db_address_rel;}
#        else {$dba = $db_address_abs;}

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

/**
 * Get participants
 *
 * @return all participants
 **/
function get_participants($db) {
    global $tableName;
    return $db->query("SELECT * FROM {$tableName} ORDER BY LOWER(lastname) ASC, firstname ASC;", PDO::FETCH_ASSOC);
}

function get_participant_id($db, $id) {
    global $tableName;
    return $db->query("SELECT * FROM {$tableName} WHERE id={$id} ORDER BY LOWER(lastname) ASC, firstname ASC;", PDO::FETCH_ASSOC)->fetch();
}

?>
