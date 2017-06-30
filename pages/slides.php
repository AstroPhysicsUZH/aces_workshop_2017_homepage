<?php

require_once "lib/app.php";
$db = open_db();
require_once "data/events.php";


$fmt = 'H:i';
$cur = NULL;
$cid = 0;

$talks = array_merge($presentations); #, $breaks, $specialevents);

function cmp($a, $b) {
    $fmt = 'Y-m-d\TH:i:s';
    return strcmp($a->start->format($fmt), $b->start->format($fmt));
}

usort($talks, "cmp");

?>

<h1>Slides</h1>

<p>
    Please upload your slide as a <b>pdf only</b> in the <a href="user/">user center</a>
</p>

<ul>

<?php

foreach($talks as $T) {
    $id = sprintf("%03u", $T->id);
    $slot = (new DateTime($T->talkSlot))->format("d. M H:i");
    print "  <li>{$T->firstname} {$T->lastname} &ndash; {$slot}\n";
    $path = './uploads/' . $id;
    $pathpdf = $path . "/*.pdf";

    $cc = FALSE;
    if (file_exists($path)){
        $files = glob($pathpdf);
        if (count($files)>0) {
            print "  <ul>\n";
            foreach ($files as $filepath) {
                $filename = basename($filepath);
                print "    <li> <a href='{$filepath}'>$filename</a> </li>\n";
            }
            print "  </ul>\n";
        }

        // $files = array_diff(scandir(), array('..', '.'));
        // foreach($files as $f) {
        //     if (!$cc) { print "  <ul>\n"; }
        //     $info = pathinfo($f);
        //     if ($info['extension']=="pdf") {
        //         $url = $id . "/" . $f;
        //         print "    <li> <a href='{$url}'>$f</a> </li>\n";
        //     }
        //     if (!$cc) { print "  </ul>\n"; $cc = TRUE; }
        // }
    }
}

?>
</ul>
</body>
</html>
