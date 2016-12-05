<?php
require_once "data/events.php";



?>

<h2 id='listoftalks'>List of Talks and PDFs</h2>

<p>
    Click on the right hand side to download a pdf version of the slides or to see the
    recording where available. If you cannot see youtube videos for some reason please let me know and I'll upload the videos elsewhere.
    <br>
    Speakers please upload a pdf version of your talk in the <a href="user/login.php">user center</a>.
</p>

<?php

$fmt = 'H:i';
$cur = NULL;
$cid = 0;

$all = array_merge($presentations, $breaks);

function cmp($a, $b) {
    $fmt = 'Y-m-d\TH:i:s';
    return strcmp($a->start->format($fmt), $b->start->format($fmt));
}


usort($all, "cmp");

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
        print "<h4 class='chair'>Chairperson: " . $chairs[$cid]['chair'] . "</h4>";
        $cid += 1;
    }


    print <<<EOT
    <li>
        <span class='time'>{$p->start->format($fmt)} &ndash; {$p->end->format($fmt)}:</span>

EOT;
    if (isset($p->is_no_talk) && $p->is_no_talk) {
        print <<<EOT
        <span class="notalk">{$p->name}</span>

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
                &lsaquo;&nbsp;{$p->presentationTitle}&nbsp;&rsaquo;
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
                    <span class="mod_title">{$p->presentationTitle}</span>
                    {$p->abstract}
                </p>
            </div>
        </div>


EOT;
    }
    print "    </li>\n";
}
print "</ul>\n";

?>
