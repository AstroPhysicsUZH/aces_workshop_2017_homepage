<?php

/**
    set error reporting
******************************************************************************/
$DEBUG = TRUE;

if ($DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 'On');
    ini_set('display_startup_errors',1);
} else {
    error_reporting(E_NONE);
    ini_set('display_errors', 'Off');
    ini_set('display_startup_errors',0);
}

// Set default timezone
date_default_timezone_set('UTC');
$datetime_db_fstr = 'Y-m-d\ H:i:s'; // how are datetimes represented in the database
$datetime_fstr = 'Y-m-d\ H:i';      // how to present dates with time
$date_fstr = 'Y-m-d';               // how to present dates only

/**
    setup the page layout / structure
******************************************************************************/

/*
    menu entries / pages available

    for each of this entries there must be a php file in root
    This php there is supposed to inlcude "items" to generate a page
*/
$PAGES = array(
    'home',
    'programme_slides',
    'committees',
    'registration',
    'participants',
    'accommodation',
    'transportation',
    'Q_and_A',
    'proceedings',
    'login_for_participants',
    'pictures',
    'full_program'
);

/*
    menu entries that will be printed in the menu, but are not accessible /
    grayed out, because not implemented
*/
$NOT_IMPLEMENTED_PAGES = array(
/*    'participants',  */
);

/*
    pages that exist, but are not listed in the menu
*/
$HIDDEN_PAGES = array(
    'msgs',
    'user',
    'full_program',
);


/**
    Setup the registration process
******************************************************************************/

$baseFeeReduced = 250; // conference cost for early bookers
// $baseFeeStudents = 250; // conference cost for students bookers (we didn't do this, NOT IMPLEMENTED)
$baseFeeRegular = 300; // conference cost for late bookers


$registrationOpens = new DateTime("2016-03-01 00:00:00"); // the date when early booking is over
$reducedLimitDate = new DateTime("2016-07-31 23:59:59"); // the date when early booking is over
$registrationCloses = new DateTime("2016-08-29 12:00:00"); // the date when booking is over

$dinnerFee = 100; // the price of the dinner, per person

$abstractSubmissionDate = new DateTime("2016-07-15 23:59:59");
$conferenceDinnerDate = new DateTime("2016-09-07 19:00:00");

$conferenceStartDay = new DateTime("2016-09-05 00:00:00");
$conferenceEndDay = new DateTime("2016-09-09 23:59:59");


// calculate if we still get early/reduced price
$now = new DateTime('NOW');

if ($now > $registrationOpens && $now < $registrationCloses) {
    $isBookingOpen = TRUE;
}
else {$isBookingOpen = FALSE;}

$isItEarly = $now < $reducedLimitDate;
$isItTooEarly = $now < $registrationOpens;
$isItTooLate  = $now > $registrationCloses;
$baseFee = $isItEarly ? $baseFeeReduced : $baseFeeRegular;




/**
    Setup the application & database
******************************************************************************/

// name of the csv log file
$csv_db_name     = "../db/register.csv";
$csv_db_name_abs = "db/register.csv";

// name of the sqlite database (mysql should work as well, thanks to PDO, but is not tested)
$db_address     = 'sqlite:../db/registration.sqlite3';
$db_address_abs = 'sqlite:db/registration.sqlite3';

// the table in the database to use
$tableName = "registrationTable";

// table to manage the (parallel)sessions
$sessionsTable = "sessionsTable";

$UPLOADS_DIR = "uploads";

// Which fields do you want to have in the database?

