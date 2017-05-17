<?php

require_once "../lib/app.php";

$BASEURL = dirname(dirname(get_baseurl()));


print_r("post:\n");
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

        if (!$isBookingOpen) {
            echo "<h1 style='text-align:center;'>Sorry, the registration is really closed!!</h1>";
            var_dump($_POST);
            die(1);
        }


        if (! isset($_POST['email']) || strlen($_POST['email']) <6) {
            echo "<h1 style='text-align:center;'>No email in POST or too short<br>However this happend...</h1>";
            var_dump($_POST);
            die(1);
        }

        $vals = []; // values to be written in the database

        // scan though all possible database fields
        foreach ($tableFields as $key => $arr) {

            $sqltype = $arr[0];
            $type    = $arr[1];
            $default = (isset($arr[2])) ? $arr[2] : "";
            $choices = (isset($arr[3])) ? $arr[3] : NULL ;

            # echo $key. ' | '.$type. ' | '.isset($_POST[$key])."<br />\n";

            $vals[$key] = $default; // first set to default value from settings.php

            if (isset($_POST[$key])) {

                $x = $_POST[$key];

                if ($type == 'string') {
                    $vals[$key] = strval($x);
                }
                elseif ($type == "integer") {
                    $vals[$key] = intval($x);
                }
                elseif ($type == "boolean") {
                    $vals[$key] = ($x == "TRUE" ? TRUE : FALSE);
                }
                elseif ($type == "choice") {
                    if (is_numeric($x)) {
                        $vals[$key] = intval($x);
                    }
                    else {
                        $vals[$key] = intval(array_search($x, $choices, TRUE)); # if not found this returns False, which gets casted to 0, the first and default choice
                    }
                }
                elseif ($type == "datetime") {
                    $dt = new DateTime($x);
                    $vals[$key] = $dt->format($datetime_db_fstr);
                }
            }
        }
        $vals["talkCoauthors"] = $vals["lastname"] . ", " . $vals["firstname"] . " [1]; " . $vals["talkCoauthors"];
        $vals["talkCoauthorsAffil"] = $vals["affiliation"] . "\n" . $vals["talkCoauthorsAffil"];

        // override automatic fields
        $vals["log"] = $now->format($datetime_db_fstr) . "\t(webform)\tregistration\n";
        $vals["registrationDate"] = $now->format($datetime_db_fstr);

        $akey = bin2hex(openssl_random_pseudo_bytes(4)); # 4 bytes makes 8 = 2x4 hex digits
        $vals["accessKey"] = $akey;

        #print "vals";
        #var_dump($vals);

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

            file_put_contents($logfile, "writing entry: ".json_encode($vals)."\n", FILE_APPEND | LOCK_EX);

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

        $stmt = $db->prepare("SELECT * FROM {$tableName} WHERE id=:uid LIMIT 1");
        $stmt->execute([':uid' => $lastId]);
        $data = $stmt->fetch();

        _load_mailer();
        send_registration_email($data, $BASEURL);

        echo "<h1 style='text-align:center;'>Registration successful</h1>\n";

        $db = null;
        exit();
    }
}

$db = null;
#    require "lib/footer.php";
exit();

?>
