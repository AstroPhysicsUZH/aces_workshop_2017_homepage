<?php
require_once "lib/header.php";
require "../data/events.php";

function print_subm($p){
    $pid = sprintf("%03u", $p->id);
    $name = substr($p->firstname,0,1) . ". " . $p->lastname;

    print "<span class='pid small'><code>[{$pid}]</code></span> ";
    print "<span class='name'>{$name}</span> ";
    print "&mdash; <span class='title'>{$p->presentationTitle}</span>";
    print "<br>\n";
}

?>
<h1>Report for all submissions</h1>
<?php


print "<h2><a name='tid'>rejected submissions</a></h2><br>\n";

foreach ($sessions as $s) {
    $sid = $s->id;
    print "<h3>".$s->shortName . ") " . $s->description . "</h3><br>\n";
    $i = 0;
    foreach($all_submissions as $sub) {
        #print $sub->isPresentationAccepted;
        if ( isset($sub->isPresentationAccepted)
            && $sub->isPresentationAccepted==FALSE
            && $sub->assignedSession == $sid) {
            $i += 1;
            print_subm($sub);
        }
    }
    print "<p><b>TOTAL: {$i}</b></p>\n<hr>\n";
}



foreach($PRESENTATION_TYPES as $tid => $typestr) {

    print "<h2><a name='tid" . ($tid+1) . "'>" . $typestr ."</a></h2><br>\n";

    foreach ($sessions as $s) {
        $sid = $s->id;
        print "<h3>".$s->shortName . ") " . $s->description . "</h3><br>\n";
        $i = 0;
        foreach($all_submissions as $sub) {
            # print_r($sub);
            if (   $sub->acceptedType == $tid
                && $sub->assignedSession == $sid) {
                $i += 1;
                print_subm($sub);
                #print $sub->id . "<br>\n";
            }
        }
        print "<p><b>TOTAL: {$i}</b></p>\n<hr>\n";
    }

}

print "<hr>\n";
print "<h2><a name='tid" . (98) . "'>[[ INVALID PRESENTAION TYPE ]]</a></h2><br>\n";

foreach ($sessions as $s) {
    print "<h3>".$s->shortName . ") " . $s->description . "</h3><br>\n";
    foreach($all_submissions as $sub) {

        if ( array_key_exists($sub->acceptedType, $PRESENTATION_TYPES) ) { continue; }
        $sid = $s->id;
        if ($sub->assignedSession == $sid) {
            print_subm($sub);
        }
    }
}

print "<hr>\n";
print "<h2><a name='tid" . (99) . "'>[[ NO ASSIGNED SESSION ]]</a></h2><br>\n";

foreach($all_submissions as $sub) {
    if ( ! intval($sub->talkType) == PRESENTATION_TYPE_NONE
         && ! is_null($sub->assignedSession) ) { continue; }
    print_subm($sub);
    print "<!--\n";
    print_r($sub);
    print "-->\n";
}


?>
