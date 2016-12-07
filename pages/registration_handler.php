<?php
if (!$appIsLoaded) {
    require_once "../lib/app.php";
}

print_r($_POST);

if (!empty($_POST)) {

    if (array_key_exists("action", $_POST)) {
        $action = $_POST["action"];
        unset($_POST["action"]);
    }
    else {
        $action="";
    }

    $rtest  = intval($_POST['robot']);
    $rcheck = intval(substr($_POST['check'],1,1));
    if ($rtest != $rcheck) {
        echo "<h1 style='text-align:center;'>You did not pass the robot test.<br> Please just enter the digit in the middle..<br />
        like for 384 enter 8</h1>";
        var_dump($_POST);
        die(1);
    }

    if ($action=="save") {
/*
        if (!$isBookingOpen) {
            echo "<h1 style='text-align:center;'>Sorry, the registration is really closed!!</h1>";
            var_dump($_POST);
            die(1);
        }
*/
/*
        if (! isset($_POST['email']) || strlen($_POST['email']) <6) {
            echo "<h1 style='text-align:center;'>No email in POST or too short<br>However this happend...</h1>";
            var_dump($_POST);
            die(1);
        }
*/
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

            "isPassive" => FALSE,

            "needInet" => FALSE,
            "nPersons" => 1,
            "isVeggie" => FALSE,

            'wantsPresentTalk' => FALSE,
            'talkTitle'     => "",
            'talkCoauthors' => "",
            'talkAbstract'  => "",

            'isTalkChecked'  => FALSE,  # has it been considered / looked at, and descision shall be published
            'isTalkAccepted' => FALSE, # ... the desicission. Only valid if
         ];

        // read post fields
        foreach ($tableFields as $key => $arr) {
            $sqltype = $arr[0];
            $type = $arr[1];
            $choices = (isset($arr[2])) ? $arr[2] : NULL ;

            # echo $key. ' | '.$type. ' | '.isset($_POST[$key])."<br />\n";

            if (isset($_POST[$key])) {
                $x = $_POST[$key];
                # echo $x . "\n";
                if ($type == 'string') {
                    $vals[$key] = strval($x);
                }
                elseif ($type == "integer") {
                    $vals[$key] = intval($x);
                }
                elseif ($type == "boolean") {
                    $vals[':'.$key] = boolval($x);
                }
                elseif ($type == "choice") {
                    if (is_numeric($x)) {
                        $vals[$key] = intval($x);
                    }
                    else {
                        $vals[$key] = intval(array_search($x, $choices, TRUE)); # if not found this returns False, which gets casted to 0, the first and default choice
                    }
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

        $vals["log"] = $now->format($datetime_db_fstr) . "\t(webform)\tregistration\n";
        $vals["registrationDate"] = $now->format($datetime_db_fstr);
        $vals["lastAccessDate"] = NULL;

        $akey = bin2hex(openssl_random_pseudo_bytes(4)); # 4 bytes makes 8 = 2x4 hex digits
        $vals["accessKey"] = $akey;

        var_dump($vals);

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

            $insert  = "INSERT INTO {$tableName} (";
            $insert .= implode(", ", array_keys($tableFields));
            $insert .= ") VALUES ( ";
            $insert .= implode(", ", array_map(function($value) { return ':'.$value; }, array_keys($tableFields)));
            $insert .= ");";

            $stmt = $db->prepare($insert);
            $stmt->execute($vals);
            $lastId = $db->lastInsertId();
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

        $res = null;
        $stmt = null;
        print "DONE\n";
    }
}

$db = null;
#    require "lib/footer.php";
exit();

?>
