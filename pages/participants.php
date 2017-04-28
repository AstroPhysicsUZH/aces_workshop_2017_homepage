<?php

require_once "lib/app.php";

$db = open_db();
$participants = get_participants($db);
?>

<h1>Participants</h1>

<p>
    So far the following people have registered:
</p>

<table class="participantstable">
    <tbody>

<?php
foreach($participants as $p) {

    $ti = htmlentities($p['title'], FALSE);
    $fn = htmlentities($p['firstname'], FALSE);
    $ln = htmlentities($p['lastname'], FALSE);
    $af = htmlentities($p['affiliation'], FALSE);
    if (strlen($af)<1) {$af="none";}

    print <<<EOT
        <tr>
            <td class='small title'>{$ti}</td>
            <td>{$fn} {$ln}</td>
            <td class='small'> ({$af})</td>
        </tr>
EOT;

}

?>

    </tbody>
</table>
