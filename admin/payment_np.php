<?php

require_once "lib/header.php";

$addQuery = "WHERE hasPayed<>1";

$h2tit = "List of people that haven't yet payed";

require_once "lib/payment.php";
require_once "lib/footer.php";

?>
