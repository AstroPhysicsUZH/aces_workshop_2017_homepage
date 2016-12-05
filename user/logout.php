<?php
session_start();
session_destroy();

header('Location: http://' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . '/index.php');

?>
