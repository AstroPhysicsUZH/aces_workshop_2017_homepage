<?php

require_once "lib/app.php";


$reg_spkrs_ids = [
    18, # Peter Wolf
    12, # Luigi Cacciapuoti
    4,  # Christophe Salomon
    25, # Flury', 'Jacob.
    32, # Rodrigues, Manuel
    53, # Guerlin, Christine
];

$miss_spkrs = [
    [
        'Laemmerzahl', 'Claus',
        'ZARM, Bremen, Germany',
        '',
        ''
    ],
    [
        'Schnatz', 'Harald',
        'PTB, Braunschweig, Germany',
        '',
        ''
    ],
];


$speakers = [];

$db = open_db();

foreach ($reg_spkrs_ids as $id) {
    $p = get_participant_id($db, $id);
    $key = $p['lastname'] . $p['firstname'];
    $speakers[$key] = $p;
}
foreach ($miss_spkrs as $s) {
    $key = $s[0] . $s[1];
    $speakers[$key] = [
        "lastname" => $s[0],
        "firstname" => $s[1],
        "affiliation" => $s[2],
        "talkTitle" => $s[3],
        "talkAbstract" => $s[4],
    ];
}

ksort($speakers);
#var_dump($speakers);

function has_abstract($s) {
    return !is_null($s['talkAbstract']) && strlen($s['talkAbstract']) >0;
}
function has_title($s) {
    return !is_null($s['talkTitle']) && strlen($s['talkTitle']) >0;
}

function print_entry($k, $s) {
    $stack = [];
    $stack[] = ['li', "class='speaker'"];
    $stack[] =
        ["span","",
        "<span class='name'>{$s['firstname']} {$s['lastname']}</span> " .
        "<span class='affiliation'>({$s['affiliation']})</span>"
    ];
    if (has_abstract($s)) {
        $stack[] = [
            'a',
            "class='linked' onclick=\"document.getElementById('mod_abstr_{$k}').style.display = 'block';\""
        ];
    }
    if (has_title($s)) {
        $stack[] = ["span", "class='talkTitle'", "{$s['talkTitle']}"];
    }

    $str = "";
    while ($s = array_pop($stack)) {
        array_push($s, "", "", "");
        if (strlen($s[1])>0) {$s[1]=" ".$s[1];}
        $str = "<{$s[0]}{$s[1]}>{$s[2]}$str{$s[3]}</{$s[0]}>";
    }
    print $str . "\n";
}


function print_overlay($k, $s) {
    print <<<EOT
    <div id="mod_abstr_{$k}" class="modal_abstract" onclick="
        document.getElementById('mod_abstr_{$k}').style.display = 'none';
        ">
        <div class="container">
            <a class="close" onclick="
                document.getElementById('mod_abstr_{$k}').style.display = 'none';
                ">
                [ close ]
            </a>
            <p onclick="
                var event = arguments[0] || window.event;
                if (event.stopPropagation) { event.stopPropagation(); }
                else { event.cancelBubble = true; }
                ">
                <span class="mod_name">{$s['firstname']} {$s['lastname']}</span>
                <span class="mod_aff">{$s['affiliation']}</span>
                <span class="mod_title">{$s['talkTitle']}</span>
                {$s['talkAbstract']}
            </p>
        </div>
    </div>
EOT;
}

?>


<h1>Programme</h1>
<p>
    We are happy to announce the final program. Please note that small last minute changes might still be possible, this page however will always represent the latest version.
</p>
<p>
    All the talks will take place in <b>Zurich at University Irchel, in lecture room Y15-G-20</b>. You can find <a href="?page=transportation">directions here</a>.
</p>

<h2>Final Program - List</h2>
<div class='speakers_abstracts'>
<?php require "items/list_of_talks.php"; ?>
</div>

<h2>Final Program - Schedule</h2>
<?php require "items/program.php"; ?>

<?php
/*
<style>
li { margin-bottom: 0.5em; }
</style>

<h2>Preliminary Speakers</h2>
<p class="small">
    Click on the title for the abstract
</p>

<ul class="speakers">
<?php foreach ($speakers as $k=>$s) {print_entry($k, $s);}?>
</ul>

<div class='speakers_abstracts'>
<?php foreach ($speakers as $k=>$s) { if (has_abstract($s)) {print_overlay($k, $s);}}?>
</div>

*/?>

<h2>Conference Dinner</h2>
<p>
    The dinner will take place in <a href="http://www.lasalle-restaurant.ch/en/restaurant/">LaSalle, Zurich</a>.
</p>
