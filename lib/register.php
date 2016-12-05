<?php

/**
    we send post requests for registration to here.
    will write the data to the db and notify admins by email
**/

require_once "../lib/app.php";

$rtest = intval($_POST['robot']);
$rtest = 37;
// print_r($_POST);

if (!$isBookingOpen) {
    echo "<h1 style='text-align:center;'>Yes, the registration is really closed!!</h1>";
    var_dump($_POST);
    die(1);
}

if ($rtest != 37) {
    echo "<h1 style='text-align:center;'>You did not pass the robot test.<br> Please just enter the number 37..</h1>";
    var_dump($_POST);
    die(1);
}

if (! isset($_POST['email']) || strlen($_POST['email']) <6) {
    echo "<h1 style='text-align:center;'>No email in POST or too short<br>However this happend...</h1>";
    var_dump($_POST);
    die(1);
}


/*
    init to default values
    because checkboxes only give a value if checked..
    all others could be set to null

    make sure here are only fields that settings.php defines!!
*/
$vals = [
    "title" => "",
    "firstname" => "",
    "lastname" => "",
    "email" => "",
    "affiliation" => "",
    "address" => "",

    "needInet" => FALSE,
    "nPersons" => 1,
    "isVeggie" => FALSE,
    "isImpaired" => FALSE,
    "lookingForRoomMate" => FALSE,

    "hasPayed" => FALSE,

    "talkType" => 0,
    'isAbstractSubmitted' => NULL,
];

// read post fields
foreach ($tableFields as $key => $arr) {
    $sqltype = $arr[0];
    $type = $arr[1];
    $choices = (isset($arr[2])) ? $arr[2] : NULL ;

    if (isset($_POST[$key])) {
        $x = $_POST[$key];
        if ($type == 'string') {
            $vals[$key] = strval($x);
        }
        elseif ($type == "integer") {
            $vals[$key] = intval($x);
        }
        elseif ($type == "boolean") {
            $vals[$key] = boolval($x);
        }
        elseif ($type == "choice") {
            $vals[$key] = intval(array_search($x, $choices, TRUE)); # if not found this returns False, which gets casted to 0, the first and default choice
        }
        elseif ($type == "date") {
            $dt = new DateTime($x);
            $vals[$key] = $dt->format($datetime_db_fstr);
        }
    }
    else { # if no default value is set, set to null
        if (!isset($vals[$key])) {
            $vals[$key] = NULL;
        }
    }
}

// fill in other fields

$vals["nPersons"] += 1; # convert from nAdditionalPersons to nPersons
$vals["notes"] = $now->format($datetime_db_fstr) . "\tregistered\n";
$vals["price"] = $baseFee + $dinnerFee * $vals["nPersons"];
$vals["registrationDate"] = $now->format($datetime_db_fstr);
$vals["lastAccessDate"] = NULL;
$vals["hasPayed"] = FALSE;


try {
    // Create (connect to) SQLite database (creates if not exists)
    $db = open_db();

    // check if this email address already exists
    $findEmail = $vals['email'];
    $stmt = $db->prepare("SELECT COUNT(*) FROM {$tableName} WHERE email = :email");
    $res = $stmt->execute(array(':email'=>$findEmail));
    $nEntries = $stmt->fetchColumn();

    if ($nEntries > 0) {
        echo "<h1 style='text-align:center;'>This email address is already registered</h1>\n";
        //var_dump($_POST);
        die(1);
    }

    // generate unique access key
    $repeat = TRUE;
    while ($repeat) {
        $akey = bin2hex(openssl_random_pseudo_bytes(4)); # 4 bytes makes 8 = 2x4 hex digits
        $stmt = $db->prepare("SELECT COUNT(*) FROM {$tableName} WHERE accessKey = :akey");
        $res = $stmt->execute(array(':akey'=>$akey));
        if ( $stmt->fetchColumn() == 0 ) { # if no entries found, we got a valid one
            $vals["accessKey"] = $akey;
            $repeat = FALSE;
        }
    }

    //var_dump(array_keys($tableFields));
    //echo "\n<hr />\n";
    //var_dump(array_keys($vals));


    $insert  = "INSERT INTO {$tableName} (";
    $insert .= implode(", ", array_keys($tableFields));
    $insert .= ") VALUES ( ";
    $insert .= implode(", ", array_map(function($value) { return ':'.$value; }, array_keys($tableFields)));
    $insert .= ");";

    $stmt = $db->prepare($insert);
    $stmt->execute($vals);
    $lastId = $db->lastInsertId();

    // Close file db connection
    $db = null;
}
catch(PDOException $e) {
    // Print PDOException message
    echo "<h1 style='text-align:center;'>
        Something went wrong with the database..
        </h1>\n";
    echo $e->getMessage();
    echo '<br>';
    var_dump($e->getTraceAsString());
    die(1);
}

/*
    for debug, append the data to a csv as well
*/
// insert id in front
$vals = ['id' => $lastId] + $vals;

$csv = new parseCSV();
$csv->sort_by = 'email';
$csv->parse($csv_db_name);
$csv->data[$vals['email']] = $vals;
$csv->save();
#print_r($csv->data);



$from = '"LISA Symposium Website" <rafik@physik.uzh.ch>';
$replyto = $from;
$to1 = "rafael.kueng@uzh.ch";
$to2 = "relativityUZH@gmail.com";

$subj = "[LISA] registration";

$headers  = 'From: ' . $from . "\r\n";
$headers .= 'Reply-To:' . $replyto . "\r\n";
$headers .= 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/plain; charset=UTF-8' . "\r\n";
$headers .= 'X-Mailer: PHP/' . phpversion() . "\r\n";
$headers .= 'Delivery-Date: ' . date("r") . "\r\n";

$msg  = "Someone registered for the lisa conference. Here a backup dump\r\n\r\n";
if ($vals['lookingForRoomMate']){
    $msg .= "ADD TO EMAIL LIST! : {$vals['email']} \r\n\r\n";
}
$msg .= "PHP var export:\r\n";
$msg .= var_export($vals, true);
$msg .= "\r\n\r\n";
$msg .= "json encoded:\r\n";
$msg .= chunk_split(json_encode($vals), 76, "\r\n");

mail($to1, $subj, $msg, $headers);
mail($to2, $subj, $msg, $headers);

/*
    load the email message to send
    this should set:
    $from
    $replyto
    $subject
    $message
    expects the database fields in $X
*/
$X = $vals;
require "../items/registration_email.php";

$headers  = "";
$headers .= 'From: ' . $from . "\r\n";
$headers .= 'Reply-To:' . $replyto . "\r\n";
$headers .= 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/plain; charset=UTF-8' . "\r\n";
$headers .= 'X-Mailer: PHP/' . phpversion() . "\r\n";
$headers .= 'Delivery-Date: ' . date("r") . "\r\n";

mail($vals['email'], $subject, $message, $headers);

/*
    Now follows a redirect to a message page with a database dump to confirm
    Only send user id and accesskey for security.
*/
?>

<html>
<head>
</head>
<body>
    <h1>processing registration...</h1>
    <form action='../index.php?page=msgs&amp;mid=reg_suc' method='post' name='frm'>
<?php
$senddata = [
    "id"        => $vals['id'],
    "accessKey" => $vals['accessKey'],
];
foreach ($senddata as $a => $b) {
    echo "<input type='hidden' name='".htmlentities($a)."' value='".htmlentities($b)."'>\n";
}
?>
        <input type="submit" value="Click here if you are not redirected."/>
    </form>
    <script type="text/javascript">
        document.frm.submit();
    </script>
</body>
</html>
