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
        $stmtstr .= ", notes = ('$str' || CHAR(13) || notes ) ";

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
		var $abstract = $('#abstract');
		var $title    = $('#presentationTitle');
		var $authors  = $('#coauthors');
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
                    <input id="r1" type="radio" name="talkType"
                        value="0" <?=($P["talkType"]==0?"checked":"")?>>
                </td>
                <td>
                    <label for="r1">None</label>
                </td>
            </tr>
            <tr>
                <td>
                    <input id="r3" type="radio" name="talkType"
                        value="1" <?=($P["talkType"]==1?"checked":"")?>>
                </td>
                <td>
                    <label for="r3">Talk</label>
                </td>
            </tr>
            <tr>
                <td>
                    <input id="r2" type="radio" name="talkType"
                        value="2" <?=($P["talkType"]==2?"checked":"")?>>
                </td>
                <td>
                    <label for="r2">Poster</label>
                </td>
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
                    <label for="presentationTitle" class="left">Titel</label>
                </td>
                <td>
                    <input id="presentationTitle" type="text" name="presentationTitle" placeholder="Titel of Presentation"
                    value="<?=$P["presentationTitle"]?>">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="coauthors" class="left">Co-Authors</label>
                </td>
                <td>
                    <input id="coauthors" type="text" name="coauthors" placeholder="Last, First; Last, First; ..."
                    value="<?=$P["coauthors"]?>">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="abstract" class="left">Abstract</label>
                </td>
                <td>
                    <textarea id="abstract" name="abstract"
                              style="height:10em;"
                              placeholder="Short abstract (max 200 words). You can use basic latex commands (MathJax), check the preview."
                              ><?=$P["abstract"]?></textarea>
                    <br />
                    <?php /* open popup and trigger initial update for datatransfer */ ?>
                    <a href="preview.php"  style="font-size: 80%;" onclick="window.open('../preview.php', 'newwindow', 'width=400, height=600'); setTimeout(function() {$('#abstract').change()},500); return false;">open interactive preview (disable popup blocker)</a>
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
