<?php
require "lib/headerphp.php";
require "../lib/app.php";


// $db is already open!

if (!empty($_POST)) {

    #print "<h1>Processing changes</h1>";
    #print_r($_POST);

    if (array_key_exists("action", $_POST)) {

        $action = $_POST["action"];
        unset($_POST["action"]);

        if ($action=="save") {



            if (! isset($_POST['email'])) {
                echo "no email in POST";
                var_dump($_POST);
                die(1);
            }

            var_dump($_POST);
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
                "isPassive" => TRUE,

                "needInet" => FALSE,
                "nPersons" => 1,
                "isVeggie" => FALSE,
                "isImpaired" => FALSE,
                "lookingForRoomMate" => FALSE,

                "hasPayed" => TRUE,
                "price" => 0,

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

            $vals["notes"] = $now->format($datetime_db_fstr) . "\t{$_SESSION["username"]}\twas registered\n";
            $vals["registrationDate"] = $now->format($datetime_db_fstr);
            $vals["lastAccessDate"] = NULL;


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
}

// ----------------------------------------------------------------------------


require "lib/headerhtml.php";

$all_sessions = $db->query( "SELECT * FROM {$sessionsTable}")->fetchAll(PDO::FETCH_OBJ);

?>



<h1>Add Entry</h1>
<ul class="pagemenu">
    <li><a href='#entry'>entry</a></li>
    <li><a href='#overview'>overview</a></li>
</ul>

<p class="warn">
    Keep your eyes open, this page has NO sanity checks and writes direclty to the database!
</p>

<h2 id='entry'>Entry</h2>

<form id="frm_edit"
      action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>"
      method="post">
    <table class="edit">
        <thead>
            <th colspan="2">
                <b>Personal Details</b>
            </th>
        </thead>
        <tr>
            <td><label for="title" class="left">Title</label></td>
            <td>
                <input id="title" type="text" name="title" placeholder="- / PhD / Dr / Prof" value="">
            </td>
        </tr>
        <tr>
            <td><label for="firstname" class="left">First name</label></td>
            <td>
                <input id="firstname" type="text" name="firstname" required placeholder="Enter First Name" value="">
            </td>
        </tr>
        <tr>
            <td><label for="lastname" class="left">Last name</label></td>
            <td>
                <input id="lastname" type="text" name="lastname" required placeholder="Enter Last Name"  value="">
            </td>
        </tr>
        <tr>
            <td><label for="email" class="left">Email</label></td>
            <td>
                <input type="email" name="email" required placeholder="Enter Email"  value="">
            </td>
        </tr>
        <tr>
            <td><label for="affiliation" class="left">Affiliation</label></td>
            <td>
                <input id="affiliation" type="text" name="affiliation" placeholder="Enter Affiliation" value="">
            </td>
        </tr>
        <tr>
            <td><label for="address" class="left">Full Address</label></td>
            <td>
                <textarea name="address"
                          placeholder="Enter your FULL ADDRESS, including your FULL NAME and country, as it should be written on a letter."
                          required></textarea>
            </td>
        </tr>
        <tr>
            <td>
                <input type='hidden' value='0' name='needInet'>
                <input
                    id="needInet" class="left" type="checkbox"
                    name="needInet" value="1" >
            </td>
            <td><label for="needInet">Need WIFI</label></td>
        </tr>

        <tr class='topborder'>
            <td>
                <input type='hidden' value='0' name='isPassive'>
                <input
                    id="isPassive" class="left" type="checkbox"
                    name="isPassive" value="1" checked>
            </td>
            <td><label for="isPassive">is Passive</label></td>
        </tr>


        <tr class='topborder'>
            <td><label for="talkType" class="left">requested Talk Type</label></td>
            <td>
                <input id="talkType"
                    type="text" name="talkType" placeholder="talktype 0:none, 1:talk, 2:poster"
                    value="">
            </td>
        </tr>
        <tr>
            <td><label for="acceptedType" class="left">accepted Talk Type</label></td>
            <td>
                <input id="acceptedType"
                    type="text" name="acceptedType" placeholder="talktype 0:none, 1:talk, 2:poster"
                    value="">
            </td>
        </tr>

        <tr>
            <td></td>
            <td><small><code>
<?php foreach($PRESENTATION_TYPES as $k => $t) { $kk = sprintf("%02d", $k); print <<<EOT
                $kk: $t<br>\n
EOT;
} ?>
            </code></small></td>
        </tr>

        <tr>
            <td><label for="presentationTitle" class="left">P Title</label></td>
            <td>
                <input id="presentationTitle"
                    type="text" name="presentationTitle" placeholder=""
                    value="">
            </td>
        </tr>

        <tr>
            <td><label for="abstract" class="left">Abstract</label></td>
            <td>
                <textarea name="abstract"
                          placeholder="Abstract"></textarea>
            </td>
        </tr>

        <tr>
            <td><label for="presentationCategories" class="left">Category</label></td>
            <td>
                <input id="presentationCategories"
                    type="text" name="presentationCategories" placeholder=""
                    value="">
            </td>
        </tr>
        <tr>
            <td></td>
            <td><small><code>
<?php
    $categories = $db->query( "SELECT DISTINCT presentationCategories FROM {$tableName}" )->fetchAll(PDO::FETCH_OBJ);
    foreach($categories as $c) {
        if ( $c->presentationCategories != "" ) {
            print "{$c->presentationCategories}<br>\n";
        }
    }
?>
            </small></td>
        </td>
        <tr>
            <td><label for="assignedSession" class="left">SID</label></td>
            <td>
                <input id="assignedSession"
                    type="text" name="assignedSession" placeholder=""
                    value="">
            </td>
        </tr>
        <tr>
            <td>
            </td>
            <td>
                <p><small><code>
<?php foreach ($all_sessions as $s) {print "{$s->id}: [{$s->shortName}] {$s->description}<br>\n"; } ?>
                </code></small></p>
            </td>
        </tr>
        <tr>
            <td>
                <input type='hidden' value='0' name='isPresentationChecked'>
                <input id="isPresentationChecked"
                    class="left" type="checkbox"
                    name="isPresentationChecked" value="1" >
            </td>
            <td><label for="isPresentationChecked">isPresentationChecked (publish final descission)</label></td>
        </tr>
        <tr>
            <td>
                <input type='hidden' value='0' name='isPresentationAccepted'>
                <input id="isPresentationAccepted"
                    class="left" type="checkbox"
                    name="isPresentationAccepted" value="1" >
            </td>
            <td><label for="isPresentationAccepted">isPresentationAccepted (internal: accept or reject?)</label></td>
        </tr>
        <tr>
            <td><label for="presentationSlot" class="left">slot datetime<br>YYYY-MM-DD HH:MM:SS</label></td>
            <td>
                <input id="presentationSlot"
                    type="text" name="presentationSlot" placeholder=""
                    value="">
            </td>
        </tr>
        <tr>
            <td><label for="presentationDuration" class="left">duration<br>(min)</label></td>
            <td>
                <input id="presentationDuration"
                    type="text" name="presentationDuration" placeholder=""
                    value="">
            </td>
        </tr>
        <tr>
            <td><label for="posterPlace" class="left">posterPlace</label></td>
            <td>
                <input id="posterPlace"
                    type="text" name="posterPlace" placeholder=""
                    value="">
            </td>
        </tr>


        <tr class='topborder'>
            <td><label for="nPersons">Acc persons</label></td>
            <td>
                <input id="nPersons"
                    type="number" name="nPersons"
                    value="">
            </td>
        </tr>
        <tr>
            <td>
                <input type='hidden' value='0' name='isVeggie'>
                <input id="c1" class="left" type="checkbox"
                    name="isVeggie" value="1" >
            </td>
            <td><label for="c1">Vegetarian meal</label></td>
        </tr>
        <tr>
            <td>
                <input type='hidden' value='0' name='isImpaired'>
                <input id="isImpaired"
                    class="left" type="checkbox"
                    name="isImpaired" value="1" >
            </td>
            <td><label for="isImpaired">Mobility impaired</label></td>
        </tr>

        <tr>
            <td>
                <input type='hidden' value='0' name='lookingForRoomMate'>
                <input id="lookingForRoomMate"
                    class="left" type="checkbox"
                    name="lookingForRoomMate" value="1" >
            </td>
            <td><label for="lookingForRoomMate">is looking for RoomMate</label></td>
        </tr>

        <tr class='topborder'>
            <td><label for="notes" class="left">notes / log</label></td>
            <td>
                <textarea name="notes" readonly
                          placeholder=""></textarea>
            </td>
        </tr>
    </table>
    <input id="action" type="hidden" name="action" value="" >
    <input id="btn_save" type="button" value="SAVE CHANGES" class="save" >
</form>


<h2 id='overview'>Overview</h2>
<p>
    Click on the entry to edit
</p>

<table id="tab_all" style='width:100%;'>
    <thead class='line_bot'>
        <th>ID</th>
        <th colspan='3'>name</th>
        <th>affil</th>
        <th>akey</th>
        <th>email</th>
        <th>login as</th>
    </thead>
    <tbody>

<?php
$all_people = $db->query( "SELECT * FROM {$tableName}")->fetchAll(PDO::FETCH_OBJ);

foreach($all_people as $p):
    $lnk =
        $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']
        . dirname(dirname($_SERVER['SCRIPT_NAME']))
        . "/user/login.php?op=login&"
        . "email=" . urlencode($p->email)
        . "&akey=" . $p->accessKey
        . "&rdir=index.php";
    ?>
        <tr id="tr<?=$p->id;?>" class='line_bot' data-id="<?=$p->id;?>">
            <td class='center'><?=$p->id;?></td>
            <td class='right'><?=$p->title;?></td>
            <td><?=$p->lastname;?></td>
            <td><?=$p->firstname;?></td>
            <td><?=$p->affiliation;?></td>
            <td><?=$p->accessKey;?></td>
            <td><?=$p->email;?></td>
            <td><a href="<?=$lnk ?>">lnk</a></td>
        </tr>
<?php endforeach; ?>
    </tbody>
</table>

<script>
    $(function(){

        $('#btn_save').click(function(){
            $('#action').val("save");
            $('#frm_edit').submit();
        });
    })
</script>


<?php require "lib/footer.php" ?>
