<?php

/**
    set error reporting
******************************************************************************/
$DEBUG = TRUE;

$confTitle = "ACES Workshop";
$confSubTitle = "Fundamental and applied science with clocks and cold atoms in space";

$confShortTitle = "ACES Workshop Zurich 2017 ";

$confLocation = "University Zurich, Switzerland";
$confDate = "June 29 &amp; 30, 2017";
$confLocationAndTime = $confLocation . " &mdash; " . $confDate;

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
#   id and php filename     title
    array('home',          'home'),
    array('program',       'programme'),
    array('committees',    'committees'),
    array('registration',  'registration'),
    array('participants',  'participants'),
    array('accommodation', 'accommodation'),
    array('transportation','transportation'),
#    array('q_and_a',       'Q & A'),
#    array('proceedings',   'proceedings'),
    array('login',         'login'),
);

/*
    menu entries that will be printed in the menu, but are not accessible /
    grayed out, because not implemented
*/
$NOT_IMPLEMENTED_PAGES = array(
    'program',
    'participants',
    'login',
);


/*
    pages that exist, but are not listed in the menu
*/
$HIDDEN_PAGES = array(
//    'msgs',
//    'user',
//    'full_program',
);


/**
    Setup the registration process
******************************************************************************/


#$feeReducedStudent = 250;  // conference cost for early bookers
#$feeReducedRegular = 250; // conference cost for students bookers (we didn't do this, NOT IMPLEMENTED)
#$feeFullStudent    = 300;  // conference cost for late bookers
$feeFullRegular    = 0;  // conference cost for late bookers

#$feeDinnerStudent  = 100; // the price of the dinner, per person
$feeDinnerRegular  = 100; // the price of the dinner, per person


$dateRegistrationOpens          = new DateTime("2016-01-01 00:00:00"); // the date when early booking is over
#$dateReducedFeeDeadline         = new DateTime("2016-07-31 23:59:59"); // the date when early booking is over
$dateReducedFeeDeadline         = $dateRegistrationOpens;
$dateAbstractSubmissionDeadline = new DateTime("2017-05-15 23:59:59");
$dateRegistrationDeadline       = new DateTime("2017-05-30 23:59:59"); // the date when booking is over

$dateConferenceStarts  = new DateTime("2017-06-29 00:00:00");
$dateConferenceEnds    = new DateTime("2017-06-30 23:59:59");

$dateConferenceDinner  = new DateTime("2016-09-29 19:00:00");



/**
    Setup the application & database
******************************************************************************/

// name of log file
$logfile     = "../events.log";
$loggile_abs = "events.log";

// name of the sqlite database (mysql should work as well, thanks to PDO, but is not tested)
$db_address     = 'sqlite:../db/registration.sqlite3';
$db_address_abs = 'sqlite:db/registration.sqlite3';

// the table in the database to use
$tableName = "registrationTable";

// table to manage the (parallel)sessions
$sessionsTable = "sessionsTable";


$UPLOADS_DIR = "uploads";
$pages_dir = "pages";


/*
Which fields do you want to have in the database?
database table columns with
key => [SQL_DATATYPE, meaning, default, choices]

meaning is one of:
    string, integer, boolean, choice, datetime, date, time, file
choices is an array:
    [choice0_default, choice1, ...] is freiwillig and the first choice 0 is default
    they are referenced either by name or by id.
*/
$tableFields = array(

// personal information
    'title'       => ['TEXT', 'string', ""],
    'firstname'   => ['TEXT', 'string', ""],
    'lastname'    => ['TEXT', 'string', ""],
    'email'       => ['TEXT', 'string', ""],
    'affiliation' => ['TEXT', 'string', ""],
    'address'     => ['TEXT', 'string', ""],

    'isPassive'   => ['INTEGER', 'boolean', FALSE], # passive accounts are for lazy VIP that don't feel like they have to register (won't get emails)

// options
    'needsInet'   => ['INTEGER', 'boolean', FALSE], # people that don't have eduroam
    'nPersons'    => ['INTEGER', 'integer', 1], # total amount of people, incl accompaning.. >=1
    'isVeggie'    => ['INTEGER', 'boolean', FALSE],
#    'isImpaired' => ['INTEGER', 'boolean'],
#    'lookingForRoomMate' => ['INTEGER', 'boolean'],

#    'price' => ['INTEGER', 'integer'],
#    'hasPayed' => ['INTEGER', 'boolean'],
#    'amountPayed' => ['INTEGER', 'integer'],
#    'paymentDate' => ['INTEGER', 'date'],
#    'paymentNotes' => ['TEXT', 'string'], # special notes about the payment, can be seen by the user


    'wantsPresentTalk' => ['INTEGER', 'boolean', FALSE],
    'talkTitle'      => ['TEXT', 'string', ""],
    'talkCoauthors'  => ['TEXT', 'string', ""],
    'talkAbstract'   => ['TEXT', 'string', ""],

    'isTalkChecked'  => ['INTEGER', 'boolean', FALSE],  # has it been considered / looked at, and descision shall be published
    'isTalkAccepted' => ['INTEGER', 'boolean', FALSE], # ... the desicission. Only valid if isTalkChecked=True

/*
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
*/

/*
    'proceeding'               => ['BLOB', 'file'],
    'isProceedingUploaded'     => ['INTEGER', 'boolean'],
    'proceedingSubmissionDate' => ['TEXT', 'date'],
    'isProceedingAccepted'     => ['INTEGER', 'boolean']
*/


);





?>
