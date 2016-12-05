
<h1>Full Program</h1>


<?php
require_once "lib/app.php";
$db = open_db($db_address_abs);

require "data/events.php";



$fmt = 'H:i';
$cur = NULL;

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
        print "<h2>$day</h2>\n";
        print "<ul class='fullprog'>\n";
        $cur = $day;
    }
    #print "<!--  " . $day . "  " . $cur . "-->\n";

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
        print <<<EOT
        <span class="authortitle">
            <span class="author">{$p->name}</span>
            &mdash;
            <span class="title">{$p->presentationTitle}</span>
        </span>

EOT;
    }
    print "    </li>\n";
}
print "</ul>\n";

?>
