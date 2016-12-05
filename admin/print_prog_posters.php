<?php

require_once "lib/headerphp.php";
require_once "../data/events.php";
require_once "../lib/app.php";


$fmt = 'H:i';
$cur = NULL;
$cid = 0;

$all = array_merge($presentations, $breaks);

function cmp($a, $b) {
    $fmt = 'Y-m-d\TH:i:s';
    return strcmp($a->start->format($fmt), $b->start->format($fmt));
}


usort($all, "cmp");


?>
<html>
<head>
    <link rel="stylesheet" href="../js/jquery-ui-1.12.0.custom/jquery-ui.min.css">
    <link rel="stylesheet" href="../js/jquery-ui-1.12.0.custom/jquery-ui.theme.min.css">
    <link rel="stylesheet" href="../css/fullcalendar.min.css">
    <link rel="stylesheet" href="../css/layout_hack.css">
    <script src="../js/jquery-1.12.1.min.js"></script>
    <script src="../js/jquery-ui.min.js"></script>
    <script src="../js/moment.min.js"></script>
    <script src="../js/fullcalendar.min.js"></script>

    <script src="http://cdn.jsdelivr.net/qtip2/3.0.3/jquery.qtip.min.js"></script>
    <link rel="stylesheet" href="http://cdn.jsdelivr.net/qtip2/3.0.3/jquery.qtip.min.css">

<style>

</style>


</head>
<body>
    <h1>Posters &mdash; List</h1>
<?php

foreach($sessions as $s) {
    $sid = $s->id;
    $sids[] = $sid;

    // plen posters don't exist here
    if ($s->description == "Plenary") { continue; }

    print "<h3>".$s->shortName . ") " . $s->description . "</h3>\n";
    print "<ul class='speakers'>\n";

    foreach($posters as $p) {
        if ($p->assignedSession === $sid) {
            $pid = sprintf("%03u", $p->id);
            print <<<EOT
                <li>
                    {$p->lastname} $p->firstname <br>
                    <a class="title linked" onclick="
                        document.getElementById('mod_abstr_{$pid}').style.display = 'block';
                        ">
                        &lsaquo;&nbsp;{$p->presentationTitle}&nbsp;&rsaquo;
                    </a>

                    <div id="mod_abstr_{$pid}" class="modal_abstract" onclick="
                        document.getElementById('mod_abstr_{$pid}').style.display = 'none';
                        ">
                        <div class="container">
                            <a class="close" onclick="
                                document.getElementById('mod_abstr_{$pid}').style.display = 'none';
                                ">
                                [ close ]
                            </a>
                            <p onclick="
                                var event = arguments[0] || window.event;
                                if (event.stopPropagation) { event.stopPropagation(); }
                                else { event.cancelBubble = true; }
                                ">
                                <span class="mod_name">{$p->firstname} {$p->lastname}</span>
                                <span class="mod_aff">{$p->affiliation}</span>
                                <span class="mod_title">{$p->presentationTitle}</span>
                                {$p->abstract}
                            </p>
                        </div>
                    </div>

                </li>

EOT;
        }
    }
    print "</ul>\n";
}



?>
</body>
</html>
