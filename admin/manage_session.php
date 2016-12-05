<?php
require_once "lib/headerphp.php";


if (array_key_exists('sid', $_GET)) {
    $sid = $_GET['sid'];
    $sid_is_set = TRUE;

    $stmt = $db->prepare( "SELECT * FROM {$sessionsTable} WHERE id=:sid");
    $stmt->bindParam(':sid', $sid , PDO::PARAM_INT);
    $stmt->execute();
    $s = $stmt->fetch(PDO::FETCH_OBJ);

    if ( !in_array($USER->username, explode(";", $s->orgas)) &&  # an unauthorised user access existing stuff
         !in_array($USER->role, $special_power_roles) ) { # the user has super powers or asked nicely
        print "nice try, but you murdered me... die() [access denied]";
        die();
    }

    # parse the timeslots.. we'll use it anyways quite often
    # timeslots have format "2016-09-05 08:00/12:00; ..."
    if (!empty($s->timeslots)){
        $slots = explode(";",$s->timeslots);
        $startend_datetime = explode("/", $slots[0]); # for the moment, we only
                                                # support one timeslot per session

        $start_datetime = $startend_datetime[0];
        $start_date = explode(" ", $startend_datetime[0])[0];
        $end_datetime = $start_date . " " . $startend_datetime[1];

        $start_dt = new DateTime($start_datetime);
        $end_dt   = new DateTime($end_datetime);

        $dt_are_set = TRUE;
    }
    else {
        $dt_are_set = FALSE;
    }
}
else {$sid_is_set=FALSE;}



if (!empty($_POST)) {

    # print "<h1>Processing POST</h1>\n";
    #print_r($_POST);


    if (array_key_exists("action", $_POST)) {

        $action = $_POST["action"];
        unset($_POST["action"]);

        #print_r($action);

        if ($action==
"save") {
            $id = $_POST['id'];

            $atp = NULL;
            $dat = NULL;
            $dur = NULL;
            $ppl = NULL;
            $pac = NULL;

            if ($_POST['acceptedType'] == 0) {
                $atp = NULL;
                $pac = NULL;
            }
#            elseif ($_POST['acceptedType'] == -1) {
#                $atp = -1;
#                $pac = FALSE;
#            }
            else {
                $atp = $_POST['acceptedType'];
                $pac = TRUE;
            }

            # check if strings valid
            if ($atp == PRESENTATION_TYPE_TALK) {
                # echo "\ndtcheck >".$_POST['time']."<\n\n";
                if (!empty($_POST['time'])) {
                    try {
                        $dat = (
                            new DateTime(
                                $_POST['date'] . " " . $_POST['time']
                                )
                            )->format($datetime_db_fstr);
                        $int = new DateInterval('PT'.$_POST['duration'].'M');
                        $dur = $_POST['duration'];
                    }
                    catch (Exception $e) {
                        echo nl2br("\n\nerror while parsing date and time\nMAKE SURE TO ENTER DATE TIME AND DURATION\nnot saving anything!");
                        print "<!--";
                        print_r($_POST);
                        print_r($e);
                        die();
                    }
                }
            }
            elseif ($atp == PRESENTATION_TYPE_POSTER) {
                $ppl = $_POST['posterPlace'];
            }

            $stmtstr = "UPDATE {$tableName} SET
                acceptedType = :atp,
                presentationSlot = :dat,
                presentationDuration = :dur,
                posterPlace = :ppl,
                isPresentationAccepted = :pac
                WHERE id = :id;";
            $stmt = $db->prepare($stmtstr);
            $stmt->bindParam(':id', $id , PDO::PARAM_INT);
            $stmt->bindParam(':atp', $atp , PDO::PARAM_INT);
            $stmt->bindParam(':dat', $dat , PDO::PARAM_STR);
            $stmt->bindParam(':dur', $dur , PDO::PARAM_INT);
            $stmt->bindParam(':ppl', $ppl , PDO::PARAM_STR);
            $stmt->bindParam(':pac', $pac , PDO::PARAM_BOOL);

            $res = $stmt->execute();

            $target = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . ($sid ? "?sid=$sid" : "?_=_") . "#frmid" . $id; # ?_=_ part is needed to trigger actual reload
            #print "<script type='text/javascript'>window.location = '$target';</script>";
            header('Location: '.$target);
            exit();
        }

        elseif ( $action=="reconsider" ) {
            $id = $_POST['id'];
            $pac = NULL;
            $atp = NULL;

            $stmtstr = "UPDATE {$tableName} SET
                acceptedType = :atp,
                isPresentationAccepted = :pac
                WHERE id = :id;";
            $stmt = $db->prepare($stmtstr);
            $stmt->bindParam(':id', $id , PDO::PARAM_INT);
            $stmt->bindParam(':atp', $atp , PDO::PARAM_INT);
            $stmt->bindParam(':pac', $pac , PDO::PARAM_BOOL);

            $res = $stmt->execute();

            $target = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . ($sid ? "?sid=$sid" : "?_=_") . "#frmid" . $id; # ?_=_ part is needed to trigger actual reload
            #print "<script type='text/javascript'>window.location = '$target';</script>";
            header('Location: '.$target);
            exit();
        }

        elseif ( $action=="REJECT" ) {
            $id = $_POST['id'];
            $pac = FALSE;

            #                acceptedType = :atp,
            $stmtstr = "UPDATE {$tableName} SET
                isPresentationAccepted = :pac
                WHERE id = :id;";
            $stmt = $db->prepare($stmtstr);
            $stmt->bindParam(':id', $id , PDO::PARAM_INT);
#            $stmt->bindParam(':atp', $atp , PDO::PARAM_INT);
            $stmt->bindParam(':pac', $pac , PDO::PARAM_BOOL);

            $res = $stmt->execute();

            $target = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . ($sid ? "?sid=$sid" : "?_=_") . "#frmid" . $id; # ?_=_ part is needed to trigger actual reload
            #print "<script type='text/javascript'>window.location = '$target';</script>";
            header('Location: '.$target);
            exit();
        }

        else {
            print "huch??";
            require "lib/footer.php";
            die();
        }

    }

    $db = null;
    require "lib/footer.php";
    die();
}

