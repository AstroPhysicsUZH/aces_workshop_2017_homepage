<?php

require "lib/headerphp.php";

$stmtstr = "SELECT *
            FROM {$tableName}
            ORDER BY id ASC;" ;

$data = $db->query( $stmtstr )->fetchAll(PDO::FETCH_OBJ);

header('Content-Type: application/json; charset=UTF-8');
# header('Content-Disposition: attachment; filename="participants.csv";');

print json_encode($data);

?>
