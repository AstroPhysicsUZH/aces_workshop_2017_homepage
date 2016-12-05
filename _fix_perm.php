<?php
  $outp = "";
  exec("chmod -R 777 * 2>&1", $outp, $retval);
  var_dump($outp);
?>

