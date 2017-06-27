<?php

require_once "lib/headerphp.php";
require_once "../lib/app.php";



$stmtstr = "SELECT
                id, title, firstname, lastname, email, affiliation,
                nPersons, nVeggies, price
            FROM {$tableName}
            ORDER BY lastname ASC;" ;

$meals = $db->query( $stmtstr )->fetchAll(PDO::FETCH_OBJ);



function print_line($p){
    $pid = sprintf("%03u", $p->id);
    $name = substr($p->firstname,0,1) . ". " . $p->lastname;

    print "<span class='pid small'><code>[{$pid}]</code></span> ";
    print "<span class='name'>{$name}</span> ";
    print "<br>\n";
}

?>
<html>
<head>
    <link rel="stylesheet" href="../js/jquery-ui-1.12.0.custom/jquery-ui.min.css">
    <link rel="stylesheet" href="../js/jquery-ui-1.12.0.custom/jquery-ui.theme.min.css">
    <!--<link rel="stylesheet" href="../css/layout_hack.css">-->
    <script src="../js/jquery-1.12.1.min.js"></script>
    <script src="../js/jquery-ui.min.js"></script>
    <script src="../js/moment.min.js"></script>

<style>
    p {
        display: block;
    }
    .small {
        font-size: 80%;
    }

    td {
        /*border-top: 1px solid black;*/
        border-bottom: 1px solid black;
        border-collapse: collapse;
    }
</style>


</head>
<body>
    <h1>Meal Time!</h1>
    <table>
        <tr>
            <th>id</th>
            <th colspan="2">name</th>
            <th># Meat</th>
            <th># Veggies</th>
            <th>Price</th>
        </tr>

<?php
$n = 0;
$NM = 0;
$NV = 0;
$NP = 0;
foreach ($meals as $M) {
    $n += 1;
    $pid = sprintf("%03u", $M->id);
    $aff = "";
    $email = "";
    $price = (int)$M->price ;
    $npers = (int)$M->nPersons;
    $nVegg = (int)$M->nVeggies;
    $nMeat = $npers - $nVegg;
    if (array_key_exists('email', $M)) {$email = $M->email;}
    $NM += $nMeat;
    $NV += $nVegg;
    $NP += $price;



    print "<tr>";
    print "<td><code>[{$pid}]</code></td>";
    print "<td>{$M->lastname}</td>";
    print "<td>{$M->firstname}</td>";
    print "<td style='text-align:center'>{$nMeat}</td>";
    print "<td style='text-align:center'>{$nVegg}</td>";
    print "<td style='text-align:right'>{$price}</td>";

    print "</tr>";

    #print "<h3>{$TS->talkTitle}</h3>\n";
    #print "<p class=''>{$TS->talkCoauthors}</p>\n";
    #print "<p class='small'>{$aff}</p>\n";
    #print "<p class='small'>(submitted by: {$TS->lastname}, {$TS->firstname} <code>[{$pid}]</code> email: <a href='mailto:$email'>$email</a>)</p>\n";
    #print "<p>{$TS->talkAbstract}</p>\n";
    #print "<hr />";

}

print "<tr>";
print "<td></td>";
print "<td>TOTAL</td>";
print "<td>TOTAL</td>";
print "<td style='text-align:center'>{$NM}</td>";
print "<td style='text-align:center'>{$NV}</td>";
print "<td style='text-align:right'>{$NP}</td>";
?>

</table>

</body></html>
