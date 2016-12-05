<?php require "lib/header.php";

/**
    Allows an orga to assign presentations of a certain category
    (the one he has, resp the sessions he has ben assigned to have)
    to the actual sessions
**/


// $db is already open!


if (!empty($_POST)) {

    print "<h1>Processing changes</h1>";
    print_r($_POST);

    if (array_key_exists("action", $_POST)) {

        $action = $_POST["action"];
        unset($_POST["action"]);

        print_r($action);

        if ($action=="save") {
            $id = $_POST['id'];
            $sid = $_POST['sid'];
            $atp = $_POST['acceptedType'];

            $stmtstr = "UPDATE {$tableName} SET
                assignedSession = :sid,
                acceptedType = :atp,
                isPresentationAccepted = :ipa
                WHERE id = :id;";
            $stmt = $db->prepare($stmtstr);
            $stmt->bindParam(':id', $id , PDO::PARAM_INT);
            $stmt->bindParam(':sid', $sid , PDO::PARAM_INT);
            $stmt->bindParam(':atp', $atp , PDO::PARAM_INT);
            $stmt->bindValue(':ipa', TRUE, PDO::PARAM_BOOL);

            $res = $stmt->execute();

            $target = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . "?_=_#frmid" . $id; # ?_=_ part is needed to trigger actual reload
            print "<script type='text/javascript'>window.location = '$target';</script>";
        }

        elseif ($action=="REJECT") {
            $id = $_POST['id'];

            $stmtstr = "UPDATE {$tableName} SET
                isPresentationAccepted = :ipa
                WHERE id = :id;";
            $stmt = $db->prepare($stmtstr);
            $stmt->bindParam(':id', $id , PDO::PARAM_INT);
            $stmt->bindValue(':ipa', FALSE , PDO::PARAM_BOOL);

            $res = $stmt->execute();

            $target = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . "?_=_#frmid" . $id; # ?_=_ part is needed to trigger actual reload
            print "<script type='text/javascript'>window.location = '$target';</script>";
        }

        elseif ($action=="reconsider") {
            print "reconsider";
            $id = $_POST['id'];

            $stmtstr = "UPDATE {$tableName} SET
                isPresentationAccepted = :ipa
                WHERE id = :id;";
            $stmt = $db->prepare($stmtstr);
            $stmt->bindParam(':id', $id , PDO::PARAM_INT);
            $stmt->bindValue(':ipa', null , PDO::PARAM_NULL);

            $res = $stmt->execute();

            print_r($stmtstr);
            print_r($res);

            $target = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . "?_=_#frmid" . $id; # ?_=_ part is needed to trigger actual reload
            print "<script type='text/javascript'>window.location = '$target';</script>";
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

/* here we find out what session and thus what categories the current user was
assigned to */
$my_cats = [];
$available_sessions = [];

$all_sessions = $db->query( "SELECT * FROM {$sessionsTable}")->fetchAll(PDO::FETCH_OBJ);
foreach($all_sessions as $s) {
    # print "{$s->shortName} {$s->orgas} <br>";
    if ( in_array($_SESSION["username"], explode(';', $s->orgas))
        || in_array($USER->role, $special_power_roles) ) {

        foreach(explode(';', $s->categories) as $c) {
            $my_cats[$c] = TRUE;
        }
    }
}
$my_cats = array_keys($my_cats); # get unique elements

foreach($all_sessions as $s) {
    foreach(explode(';', $s->categories) as $c) {
        if (in_array($c, $my_cats)) {
            $available_sessions[] = $s;
            break;
        }
    }
}
# print_r($my_cats);
// ----------------------------------------------------------------------------

?>



<h1>Assign Presentations to Sessions</h1>
<p style="text-align: right;"><code>[your category: <?=implode('; ',$my_cats)?>]</code></p>

<p>
    Please assign all presentations to a suitable session, or reject them.
    (at a later stage, you will be able to arrange, modify and reject presentations selected for your personal session)
</p>

<h2>List of Presentations</h2>

<?php
    $stmtstr = "SELECT
                    id, title, firstname, lastname, email, affiliation,
                    talkType, presentationTitle, coauthors, abstract, presentationCategories,
                    assignedSession, isPresentationAccepted, acceptedType
                FROM {$tableName}
                WHERE presentationTitle<>'';";

    $presentations = $db->query( $stmtstr )->fetchAll(PDO::FETCH_OBJ);
    foreach($presentations as $p) {
        if ($p->talkType > 0) {$type = ($p->talkType == 1 ? "talk" : "poster");}
        else {$type = "none";}

        if (in_array($p->presentationCategories, $my_cats) ||
            in_array($USER->role, $special_power_roles)) {
?>
<form id="frmid<?=$p->id?>"
    action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>"
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

    <div class="inputarea<?=(is_null($p->assignedSession) ? " warn_bg": "")?>">
<?php
            if (isset($p->isPresentationAccepted) && !$p->isPresentationAccepted) {
                print "was rejeced <input type='submit' name='action' value='reconsider' >";
            }
            else {
?>
        <label class="left">Assign to Session:
            <select name="sid" size="1">
                <option value='none'>-- not yet assigned --</option>
<?php
                foreach($available_sessions as $s) {
                    if (array_key_exists('assignedSession', $p) &&
                        $p->assignedSession == $s->id) {
                        $sel = "selected";
                    }
                    else { $sel = ""; }

                    print "<option value='{$s->id}' {$sel}>[{$s->shortName}] {$s->description}</option>";
                }
 ?>
            </select>
        </label>
        <label class="left">As:
            <select name="acceptedType" size="1">
<?php
                $sm1=''; $s0=''; $s1=''; $s2='';
                if ($p->acceptedType == -1)    {$sm1=' selected';}
                elseif ($p->acceptedType == 1) {$s1 =' selected';}
                elseif ($p->acceptedType == 2) {$s2 =' selected';}
                else                           {$s0 =' selected';}
                print "<!-- $sm1 - $s0 - $s1 - $s2 -->\n";

                foreach ($PRESENTATION_TYPES as $tid=>$tstr) {
                    if ($tid==$p->acceptedType) { $sel = " selected"; }
                    else { $sel = ""; }
                    print "<option value='$tid'$sel>$tstr</option>\n";
                }
/*
                <option value=''<?=$s0?>>REJECTED</option>
                <option value='0'<?=$s0?>>- to be decided -</option>
                <option value='1'<?=$s1?>>talk</option>
                <option value='2'<?=$s2?>>poster</option>
*/
?>
            </select>
        </label>
        <div style="display:inline-block;">
            <input class="save" type="submit" name="action" value="save" >
            <input type="submit" name="action" value="REJECT" class="warn" >
        </div>
<?php       } ?>
    </div>
    <input type="hidden" name="id" value="<?=$p->id?>" >
</form>

<?php
        }
    }
?>

<script>
    $(function(){
    })
</script>


<?php require "lib/footer.php" ?>
