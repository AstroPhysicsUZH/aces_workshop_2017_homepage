<?php

$LIBDIR = "";
$BASEDIR = "";

if (file_exists("settings.inc.php")) {
    $LIBDIR = "";
    $BASEDIR = "../";
} elseif (file_exists("lib/settings.inc.php")) {
    $LIBDIR = "lib/";
    $BASEDIR = "./";
} elseif (file_exists("../lib/settings.inc.php")) {
    $LIBDIR = "../lib/";
    $BASEDIR = "../";
} else {
    echo "import error";
    die;
}

# echo $LIBDIR . ' ' . $BASEDIR . "<br />\n";
require_once $LIBDIR."settings.inc.php";
require_once $LIBDIR."processing.inc.php";
require_once $LIBDIR."functions.inc.php";
require_once $LIBDIR."db.inc.php";

function _load_mailer() {
    global $LIBDIR;
    require_once $LIBDIR."emails.inc.php";
}

$appIsLoaded = True;
?>
