<?php
require "data/events.php";

#print_r($presentations);
$fmt = 'Y-m-d\TH:i:s';

$sids = [];

?>

<h2 id='listofposters'>List of Posters, Slides and Recordings</h2>
<!--<p class="small">(in no particular order)</p>-->

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
            $links = "";
            if (isset($all_files[$pid])) {
                $links = '<span class="dllnks">[ <a href="'.$all_files[$pid].'">pdf</a> ]</span>';
            }

            print <<<EOT
                <li>
                    <span>{$p->lastname} $p->firstname</span>
                    $links
                    <br>
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
