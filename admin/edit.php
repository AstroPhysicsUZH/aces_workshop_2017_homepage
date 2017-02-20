<?php require "lib/header.php"; ?>

<?php


// $db is already open!

if (!empty($_POST)) {

    print "<h1>Processing changes</h1>";
    print_r($_POST);

    if (array_key_exists("action", $_POST)) {

        $action = $_POST["action"];
        unset($_POST["action"]);
        $id = $_POST["id"];
        unset($_POST["id"]);

        if ($action=="save") {
            print "<h2>saving</h2>";
            $values = [];

            $stmtstr = "UPDATE {$tableName} SET ";

            $lbls = [];
            foreach ($_POST as $name => $val) {
                $lbls[] = "$name = :$name";
                $values[":$name"] = $val;
            }
            $stmtstr .= implode(", ", $lbls);

            # log entry
            $dtstr = $now->format($datetime_db_fstr);
            $str = "$dtstr\t{$_SESSION["username"]}\tupdate entry";
            $stmtstr .= ", notes = ('$str' || CHAR(13) || notes ) ";

            $stmtstr .= " WHERE id = :id;";

            print "<p>updating ID [$id]<br />\n";

            $stmt = $db->prepare($stmtstr);
            $stmt->bindParam(':id', $id , PDO::PARAM_INT);
            foreach ($values as $lbl => $val) {
                $stmt->bindValue($lbl, $val);
            }

            $res = $stmt->execute();
            print "updated [$res] entry<br />\n";

            $res = null;
            $stmt = null;
            print "DONE</p>";
        }


        elseif ($action=="del") {
            print "<h2>deleting</h2>";
            $sql = "DELETE FROM {$tableName} WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $res = $stmt->execute();

            print "<p>deleting ID [$id]<br />\n";
            print "deleted [$res] entry<br />\n";
            $res = null;
            $stmt = null;
            print "DONE</p>";
        }

        else {
            print "huch??";
            require "lib/footer.php";
            die();
        }

    }

    $db = null;
    require "lib/footer.php";
    return;
}

// ----------------------------------------------------------------------------

?>



<h1>Edit Entries</h1>
<ul class="pagemenu">
    <li><a href='#entry'>entry</a></li>
    <li><a href='#overview'>overview</a></li>
</ul>

<h2 id='entry'>Entry</h2>

<?php
if (array_key_exists('id', $_GET)):
    $ID = $_GET['id'];

    $stmt = $db->prepare("SELECT * FROM {$tableName} WHERE ID = :id" );
    $stmt->bindParam(":id", $ID);
    $stmt->execute();
    $p = $stmt->fetch(PDO::FETCH_OBJ);

    $all_sessions = $db->query( "SELECT * FROM {$sessionsTable}")->fetchAll(PDO::FETCH_OBJ);
