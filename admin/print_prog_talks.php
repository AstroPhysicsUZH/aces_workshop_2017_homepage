<?php

require_once "lib/headerphp.php";
require_once "../data/events.php";
require_once "../lib/app.php";


$fmt = 'H:i';
$cur = NULL;
$cid = 0;

$all = array_merge($presentations, $breaks, $specialevents);

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
    <h1>Program &mdash; List</h1>
<?php

foreach($all as $p) {
    #print "//{$p->id}";
    #if (!$p->is_plenary) { continue; }
    $day = $p->start->format('l jS \of F Y');



    if (!($day==$cur)) {
        if (!is_null($cur)){print "</ul>\n";}
        print "<h3>$day</h3>\n";
        print "<ul class='prog speakers'>\n";
        $cur = $day;
    }
    #print "<!--  " . $day . "  " . $cur . "-->\n";
    if (count($chairs) > $cid && $p->start > $chairs[$cid]['date']) {
        print "<h4 class='chair'>Chair: " . $chairs[$cid]['chair'] . "</h4>";
        $cid += 1;
    }


    print <<<EOT
    <li>
        <span class='time'>{$p->start->format($fmt)} &ndash; {$p->end->format($fmt)}:</span>

EOT;
    if (isset($p->is_break) && $p->is_break) {
        print <<<EOT
        <span class="data notalk">{$p->name}</span>

EOT;
    }
    elseif (isset($p->is_special) && $p->is_special) {
        print <<<EOT
        <span class="data special">{$p->name}</span>

EOT;
    }
    else {
        $pid = sprintf("%03u", $p->id);
        $files = isset($all_files[$pid]) ? $all_files[$pid] : NULL;
        $video = isset($all_videos[$pid]) ? $all_videos[$pid] : NULL;

#        <span class="affil">({$p->affiliation})</span>
        print <<<EOT
        <span class='data'>
            <span class="author">{$p->name}</span>

EOT;
        if ($files || $video) {
            print ' <span class="dllnks"> [ ';
            if ($files) {
                print '                <a href="'.$files.'">slides</a> ';
            }
            if ($files && $video) { print " | ";}
            if ($video) {
                print '                <a href="'.$video.'">recording</a>';
            }
            print ' ]</span>';
        }

        print <<<EOT


            <a class="title linked" onclick="
                document.getElementById('mod_abstr_{$pid}').style.display = 'block';
                ">
                &lsaquo;&nbsp;{$p->talkTitle}&nbsp;&rsaquo;
            </a>

EOT;
/*
        if ($files || $video) {
            print '            <span class="icons">';
            if ($files) {
                print '                <a href="'.$files[0].'"><!--<img class="icon" src="img/x-office-presentation.png" alt="pdf">--> slides</a>';
            }
            if ($video) {
                print '                <a href="'.$video.'"><img class="icon" src="img/video-x-generic.png" alt="youtube"> recording</a>';
            }
            print '            </span>';
        }
*/
        print <<<EOT
        </span>

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
                    <span class="mod_title">{$p->talkTitle}</span>
                    {$p->talkAbstract}
                </p>
            </div>
        </div>


EOT;
    }
    print "    </li>\n";
}
print "</ul>\n";

?>
</body>
</html>
