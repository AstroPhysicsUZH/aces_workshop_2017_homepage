<?php

if (isset($_GET['mid'])){ $mid = $_GET['mid']; }
else { $id = "no_msg"; }


if ($mid == "no_msg") {
    echo "";
}


elseif ($mid=="reg_suc") {

    $dbh = new PDO($db_address_abs);
    $stmt = $dbh->prepare("SELECT * FROM {$tableName} WHERE id=:uid LIMIT 1");
    $stmt->execute([':uid' => $_POST['id']]);
    $data = $stmt->fetch();

    if (! $data['accessKey'] == $_POST['accessKey']) {
        echo "no spoofing plz!";
        die(1);
    }

    require "items/registration_successful.php";
    require "items/payment_instructions.php";

    // var_dump($_POST);
    // echo "<hr />";
    // var_dump($data);
}

?>
