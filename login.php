<?php
$target =
    $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']
    . dirname($_SERVER['SCRIPT_NAME']) . '/user/';
header('Location: ' . $target , true, $_SERVER['SERVER_PROTOCOL'] == 'HTTP/1.1' ? 303 : 302);

?>
