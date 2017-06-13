<?php

/**
 * assumes the header file is already loaded
 *
 * provides $presentations, $breaks, $posters, $specialevents,
 * only the accepted ones!
 */



/*
$stmtstr = "SELECT * FROM {$sessionsTable}";
$sessions = $db->query( $stmtstr )->fetchAll(PDO::FETCH_OBJ);

$plenary_sid = NULL;
foreach($sessions as $s) {
    if ($s->categories=="plenary") {
        $plenary_sid = $s->id;
    }
}
#print_r($sessions);
#print $plenary_sid;
*/

/* OUTDATED
$stmtstr = "SELECT
                id, title, firstname, lastname, email, affiliation,
                talkType, presentationTitle, coauthors, abstract, presentationCategories,
                assignedSession, isPresentationAccepted, acceptedType,
                presentationSlot, presentationDuration
            FROM {$tableName}
            WHERE acceptedType=" . PRESENTATION_TYPE_TALK . "
                AND isPresentationAccepted = 1
                AND presentationDuration>0
                AND presentationSlot<>''
            ORDER BY presentationSlot ASC;" ;

$presentations = $db->query( $stmtstr )->fetchAll(PDO::FETCH_OBJ);



$stmtstr = "SELECT
                id, title, firstname, lastname, email, affiliation,
                talkType, presentationTitle, coauthors, abstract, presentationCategories,
                assignedSession, isPresentationAccepted, acceptedType,
                presentationSlot, presentationDuration
            FROM {$tableName}
            WHERE acceptedType=".PRESENTATION_TYPE_POSTER."
                AND isPresentationAccepted = 1
                ORDER BY lastname ASC;" ;

$posters = $db->query( $stmtstr )->fetchAll(PDO::FETCH_OBJ);

*/

$stmtstr = "SELECT
                id, title, firstname, lastname, email, affiliation,
                talkTitle, talkCoauthors, talkCoauthorsAffil, talkAbstract,
                isTalkChecked, isTalkAccepted, talkSlot, talkDuration
            FROM {$tableName}
            WHERE wantsPresentTalk=1;
            ORDER BY lastname ASC;" ;

$talkSubmissions = $db->query( $stmtstr )->fetchAll(PDO::FETCH_OBJ);


$stmtstr = "SELECT
                id, title, firstname, lastname, email, affiliation,
                talkTitle, talkCoauthors, talkCoauthorsAffil, talkAbstract,
                isTalkChecked, isTalkAccepted, talkSlot, talkDuration
            FROM {$tableName}
            WHERE wantsPresentTalk=1 AND isTalkAccepted=1;
            ORDER BY lastname ASC;" ;

$talks = $db->query( $stmtstr )->fetchAll(PDO::FETCH_OBJ);
$presentations = $talks;


$chairs_ = [
    '2017-06-29T08:59:00' => "U. Schreiber",
    '2017-06-29T13:59:00' => "L. Cacciapuoti",
    '2017-06-30T08:29:00' => "P. Wolf",
    '2017-06-30T13:59:00' => "Ch. Salomon",
];
$chairs = [];
foreach($chairs_ as $date =>$chair) {
    $chairs[] = ['date'=>new DateTime($date),'chair'=>$chair];
}


$breaks_list = [
    ['Coffee Break', '2017-06-29T10:30:00', '2017-06-29T11:00:00', ''],
    ['Coffee Break', '2017-06-29T16:00:00', '2017-06-29T16:30:00', ''],
    ['Coffee Break', '2017-06-30T10:30:00', '2017-06-30T11:00:00', ''],
    ['Coffee Break', '2017-06-30T15:20:00', '2017-06-30T15:40:00', ''],

    ['Lunch Break', '2017-06-29T12:30:00', '2017-06-29T14:00:00', ''],
    ['Lunch Break', '2017-06-30T12:40:00', '2017-06-30T14:00:00', ''],
];

$special_events_list = [
    ['Registraton opens', '2017-06-29T08:00:00', '2017-06-29T08:45:00', ''],
    ['Welcome Talk', '2017-06-29T08:45:00', '2017-06-29T09:00:00', ''],
    ['Dinner', '2017-06-29T19:30:00', '2017-06-29T22:30:00', ''],
    ['Discussion + Final Remarks', '2017-06-30T16:40:00', '2017-06-30T17:00:00', ''],
];


/*
*/




# group by day
foreach($talks as $p) {
    #print_r($p);

    $p->name = substr($p->firstname,0,1) . ". " . $p->lastname;
    $p->start = new DateTime($p->talkSlot);
    try {
        $dur = new DateInterval('PT'.$p->talkDuration.'M');
    }
    catch (Exception $e) {
        $dur = new DateInterval('PT'.'15'.'M');
        echo "<!-- error with duration of {$p->id} -->\n";
    }
    $end = new DateTime($p->talkSlot);
    $p->end = $end->add($dur);
    $p->is_plenary = TRUE;
    #print_r($p);
}