$tableFields = array(       # database table columns with key => [SQL_DATATYPE, meaning]

// personal information
    'title' => ['TEXT', 'string'],
    'firstname' => ['TEXT', 'string'],
    'lastname' => ['TEXT', 'string'],
    'email' => ['TEXT', 'string'],
    'affiliation' => ['TEXT', 'string'],
    'address' => ['TEXT', 'string'],

    'isPassive' => ['INTEGER', 'boolean'], # passive accounts are for lazy VIP that don't feel like they have to register

// options
    'needInet' => ['INTEGER', 'boolean'],
    'nPersons' => ['INTEGER', 'integer'],
    'isVeggie' => ['INTEGER', 'boolean'],
    'isImpaired' => ['INTEGER', 'boolean'],
    'lookingForRoomMate' => ['INTEGER', 'boolean'],

    'notes' => ['TEXT', 'string'],           # kind of a log for keeping track of stuff..

    'registrationDate' => ['TEXT', 'date'],
    'lastAccessDate' => ['TEXT', 'date'],
    'accessKey' => ['INTEGER', 'hex'],

    'price' => ['INTEGER', 'integer'],
    'hasPayed' => ['INTEGER', 'boolean'],
    'amountPayed' => ['INTEGER', 'integer'],
    'paymentDate' => ['INTEGER', 'date'],
    'paymentNotes' => ['TEXT', 'string'], # special notes about the payment, can be seen by the user

    'talkType' => ['INTEGER', 'choice', ['none', 'talk', 'poster']],
    'presentationTitle' => ['TEXT', 'string'],
    'coauthors' => ['TEXT', 'string'],
    'abstract' => ['TEXT', 'string'],
    'presentationCategories' => ['TEXT', 'string'],      # comma separated values of categories, NOPE should be only one!!
    'assignedSession' => ['INTEGER', 'integer'],    # id of session from sessionsTable
    'isAbstractSubmitted' => ['INTEGER', 'boolean'],
    'abstractSubmissionDate' => ['TEXT', 'date'],
    'isPresentationChecked' => ['INTEGER', 'boolean'],  # has it been considered / looked at, and descision shall be published
    'isPresentationAccepted' => ['INTEGER', 'boolean'], # ... the desicission. Tristate: None -> not decided..
    'acceptedType' => ['INTEGER', 'choice', ['none', 'talk', 'poster']], # What type of presentation will be given (talks can be downgraded to posters, posters upgraded to talks)
    'presentationSlot' => ['TEXT', 'date'],             # which timeslot, as a date -OR-
    'presentationDuration' => ['INTEGER', 'integer'],   # duration of talk, in mins
    'posterPlace' => ['TEXT', 'string'],                # where to put your poster

    'proceeding' => ['BLOB', 'file'],
    'isProceedingUploaded' => ['INTEGER', 'boolean'],
    'proceedingSubmissionDate' => ['TEXT', 'date'],
    'isProceedingAccepted' => ['INTEGER', 'boolean']
);

// create lookup tables
foreach ($tableFields as $key => $val) {
    if     ($val[1]=='boolean') { $boolTableFields[] = $key; }
    elseif ($val[1]=='choice')  { $choiceTableFields[] = $key; }
    elseif ($val[1]=='date')    { $dateTableFields[] = $key; }
    elseif ($val[1]=='hex')     { $hexTableFields[] = $key; }
}

#define("PRESENTATION_TYPE_REJECTED", -1);
define("PRESENTATION_TYPE_NONE", 0);
define("PRESENTATION_TYPE_TALK", 1);
define("PRESENTATION_TYPE_POSTER", 2);

#define("PRESENTATION_STR_REJECTED", 'REJECTED');
define("PRESENTATION_STR_NONE", '- not decided yet -');
define("PRESENTATION_STR_TALK", 'talk');
define("PRESENTATION_STR_POSTER", 'poster');

$PRESENTATION_TYPES = [
#    PRESENTATION_TYPE_REJECTED => PRESENTATION_STR_REJECTED,
    PRESENTATION_TYPE_NONE =>     PRESENTATION_STR_NONE,
    PRESENTATION_TYPE_TALK =>     PRESENTATION_STR_TALK,
    PRESENTATION_TYPE_POSTER =>   PRESENTATION_STR_POSTER
];

/*
function GET_PRES_STR( $index ) {
    if ( $index ==  PRESENTATION_TYPE_NONE ) { return PRESENTATION_STR_NONE; }
    elseif ( $index ==  PRESENTATION_TYPE_TALK ) { return PRESENTATION_STR_TALK; }
    elseif ( $index ==  PRESENTATION_TYPE_POSTER ) { return PRESENTATION_STR_POSTER; }
}
*/
?>
