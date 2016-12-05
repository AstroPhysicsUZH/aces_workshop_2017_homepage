<?php

require_once "lib/app.php";

try {
    $db = open_db($db_address_abs);

    $stmt = $db->prepare("SELECT * FROM {$tableName} WHERE accessKey = :akey");

    if ($stmt->execute( [ ':akey'=>$_GET['akey'] ])) {
        while ($row = $stmt->fetch()) {
            $data = $row;
            $id = $data['id'];
        }
    }

    if ($data) {
        $stmt = $db->prepare("UPDATE {$tableName} SET lastAccessDate = :lac WHERE id = :id");
        $lac = $now->format($datetime_db_fstr);
        $res = $stmt->execute( [':lac'=>$lac, ':id'=>$id ]);
        //var_dump($res);
    }
    // Close file db connection
    // -------------------------------------------------------------------------
    $db = null;
}
catch(PDOException $e) {
    // Print PDOException message
    echo $e->getMessage();
    echo '<br />';
    var_dump($e->getTraceAsString());
}

?>

<h1>Registration ID <?=$id?> activated</h1>
<p>
    <?=$data['title']?> <?=$data['firstname']?> <?=$data['lastname']?> <br>
    price: <?=$data['price']?> <br>
    has payed: <?=B($data['hasPayed'])?> <br>
</p>