require_once "lib/headerhtml.php";

#
# DISPLAY MENU ONLY
#
if ( !$sid_is_set ) {

    $all_sessions = $db->query( "SELECT * FROM {$sessionsTable}")->fetchAll(PDO::FETCH_OBJ);
    $url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];

?>
    <h1>Manage Your Sessions</h1>
    <h2>Select a session to manage:</h2>

    <ul class="pagemenu">
<?php
    foreach($all_sessions as $s) {
        if (in_array($_SESSION["username"], explode(';', $s->orgas)) ||
            in_array($USER->role, $special_power_roles)) {
            print "<li><a href='{$url}?sid={$s->id}'><code>[{$s->shortName}]</code> {$s->description}</a></li>\n";
        }
    }
    echo "    </ul>\n";

    require "lib/footer.php";
    exit();
}

#
# DISPLAY MANAGEMENT TOOLS
#
elseif ($sid_is_set) {

    print "<h1>Manage Your Session:</h1>";
    print "<p style='text-align:right;'><code>[{$s->shortName}]</code> {$s->description}</p>";

    $stmtstr = "SELECT
                    id, title, firstname, lastname, email, affiliation,
                    talkType, presentationTitle, coauthors, abstract, presentationCategories,
                    assignedSession, isPresentationAccepted, acceptedType,
                    presentationSlot, presentationDuration, posterPlace
                FROM {$tableName}
                WHERE assignedSession=:sid;";
    $stmt = $db->prepare($stmtstr);
    $stmt->bindParam(':sid', $sid , PDO::PARAM_INT);
    $stmt->execute();
    $presentations = $stmt->fetchAll(PDO::FETCH_OBJ);

    $stmtstr = "SELECT
                    id, title, firstname, lastname, email, affiliation,
                    talkType, presentationTitle, coauthors, abstract, presentationCategories,
                    assignedSession, isPresentationAccepted, acceptedType,
                    presentationSlot, presentationDuration, posterPlace
                FROM {$tableName}
                WHERE assignedSession<>:sid;";
    $stmt = $db->prepare($stmtstr);
    $stmt->bindParam(':sid', $sid , PDO::PARAM_INT);
    $stmt->execute();
    $other_presentations = $stmt->fetchAll(PDO::FETCH_OBJ);

?>

    <h2>Presentations</h2>

<?php
    foreach($presentations as $p) {
        #if ($p->talkType > 0) {$type = GET_PRES_STR($p->talkType); }
        #else {$type = "none";}
        $type = $PRESENTATION_TYPES[$p->talkType];
        print "<!-- {$p->presentationSlot} / {$p->presentationDuration} -->";

        if (isset($p->presentationSlot)) {
            $ddtt = new DateTime($p->presentationSlot);
            $ptime = $ddtt->format('H:i');
            $pdate = $ddtt->format('Y-m-d');
        }
        else {
            $ptime = "";
            $pdate = "";
        }
?>

        <form id="frmid<?=$p->id?>"
            action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?sid=$sid"; ?>"
            method="post">
            <h3 class="presentation"><code>[<?=$p->id?>]</code> <?=$p->presentationTitle?></h3>
            <p class="summary">
                Type: <?=$type?> <br />
                By: <?=$p->title?> <?=$p->firstname?> <?=$p->lastname?> (<?=$p->email?>)<br />
                <i><?=$p->affiliation?></i><br />
                <small>Coauthors: <?=$p->coauthors?></small><br />
            </p>
            <p class="abstract">
                <code><?=nl2br($p->abstract) ?></code>
            </p>

            <div class="inputarea">
<?php
                    if (isset($p->isPresentationAccepted) && !$p->isPresentationAccepted) {
                        print "was rejeced <input type='submit' name='action' value='reconsider' >";
                    }
                    else {
                        $s = [];
                        $s[-1] = ""; $s[0] = ""; $s[1] = ""; $s[2] = "";
                        if ($p->acceptedType == 1) {$s[1]=' selected';}
                        elseif ($p->acceptedType == 2) {$s[2]=' selected';}
                        else {$s[0]=' selected';}
?>
                <div class="half">
                    <label class="left">Accept as:
                        <select name="acceptedType" size="1">
<?php
                        foreach ($PRESENTATION_TYPES as $tid=>$tstr) {
                            if ($tid==$p->acceptedType) { $sel = " selected"; }
                            else { $sel = ""; }
                            print "<option value='$tid'$sel>$tstr</option>\n";
                        }
/*
                            <option value='-1'<?=$s[-1]?>>REJECT</option>
                            <option value='0'<?=$s[0]?>>-- not defined --</option>
                            <option value='1'<?=$s[1]?>>talk</option>
                            <option value='2'<?=$s[2]?>>poster</option>
*/
?>
                        </select>
                    </label>
                    <br>
                    <input class="save" type="submit" name="action" value="save" >
                    <input class="warn" type="submit" name="action" value="REJECT" >
                </div>
<?php
                        // allow only super orgas to manage the time
                        if (in_array($USER->role, $special_power_roles)) {
?>
                <div class="half">
                    <label class="left">
                        <small>[talk] starting time (24h):</small>
                        <input class="short" name="time" type="text"
                            value="<?=$ptime?>"
                            placeholder="14:15" pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]"
                            title="Enter a time in 24h HH:mm format, like 14:15"
                            >
                    </label><br>
                    <label class="left">
                        <small>[talk] date (YYYY-MM-DD):</small>
                        <input class="short" name="date" type="text"
                            value="<?=$pdate?>"
                            placeholder="2016-09-05"
                            pattern="[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]"
                            title="Enter a date in ISO norm YYYY-MM-DD , like 2016-09-05"
                            >
                    </label><br>
                    <label class="left">
                        <small>[talk] duration in minutes:</small>
                        <input class="short" name="duration" type="numeric" min=0 max=240
                            placeholder="25" value="<?=$p->presentationDuration?>"
                            >
                    </label><br>
                    <!--
                    <label class="left">
                        <small>[poster] number / place:</small>
                        <input class="short" name="posterPlace" type="numeric" min=0 max=240
                            value="<?=$p->posterPlace?>">
                    </label>
                    -->
                </div>
<?php                   } ?>
<?php               } ?>
            </div>
            <input type="hidden" name="id" value="<?=$p->id?>" >
        </form>

<?php
    }

    require "lib/footer.php";
    exit();
}
?>
