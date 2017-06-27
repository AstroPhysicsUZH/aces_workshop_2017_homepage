<?php

require_once "../lib/app.php";
$db = open_db();
require_once "../data/events.php";


$fmt = 'H:i';
$cur = NULL;
$cid = 0;

$stmtstr = "SELECT
                id, title, firstname, lastname, email, affiliation,
                talkTitle, talkCoauthors, talkCoauthorsAffil, talkAbstract,
                isTalkChecked, isTalkAccepted, talkSlot, talkDuration
            FROM {$tableName}
            WHERE wantsPresentTalk=1 AND isTalkAccepted=1;
            ORDER BY `lastname` ASC;" ;

$talks = $db->query( $stmtstr )->fetchAll(PDO::FETCH_OBJ);


function cmp($a, $b) { return strcmp($a->talkSlot, $b->talkSlot); }
usort($talks, "cmp");



?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../js/jquery-ui-1.12.0.custom/jquery-ui.min.css">
    <link rel="stylesheet" href="../js/jquery-ui-1.12.0.custom/jquery-ui.theme.min.css">
    <link rel="stylesheet" href="../css/layout_hack.css">
    <script src="../js/jquery-1.12.1.min.js"></script>
    <script src="../js/jquery-ui.min.js"></script>
    <script src="../js/moment.min.js"></script>

    <script src="http://cdn.jsdelivr.net/qtip2/3.0.3/jquery.qtip.min.js"></script>
    <link rel="stylesheet" href="http://cdn.jsdelivr.net/qtip2/3.0.3/jquery.qtip.min.css">

<style>
    h3 {
        margin: 0.75em 0 0.25em 0;
    }
    .data {
        padding-bottom: 0.3em;
    }
    h4.chair {
        padding: 0.1em 0 0.3em 0;
    }
    ul {
        margin: 0.5em 0 0 0;
    }
</style>


</head>
<body>
    <h1>Links to uplaods</h1>
<?php

foreach($talks as $T) {
    $id = sprintf("%03u", $T->id);
    print "<h2>{$T->lastname} // {$T->firstname} - {$id}</h2>";
    print "{$T->talkSlot} -- {$T->talkDuration}min";
    $path = './' . $id;
    if (file_exists($path)){
        print "<ul>";
        $files = array_diff(scandir($path), array('..', '.'));
        foreach($files as $f) {
            $url = $id . "/" . $f;
            print "<a href='{$url}'>$f</a>";
        }
        print "</ul>";
    }
}

?>
</body>
</html>
