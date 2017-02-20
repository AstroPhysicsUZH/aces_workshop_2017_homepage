<?php

/**
    Use this script for a simple database dump
**/

require_once "../lib/app.php";

try {
    // Create (connect to) SQLite database (creates if not exists)
    $db = open_db();

    // Create table
    // -------------------------------------------------------------------------
    $style = " style='border: 1px solid black;'";
    echo "<html><body>\n<table{$style}'>\n";

    echo "  <thead><tr>\n";
    echo "    <th{$style}>ID</th>\n";
    foreach($tableFields as $field => $type) {
        echo "    <th{$style}>{$field}</th>\n";
    }
    echo "  </tr></thead>\n";

    // Select all data from memory db messages table
    $result = $db->query("SELECT * FROM {$tableName}", PDO::FETCH_ASSOC);

    echo "  <tbody>\n";
    foreach($result as $row) {
        echo "    <tr>\n";
        foreach($row as $key => $val) {
            if (in_array($key, $boolTableFields)) {
                if     (is_null($val)) {$val = "(not set)";}
                elseif ($val == TRUE)  {$val = "yes";}
                else   {$val = "no";}
            }
            elseif (in_array($key, $choiceTableFields)) {
                if      (is_null($val)) {$val = "(not set)";}
                else { $val = $tableFields[$key][2][intval($val)]; }
            }
            echo "      <td{$style}>".nl2br(htmlentities($val,FALSE))."</td>\n";
        }
        echo "    </tr>\n";
    }
    echo "  </tbody>\n</table>\n";
    echo "</body></html>\n";

    // Close file db connection
    // -------------------------------------------------------------------------
    $db = null;
}
catch(PDOException $e) {
    // Print PDOException message
    echo $e->getMessage();
    echo '<br />';
    var_dump($e->getTraceAsString());
}
?>
