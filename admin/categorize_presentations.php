<?php require "lib/header.php";

/**
    Allows to group presentations into types
    (like theory, experimental, ...)
**/


// $db is already open!

/*
    this table has:
    - orgas: comma separated admin names
    - timeslots: comma separated start and end dates! (multiple pairs possible)
*/

if (!empty($_POST)) {

    print "<h1>Processing changes</h1>";
    print_r($_POST);

    if (array_key_exists("action", $_POST)) {

        $action = $_POST["action"];
        unset($_POST["action"]);

        if ($action=="assign_cat") {
            $id = $_POST['id'];
            $pcat = $_POST['presentationCategories'];

            $stmtstr = "UPDATE {$tableName} SET
                presentationCategories = :pcat
                WHERE id = :id;";
            $stmt = $db->prepare($stmtstr);
            $stmt->bindParam(':id', $id , PDO::PARAM_INT);
            $stmt->bindParam(':pcat', $pcat , PDO::PARAM_STR);

            $res = $stmt->execute();

            $target = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . "?_=_#frmid" . $id; # ?_=_ part is needed to trigger actual reload
            print "<script type='text/javascript'>window.location = '$target';</script>";
        }

        elseif ($action="reject") {

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

// ----------------------------------------------------------------------------

?>



<h1>Categorize Presentations</h1>

<h2>Already Used Categories</h2>

<ul>
<?php
    $categories = $db->query( "SELECT DISTINCT presentationCategories FROM {$tableName}" )->fetchAll(PDO::FETCH_OBJ);
    foreach($categories as $c) {
        if ( $c->presentationCategories != "" ) {
            print "<li><code>{$c->presentationCategories}</code></li>";
        }
    }
?>
</ul>


<h2>Presentaions</h2>

<?php
    $stmtstr = "SELECT
                    id, title, firstname, lastname, email, affiliation,
                    talkType, presentationTitle, coauthors, abstract, presentationCategories
                FROM {$tableName}
                WHERE presentationTitle<>'';";

    $presentations = $db->query( $stmtstr )->fetchAll(PDO::FETCH_OBJ);
    foreach($presentations as $p) {
        if ($p->talkType > 0) {$type = ($p->talkType == 1 ? "talk" : "poster");}
        else {$type = "none";}
?>


<form id="frmid<?=$p->id?>"
    action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>"
    method="post">
    <h3 class="presentation"><code>[<?=$p->id?>]</code> <?=$p->presentationTitle?></h3>
    <p class="summary">
        Type: <?=$type?> <br />
        By: <?=$p->title?> <?=$p->firstname?> <?=$p->lastname?> (<?=$p->email?>)<br />
        <i><?=$p->affiliation?></i><br />
        <small>Coauthors: <?=$p->coauthors?></small>
    </p>
    <p class="abstract">
        <code><?=nl2br($p->abstract) ?></code>
    </p>

    <div class="inputarea<?=(empty($p->presentationCategories) ? " warn_bg": "")?>">
        <label for="presentationCategories" class="left">Assign Categories</label>
        <input
            type="text" name="presentationCategories"
            placeholder="cats" value="<?=$p->presentationCategories ?>">
        <input type="submit" value="SAVE" class="save" >
    </div>
    <input type="hidden" name="action" value="assign_cat" >
    <input type="hidden" name="id" value="<?=$p->id?>" >
</form>

<?php } ?>

<script>
    $(function(){
    })
</script>


<?php require "lib/footer.php" ?>
