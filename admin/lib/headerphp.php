<?php
/**
    Index generator for the admin area
**/

require_once "user.php";
require_once "menu.php";
require_once "../lib/app.php";

$USER = new User();

try {
    // Create (connect to) SQLite database (creates if not exists)
    $db = open_db();
}
catch(PDOException $e) {
    // Print PDOException message
    echo $e->getMessage();
    echo '<br />';
    var_dump($e->getTraceAsString());
    die();
}
#$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
#$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

header('Content-Type: text/html; charset=utf-8');

?>
