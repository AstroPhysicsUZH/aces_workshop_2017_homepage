<?php
require_once "lib/header.php";
require "../data/events.php";


#print_r($presentations);
$fmt = 'Y-m-d\TH:i:s';

$sids = [];

print "<h1>STATS</h1>\n";
print "nposters accepted: " . count($posters) . "<br>\n";

foreach($sessions as $s) {
    $sid = $s->id;
    $sids[] = $sid;

    print "<h2>".$s->shortName . ") " . $s->description . "</h2>\n";
    print "<ol>\n";

    foreach($posters as $p) {
        if ($p->assignedSession === $sid) {
            print "<li>"
                . $p->lastname . "; "
                . $p->firstname . " "
                . "[ " . $p->id . " ] "
                . "<br>"
                . $p->presentationTitle
                . "</li>\n";
        }
    }
    print "</ol>\n";
}
print "<h2>Others / uncategorized</h2>\n";
#print_r($sids);
print "<ol>\n";

foreach($posters as $p) {
    if (! in_array($p->assignedSession, $sids) ) {
        print "<li>"
            . $p->lastname . "; "
            . $p->firstname . " "
            . "[ " . $p->id . " ] "
            . "<br>"
            . $p->presentationTitle
            . "</li>\n";
    }
}
print "</ol>\n";

?>
