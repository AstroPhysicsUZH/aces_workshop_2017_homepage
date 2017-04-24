<?php

require_once "lib/db.inc.php";

// Create table
// -------------------------------------------------------------------------
$style1 = " style='border: 0px solid black; width: 4em; text-align: right;'";
$style2 = " style='border: 0px solid black;'";
echo "<table{$style2}>\n";

$result = get_participants();

echo "  <tbody>\n";
foreach($result as $r) {
    echo "    <tr>\n";
    echo "      <td class='small'{$style1}>".nl2br(htmlentities($r['title'],FALSE))."</td>\n";
    echo "      <td{$style2}>".nl2br(htmlentities($r['firstname'], FALSE))." ";
    echo " ".nl2br(htmlentities($r['lastname'], FALSE))."</td>\n";
    echo "      <td class='small'{$style2}> (".nl2br(htmlentities($r['affiliation'], FALSE)).")</td>\n";
    echo "    </tr>\n";
}
echo "  </tbody>\n</table>\n";
?>
