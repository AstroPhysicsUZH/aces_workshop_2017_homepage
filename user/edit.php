<?php
require_once 'lib/auth.php';

require_once "../lib/app.php";


if (isset($_POST["op"]) || isset($_GET["op"])) {

    $op = isset($_POST["op"]) ? $_POST["op"] : $_GET["op"];

    if ($op=="edit_user") {

        $id = $_SESSION['uid'];

        $values = [];
        $lbls = [];

        $stmtstr = "UPDATE {$tableName} SET ";

        foreach ($_POST as $name => $val) {
            if (array_key_exists($name, $tableFields)) { # that should fix possible sql injections...
                $lbls[] = "$name = :$name";
                $values[":$name"] = $val;
            }
        }
        $stmtstr .= implode(", ", $lbls);

        # log entry
        $dtstr = $now->format($datetime_db_fstr);
        $str = "$dtstr\t" . sprintf("u%03d", $_SESSION['uid']) . "\tuser updated personal details";
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
                location = 'edit.php';
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

<main>
<article>
    <h1>Edit Your Details</h1>

    <p>
        To change any other details about your registration, please contact us directly by email.
    </p>

    <form action="edit.php" method="post">
        <table class="registration">
            <thead>
                <th colspan="2">
                </th>
            </thead>
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
                <td><label for="email" class="left">Email</label></td>
                <td>
                    <input type="email" name="email" required placeholder="Enter Email"  value="<?=$P["email"]?>">
                </td>
            </tr>
            <tr>
                <td><label for="affiliation" class="left">Affiliation</label></td>
                <td>
                    <input id="affiliation" type="text" name="affiliation" placeholder="Enter Affiliation" value="<?=$P["affiliation"]?>">
                </td>
            </tr>
            <tr>
                <td><label for="address" class="left">Full Address<br />(including name<br />and country)</label></td>
                <td>
                    <textarea name="address"
                              style="height:8em;"
                              placeholder="Enter your FULL ADDRESS, including your FULL NAME and country, as it should be written on a letter."
                              required><?=$P["address"]?></textarea>
                </td>
            </tr>
            <tr>
                <td>
                    <input type='hidden' value='0' name='needInet'>
                    <input
                        id="needInet" class="left" type="checkbox"
                        name="needInet" value="1"
                        <?= $P["needInet"] ? "checked" : "" ?> >
                </td>
                <td><label for="needInet">Need WIFI access</label></td>
            </tr>

            <tr>
                <td>
                    <input type='hidden' value='0' name='isVeggie'>
                    <input id="c1" class="left" type="checkbox"
                        name="isVeggie" value="1"
                        <?= $P["isVeggie"] ? "checked" : "" ?> >
                </td>
                <td><label for="c1">Vegetarian meal</label></td>
            </tr>
            <tr>
                <td>
                    <input type='hidden' value='0' name='isImpaired'>
                    <input id="isImpaired"
                        class="left" type="checkbox"
                        name="isImpaired" value="1"
                        <?= $P["isImpaired"] ? "checked" : "" ?> >
                </td>
                <td><label for="isImpaired">Mobility impaired</label></td>
            </tr>

            <tr>
                <td>
                    <input type='hidden' value='0' name='lookingForRoomMate'>
                    <input id="lookingForRoomMate"
                        class="left" type="checkbox"
                        name="lookingForRoomMate" value="1"
                        <?= $P["lookingForRoomMate"] ? "checked" : "" ?> >
                </td>
                <td><label for="lookingForRoomMate">looking for roommates</label></td>
            </tr>
        </table>
        <input type="hidden" name="op" value="edit_user"/>
        <input type="submit" value="Save changes" />
    </form>


</article>
</main>

<?php
require "lib/footer.php";
?>
