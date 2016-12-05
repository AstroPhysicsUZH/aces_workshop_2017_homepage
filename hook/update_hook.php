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

# startswith function
# http://stackoverflow.com/a/10473026
function startsWith($haystack, $needle) {
    // search backwards starting from haystack length characters from the end
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}


# security check 1..
# check origin ip of the request
# gh ips are 192.30.252.0/22
# https://help.github.com/articles/what-ip-addresses-does-github-use-that-i-should-whitelist/

$gh_ips = array(
    '192.30.25'
    ,'212.51.156.200'
    );

$SAFE = FALSE;
foreach ($gh_ips as &$ghip) {
    if (startsWith($_SERVER['REMOTE_ADDR'], $ghip)) {
        $SAFE = TRUE;
    }
}
if (!$SAFE) {
    header("Status: 403 Your IP [".$_SERVER['REMOTE_ADDR']."] is not on our list; bugger off", true, 403);
    die(1);
}



# Security check 2:
# check if the secret is the same..
#
# https://gist.github.com/milo/daed6e958ea534e4eba3

$hookSecret = 'hoihoi';  # set NULL to disable check
# for debug, disable check for my home machine
if ($_SERVER['REMOTE_ADDR']==='212.51.156.200'){
    $hookSecret = NULL;
}

if ($hookSecret !== NULL) {
    if (!isset($_SERVER['HTTP_X_HUB_SIGNATURE'])) {
        throw new \Exception("HTTP header 'X-Hub-Signature' is missing.");
    } elseif (!extension_loaded('hash')) {
        throw new \Exception("Missing 'hash' extension to check the secret code validity.");
    }
    list($algo, $hash) = explode('=', $_SERVER['HTTP_X_HUB_SIGNATURE'], 2) + array('', '');
    if (!in_array($algo, hash_algos(), TRUE)) {
        throw new \Exception("Hash algorithm '$algo' is not supported.");
    }
    $rawPost = file_get_contents('php://input');
    # hash_equals would protect against timing attacks
    if ( $hash !== hash_hmac($algo, $rawPost, $hookSecret)) {
        throw new \Exception('Hook secret does not match.');
    }
};


#
# ok, we're quite save now... do your stuff..
#

$outp = array();

#$command = "git status; git reset --hard HEAD; git pull origin master; git status";

# enter commands to be executed here
$commands = array(
      "git reset --hard HEAD"
    , "git fetch"
    , "git pull https master"
    , "git status"
    , "chmod -R 777 ../*"
);

# prepare structure to save all output/return variables...
$cmds = array();
foreach ($commands as &$cmd) {
    array_push($cmds, array($cmd, array(), -1));
}

#var_dump($cmds);


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


# print output and log to file

$logfile = fopen("hook.log", "w");


fwrite($logfile,
    "Request by: ".$_SERVER['REMOTE_ADDR']."\n"
    . $_SERVER['REQUEST_TIME']."\n\n"
    );

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
    
    # file
    fwrite($logfile, "\n\n> $cmd\n--> $retval ".date(DATE_ATOM)."\n\n");
    foreach ($outp as $line) {
        fwrite($logfile, $line."\n");
    }
}
# clean up
fwrite($logfile, "\n");
fwrite($logfile, "\n----------\nPOST\n");
fwrite($logfile, var_export($_POST, true));
fwrite($logfile, "\n----------\nGET\n");
fwrite($logfile, var_export($_GET, true));
fclose($logfile);
unset($elems);
?>
