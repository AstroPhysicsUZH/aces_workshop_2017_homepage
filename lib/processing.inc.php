<?php

/**
  Here we do some general stuff with the settings and add hardcoded things..
  usually no need to change anything in here
*******************************************************************************/


// administrative fields, don't change
$tableFields = array_merge($tableFields, array(

  'registrationDate' => ['TEXT', 'date'],
  'lastAccessDate'   => ['TEXT', 'date'],
  'accessKey'        => ['INTEGER', 'hex'],

  'log'              => ['TEXT', 'string'],  # kind of a log for keeping track of stuff..

));


if ($DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 'On');
    ini_set('display_startup_errors',1);
} else {
    error_reporting(E_NONE);
    ini_set('display_errors', 'Off');
    ini_set('display_startup_errors',0);
}


// calculate if we still get early/reduced price
$now = new DateTime('NOW');

if ($now > $registrationOpens && $now < $registrationCloses) {
    $isBookingOpen = TRUE;
}
else {$isBookingOpen = FALSE;}

$isItEarly = $now < $reducedLimitDate;
$isItTooEarly = $now < $registrationOpens;
$isItTooLate  = $now > $registrationCloses;


$feeStudent = $isItEarly ? $feeReducedStudent : $feeFullStudent;
$feeRegular = $isItEarly ? $feeReducedRegular : $feeFullRegular;



// create lookup tables
foreach ($tableFields as $key => $val) {
    if     ($val[1]=='boolean') { $boolTableFields[] = $key; }
    elseif ($val[1]=='choice')  { $choiceTableFields[] = $key; }
    elseif ($val[1]=='date')    { $dateTableFields[] = $key; }
    elseif ($val[1]=='hex')     { $hexTableFields[] = $key; }
}



?>
