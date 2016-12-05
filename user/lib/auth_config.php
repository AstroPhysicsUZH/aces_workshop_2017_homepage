<?php
// ------------------ SETTINGS ------------------------------------------------

error_reporting(E_ALL); # debug
#error_reporting(NULL);  # production

define('SESSION_MAX_IDLE_TIME', 3600); # max idle time in sec
ini_set('session.gc_maxlifetime', SESSION_MAX_IDLE_TIME);
session_set_cookie_params (SESSION_MAX_IDLE_TIME);
?>