/*
$chairs_ = [
    '2016-09-05T08:00:00' => "M. Colpi",
    '2016-09-05T13:30:00' => "O. Jennrich",

    '2016-09-06T08:00:00' => "P. McNamara",
    '2016-09-06T13:30:00' => "S. Vitale",

    '2016-09-07T08:00:00' => "C. Sopuerta",
#    '2016-09-07T13:30:00' => "O. Jennrich",

    '2016-09-08T08:00:00' => "P. Binetruy",
    '2016-09-08T13:30:00' => "Ch. Caprini",

    '2016-09-09T08:00:00' => "G. Mueller",
];

$chairs = [];
foreach($chairs_ as $date =>$chair) {
    $chairs[] = ['date'=>new DateTime($date),'chair'=>$chair];
}


$breaks_list = [
    ['Coffee Break', '2016-09-05T10:30:00', '2016-09-05T11:00:00', ''],
    ['Coffee Break', '2016-09-06T10:30:00', '2016-09-06T11:00:00', ''],
    ['Coffee Break', '2016-09-07T10:30:00', '2016-09-07T11:00:00', ''],
    ['Coffee Break', '2016-09-08T10:30:00', '2016-09-08T11:00:00', ''],
    ['Coffee Break', '2016-09-09T10:30:00', '2016-09-09T11:00:00', ''],

    ['Coffee Break', '2016-09-05T16:30:00', '2016-09-05T17:00:00', ''],
    ['Coffee Break', '2016-09-06T16:30:00', '2016-09-06T17:00:00', ''],
    ['Coffee Break', '2016-09-07T15:10:00', '2016-09-07T15:30:00', ''],
    ['Coffee Break', '2016-09-08T16:30:00', '2016-09-08T17:00:00', ''],

    ['Lunch Break', '2016-09-05T13:00:00', '2016-09-05T14:30:00', ''],
    ['Lunch Break', '2016-09-06T13:00:00', '2016-09-06T14:30:00', ''],
    ['Lunch Break', '2016-09-07T13:00:00', '2016-09-07T14:00:00', ''],
    ['Lunch Break', '2016-09-08T13:00:00', '2016-09-08T14:30:00', ''],
    ['Lunch Break', '2016-09-09T13:00:00', '2016-09-09T14:30:00', ''],
];

$special_events_list = [

    ['Posters', '2016-09-05T14:00:00', '2016-09-05T14:30:00', ''],
    ['Posters', '2016-09-06T14:00:00', '2016-09-06T14:30:00', ''],
    ['Posters', '2016-09-08T14:00:00', '2016-09-08T14:30:00', ''],

    ['Registraton opens', '2016-09-05T08:00:00', '2016-09-05T08:45:00', ''],
    ['Welcome Talk', '2016-09-05T08:45:00', '2016-09-05T09:00:00', ''],
    ['Questions', '2016-09-06T16:00:00', '2016-09-06T16:30:00', ''],
    ['Joint eLISA and L3ST consortium meeting', '2016-09-07T14:00:00', '2016-09-07T17:00:00', ''],

    ['Hike to Dinner', '2016-09-07T17:30:01', '2016-09-07T18:30:00', join('<br>',[
        'Hike to diner place',
        '(in case of nice weather, ask Rafael)',
        '<hr>' .
        'Meetingpoint 2: Tram station "Triemli"',
        '(end station of nr 14)' ,
        'Meeting time: 17:45',
        '<hr>'.
        'Duration: 1h',
        'Ascent: 400m',
        'Length: 4km',
        'HikeNr: 47',
        'Link: http://www.wanderland.ch/de/routen/etappe-01211.html' ])
    ],
    ['Transport to Dinner', '2016-09-07T17:40:00', '2016-09-07T18:25:00', join('<br>',[
        'Public transport',
        '<hr>' .
        'Tram Nr 14:',
        'from: 17:38 Milchbuck',
        'to: 17:50 Bahnhofplatz/HB',
        '<hr>' .
        'Train S10 on Track 22:',
        'from: 18:05 Zurich HB SZU',
        'to: 18:25 Uetliberg'])
    ],
    ['Apero & Dinner', '2016-09-07T18:45:00', '2016-09-07T22:30:00', ''],
    ['Farewell Talk', '2016-09-09T12:30:00', '2016-09-09T12:45:00', 'by K. Danzmann'],
];
*/

$breaks = [];
foreach($breaks_list as $bl) {
    $breaks[] = (object) [
        'name' => $bl[0],
        'start' => new DateTime($bl[1]),
        'end' =>   new DateTime($bl[2]),
        'description' => $bl[3]
    ];
};
foreach ($breaks as $b) {
    $b->is_break = TRUE;
    $b->is_no_talk = TRUE;
}


$specialevents = [];
foreach($special_events_list as $se) {
    $specialevents[] = (object) [
        'name' => $se[0],
        'start' => new DateTime($se[1]),
        'end' =>   new DateTime($se[2]),
        'description' => $se[3]
    ];
};
foreach ($specialevents as $se) {
    $se->is_special = TRUE;
    $se->is_no_talk = TRUE;
}

?>
