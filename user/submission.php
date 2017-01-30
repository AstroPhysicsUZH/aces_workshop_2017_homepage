<?php
require_once 'lib/auth.php';


if (isset($_POST["op"]) || isset($_GET["op"])) {

    $op = isset($_POST["op"]) ? $_POST["op"] : $_GET["op"];

    if ($op=="update_submission") {

        $id = $_SESSION['uid'];

        $values = [];
        $lbls = [];

        $stmtstr = "UPDATE {$tableName} SET ";

        foreach ($_POST as $name => $val) {
            if (array_key_exists($name, $tableFields)) { # that should fix possible sql injections...
                $lbls[] = "$name = :$name";
                $values[":$name"] = trim($val);
            }
        }
        $stmtstr .= implode(", ", $lbls);

        # log entry
        $dtstr = $now->format($datetime_db_fstr);
        $str = "$dtstr\t" . sprintf("u%03d", $_SESSION['uid']) . "\tuser updated submission";
        $stmtstr .= ", log = ('$str' || CHAR(13) || log ) ";

        $stmtstr .= " WHERE id = :id;";

        # print_r($stmtstr);
        # print "<p>updating ID [$id]<br />\n";

        $db = open_db($db_address);
        $stmt = $db->prepare($stmtstr);
        $stmt->bindParam(':id', $id , PDO::PARAM_INT);
        foreach ($values as $lbl => $val) {
            $stmt->bindValue($lbl, $val);
        }

        $res = $stmt->execute();

        if ($res == 1) {

            $target = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . '?op=success';
            header('Location: ' . $target , true, $_SERVER['SERVER_PROTOCOL'] == 'HTTP/1.1' ? 303 : 302);
            exit;
        }
        else {
            print "update failed. please inform the admin!";
            exit();
        }
        exit();
    }


    if ($op="success") {
        require "lib/header.php";
        require "lib/menu.php";
        print "
        <main><article>
        <h1>update successful</h1>
        <p>you should be redirected now...</p>
        </article></main>
        <script>
        setTimeout(function () {
                location = 'submission.php';
            }, 500);
        </script>
        ";
        require "lib/footer.php";
        exit();

    }
}






require "lib/header.php";
require "lib/menu.php";
?>

<script src="../js/intercom.min.js"></script>


<script>
$( document ).ready(function(){

    /*
        setup intertab com for preview
    */
	if (Intercom.supported) {
		var title = document.title;

        var $first    = $('#firstname');
        var $last     = $('#lastname');
		var $abstract = $('#talkAbstract');
		var $title    = $('#talkTitle');
		var $authors  = $('#talkCoauthors');
        var $affil    = $("#affiliation");

		var intercom = new Intercom();
        var changeRate = 200; // only send each X ms an update
        var canFireRequest = true;

        $abstract
            .add($title)
            .add($authors)
            .add($first)
            .add($last)
            .add($affil)
            .on('change keyup paste', function() {
            /* this function is rate limited! because mathjax reloads.. */
            if (canFireRequest) {
                canFireRequest = false;

                var authorslist = "<b>" + $last.val() + ', ' + $first.val() + "<sup>1</sup></b>";
                if ($authors.val().length > 0) {
                    authorslist += "; " + $authors.val();
                }

                intercom.emit('notice', {
                    title: $title.val(),
                    authors: authorslist,
                    affil: "<sup>1</sup>"+$affil.val(),
                    abstract: $abstract.val(),
                })
                setTimeout(function() {
                    canFireRequest = true;
                }, changeRate);
            }
        });
	} else {
		alert('intercom.js is not supported by your browser. The preview function will not work');
	}
});
</script>





<main>
<article>
    <h1>My Submission</h1>
    <p>
        Change the details about your submission.
    </p>


    <form action="submission.php" method="post">
        <table class="registration">

            <tr>
                <td>
                    <input type='hidden' value='0' name='wantsPresentTalk'>
                    <input
                        id="wantsPresentTalk" class="left" type="checkbox"
                        name="wantsPresentTalk" value="1"
                        <?= $P["wantsPresentTalk"] ? "checked" : "" ?> >
                </td>
                <td><label for="wantsPresentTalk">Presenting a talk</label></td>
            </tr>

            <tr>
                <td><label for="title" class="left">Title</label></td>
                <td>
                    <input id="title" type="text" name="title" placeholder="- / PhD / Dr / Prof" value="<?=$P["title"]?>">
                </td>
            </tr>
            <tr>
                <td><label for="firstname" class="left">First name</label></td>
                <td>
                    <input id="firstname" type="text" name="firstname" required placeholder="Enter First Name" value="<?=$P["firstname"]?>">
                </td>
            </tr>
            <tr>
                <td><label for="lastname" class="left">Last name</label></td>
                <td>
                    <input id="lastname" type="text" name="lastname" required placeholder="Enter Last Name"  value="<?=$P["lastname"]?>">
                </td>
            </tr>
            <tr>
                <td><label for="affiliation" class="left">Affiliation</label></td>
                <td>
                    <input id="affiliation" type="text" name="affiliation" placeholder="Enter Affiliation" value="<?=$P["affiliation"]?>">
                </td>
            </tr>

            <tr>
                <td>
                    <label for="talkTitle" class="left">Titel</label>
                </td>
                <td>
                    <input id="talkTitle" type="text" name="talkTitle" placeholder="Titel of Presentation"
                    value="<?=$P["talkTitle"]?>">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="talkCoauthors" class="left">Co-Authors</label>
                </td>
                <td>
                    <input id="talkCoauthors" type="text" name="talkCoauthors" placeholder="Last, First; Last, First; ..."
                    value="<?=$P["talkCoauthors"]?>">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="talkAbstract" class="left">Abstract</label>
                </td>
                <td>
                    <textarea id="talkAbstract" name="talkAbstract"
                              style="height:10em;"
                              placeholder="Short abstract (max 200 words). You can use basic latex commands (MathJax), check the preview."
                              ><?=$P["talkAbstract"]?></textarea>
                    <br />
                    <?php /* open popup and trigger initial update for datatransfer */ ?>
                    <a href="preview.php"  style="font-size: 80%;" onclick="window.open('../preview.php', 'newwindow', 'width=400, height=600'); setTimeout(function() {$('#talkAbstract').change()},500); return false;">open interactive preview (disable popup blocker)</a>
                </td>
            </tr>

            <tr>
                <td colspan="2">
                    <input type="submit" value="Submit">
                </td>
            </tr>
        </table>
        <input type="hidden" name="op" value="update_submission"/>
    </form>




</article>
</main>

<?php
require "lib/footer.php";
?>