?>

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
            <td><label for="id" class="left">id</label></td>
            <td>
                <input
                    id="id" type="number" name="id"
                    value="<?=$p->id?>" readonly="readonly">
            </td>
        </tr>
        <tr>
            <td><label for="title" class="left">Title</label></td>
            <td>
                <input id="title" type="text" name="title" placeholder="- / PhD / Dr / Prof" value="<?=$p->title?>">
            </td>
        </tr>
        <tr>
            <td><label for="firstname" class="left">First name</label></td>
            <td>
                <input id="firstname" type="text" name="firstname" required placeholder="Enter First Name" value="<?=$p->firstname?>">
            </td>
        </tr>
        <tr>
            <td><label for="lastname" class="left">Last name</label></td>
            <td>
                <input id="lastname" type="text" name="lastname" required placeholder="Enter Last Name"  value="<?=$p->lastname?>">
            </td>
        </tr>
        <tr>
            <td><label for="email" class="left">Email</label></td>
            <td>
                <input type="email" name="email" required placeholder="Enter Email"  value="<?=$p->email?>">
            </td>
        </tr>
        <tr>
            <td><label for="affiliation" class="left">Affiliation</label></td>
            <td>
                <input id="affiliation" type="text" name="affiliation" placeholder="Enter Affiliation" value="<?=$p->affiliation?>">
            </td>
        </tr>
        <tr>
            <td><label for="address" class="left">Full Address</label></td>
            <td>
                <textarea name="address"
                          placeholder="Enter your FULL ADDRESS, including your FULL NAME and country, as it should be written on a letter."
                          required><?=$p->address?></textarea>
            </td>
        </tr>
        <tr>
            <td>
                <input type='hidden' value='0' name='needInet'>
                <input
                    id="needInet" class="left" type="checkbox"
                    name="needInet" value="1"
                    <?= $p->needInet ? "checked" : "" ?> >
            </td>
            <td><label for="needInet">Need WIFI</label></td>
        </tr>
        <tr class='topborder'>
            <td>
                <input type='hidden' value='0' name='isPassive'>
                <input
                    id="isPassive" class="left" type="checkbox"
                    name="isPassive" value="1"
                    <?= $p->isPassive ? "checked" : "" ?> >
            </td>
            <td><label for="isPassive">isPassive</label></td>
        </tr>

        <tr class='topborder'>
            <td><label for="talkType" class="left">requested Talk Type</label></td>
            <td>
                <input id="talkType"
                    type="text" name="talkType" placeholder="talktype 0:none, 1:talk, 2:poster"
                    value="<?=$p->talkType?>">
            </td>
        </tr>
        <tr>
            <td><label for="acceptedType" class="left">accepted Talk Type</label></td>
            <td>
                <input id="acceptedType"
                    type="text" name="acceptedType" placeholder="talktype 0:none, 1:talk, 2:poster"
                    value="<?=$p->acceptedType?>">
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
                    value="<?=$p->presentationTitle?>">
            </td>
        </tr>

        <tr>
            <td><label for="abstract" class="left">Abstract</label></td>
            <td>
                <textarea name="abstract"
                          placeholder="Abstract"
                          required><?=$p->abstract?></textarea>
            </td>
        </tr>

        <tr>
            <td><label for="presentationCategories" class="left">Category</label></td>
            <td>
                <input id="presentationCategories"
                    type="text" name="presentationCategories" placeholder=""
                    value="<?=$p->presentationCategories?>">
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
                    value="<?=$p->assignedSession?>">
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
                    name="isPresentationChecked" value="1"
                    <?= $p->isPresentationChecked ? "checked" : "" ?> >
            </td>
            <td><label for="isPresentationChecked">isPresentationChecked</label></td>
        </tr>
        <tr>
            <td>
                <input type='hidden' value='0' name='isPresentationAccepted'>
                <input id="isPresentationAccepted"
                    class="left" type="checkbox"
                    name="isPresentationAccepted" value="1"
                    <?= $p->isPresentationAccepted ? "checked" : "" ?> >
            </td>
            <td><label for="isPresentationAccepted">isPresentationAccepted</label></td>
        </tr>
        <tr>
            <td><label for="presentationSlot" class="left">slot datetime<br>YYYY-MM-DD</label></td>
            <td>
                <input id="presentationSlot"
                    type="text" name="presentationSlot" placeholder=""
                    value="<?=$p->presentationSlot?>">
            </td>
        </tr>
        <tr>
            <td><label for="presentationDuration" class="left">duration<br>(min)</label></td>
            <td>
                <input id="presentationDuration"
                    type="text" name="presentationDuration" placeholder=""
                    value="<?=$p->presentationDuration?>">
            </td>
        </tr>
        <tr>
            <td><label for="posterPlace" class="left">posterPlace</label></td>
            <td>
                <input id="posterPlace"
                    type="text" name="posterPlace" placeholder=""
                    value="<?=$p->posterPlace?>">
            </td>
        </tr>


        <tr class='topborder'>
            <td><label for="nPersons">Acc persons</label></td>
            <td>
                <input id="nPersons"
                    type="number" name="nPersons"
                    value="<?=$p->nPersons?>">
            </td>
        </tr>
        <tr>
            <td>
                <input type='hidden' value='0' name='isVeggie'>
                <input id="c1" class="left" type="checkbox"
                    name="isVeggie" value="1"
                    <?= $p->isVeggie ? "checked" : "" ?> >
            </td>
            <td><label for="c1">Vegetarian meal</label></td>
        </tr>
        <tr>
            <td>
                <input type='hidden' value='0' name='isImpaired'>
                <input id="isImpaired"
                    class="left" type="checkbox"
                    name="isImpaired" value="1"
                    <?= $p->isImpaired ? "checked" : "" ?> >
            </td>
            <td><label for="isImpaired">Mobility impaired</label></td>
        </tr>

        <tr>
            <td>
                <input type='hidden' value='0' name='lookingForRoomMate'>
                <input id="lookingForRoomMate"
                    class="left" type="checkbox"
                    name="lookingForRoomMate" value="1"
                    <?= $p->lookingForRoomMate ? "checked" : "" ?> >
            </td>
            <td><label for="lookingForRoomMate">is looking for RoomMate</label></td>
        </tr>
        <tr class='topborder'>
            <td><label for="notes" class="left">notes / log</label></td>
            <td>
                <textarea name="notes" readonly
                          placeholder=""><?=$p->notes?></textarea>
            </td>
        </tr>
    </table>
    <input id="action" type="hidden" name="action" value="" >
    <input id="btn_save" type="button" value="SAVE CHANGES" class="save" >
    <input id="btn_del" type="button" value="DELETE" class="warn">
</form>

<?php endif; ?>

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
        $('#tab_all tr').click(function(){
            $this = $(this);
            //console.log("clicked on row: " + $this.data('id'));
            window.location = '?id=' + $this.data('id');
        });

        $('#btn_save').click(function(){
            $('#action').val("save");
            $('#frm_edit').submit();
        });
        $('#btn_del').click(function(){
            $('#action').val("del");
            $('#frm_edit').submit();
        });
    })
</script>


<?php require "lib/footer.php" ?>
