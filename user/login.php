<?php

require_once "lib/auth_config.php";
require_once "../lib/app.php";

# print_r($_POST);

if (isset($_POST["op"]) || isset($_GET["op"])){

    $op = isset($_POST["op"]) ? $_POST["op"] : $_GET["op"];

    if ($op=="login") {
        if (
            (!isset($_POST["email"]) || !isset($_POST["accessKey"])) &&
            (!isset($_GET["email"]) || !isset($_GET["akey"] )) ){

            echo "req fields not set";
            echo $_POST["email"];
            echo $_POST["accessKey"];
            echo $_GET["email"];
            echo $_GET["akey"];
            die(1);
        }
        $email = isset($_POST["email"]) ? $_POST["email"] : $_GET["email"];
        $akey = isset($_POST["accessKey"]) ? $_POST["accessKey"] : $_GET["akey"];
        $redirect = isset($_GET["rdir"]) ? $_GET["rdir"] : "index.php";

        $db = open_db($db_address);

        $stmt = $db->prepare("SELECT * FROM {$tableName} WHERE email = :email" );
        $stmt->bindParam(":email", $email);
        $res = $stmt->execute();
        $P = $stmt->fetch(PDO::FETCH_ASSOC); #PDO::FETCH_OBJ);

        if ($res && isset($P)) {
            #print_r($p);
            #print $p->accessKey . " | " . $akey;

            if ($P['accessKey'] == $akey) {
                session_start();
                session_unset();
                session_regenerate_id(true);

                $_SESSION['loggedin'] = TRUE;
                $_SESSION['uid'] = $P['id']; # the id is our username for this script
                $_SESSION['accessKey'] = $P['accessKey'];
                $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
                $_SESSION['last_action'] = date('U');

                $target = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . '/' . $redirect;
                header('Location: ' . $target , true, $_SERVER['SERVER_PROTOCOL'] == 'HTTP/1.1' ? 303 : 302);
                exit;
            }
        }

    }
    elseif ($op=="reset") {
        if (!isset($_POST["email"])) {
            echo "req fields not set2";
            die(1);
        }
        $email = $_POST["email"];

        $db = open_db($db_address);
        $stmt = $db->prepare("SELECT * FROM {$tableName} WHERE email = :email" );
        $stmt->bindParam(":email", $email);
        $res = $stmt->execute();

        if (! $res) {
            echo "This email is not registered";
            die(1);
        }
        $p = $stmt->fetch(PDO::FETCH_OBJ);
        $id = $p->id;
        $email = $p->email;

        $akey = bin2hex(openssl_random_pseudo_bytes(4));
        $stmt = $db->prepare("UPDATE {$tableName} SET accessKey=:akey, isPassive=:isp WHERE id = :id" );
        $res = $stmt->execute([':akey'=>$akey, ':isp'=>FALSE, ':id'=>$id]);

        if (! $res) {
            echo "Error while resetting the access key";
            die(1);
        }

        $from    = '"LISA Symposium Website" <relativityUZH@gmail.com>';
        $replyto = $from;

        $headers  = "";
        $headers .= 'From: ' . $from . "\r\n";
        $headers .= 'Reply-To:' . $replyto . "\r\n";
        $headers .= 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/plain; charset=UTF-8' . "\r\n";
        $headers .= 'X-Mailer: PHP/' . phpversion() . "\r\n";
        $headers .= 'Delivery-Date: ' . date("r") . "\r\n";

        $subject = "11th LISA Symposium Access Key [{$p->id}]";

        $message = preg_replace('~\R~u', "\r\n",  # make sure we have RFC 5322 linebreaks
"Dear Mrs/Mr {$p->lastname}

Someone reset your access key:
Your new access key is: {$akey}

Kind regards,
The local OK
");

        mail($email, $subject, $message, $headers);

        session_start();
        $_SESSION['loggedin'] = FALSE;

        require "lib/header.php";
        require "lib/menu.php";
        echo "<main><article>\n";
        echo "<p>A new accessKey has been mailed to $email. Please log in with the new key</p>";
        echo "</main></article>\n";
        require "lib/footer.php";
        exit;

    }
    elseif ($op=="logout") {
        session_start();
        session_destroy();

        header('Location: http://' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . '/index.php');
        exit;
    }
    else {
        echo "unsupported op";
        die(1);
    }
}

session_start();
$_SESSION['loggedin'] = FALSE;

require "lib/header.php";
require "lib/menu.php";

$email_field = isset($_GET['email']) ? $_GET['email'] : "";
$akey_field = isset($_GET['akey']) ? $_GET['akey'] : "";

?>


<main>
<article>
    <p>
        Please log in using your email and access token
    </p>
    <form class="" name="login" id="login"
          action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>"
          method="POST">
        <input type="hidden" name="op" value="login"/>
        <table>
            <tr>
                <td>email </td>
                <td><input type="text" name="email" value="<?=$email_field?>" /></td>
            </tr>
            <tr>
                <td>access token </td>
                <td><input type="text" name="accessKey" value="<?=$akey_field?>" /></td>
            </tr>
        </table>
        <input type="submit" value="login" />
    </form>

    <p>
        To get a new access token send to you, please enter your email here:
    </p>
    <form class="" name="reset" id="reset"
          action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>"
          method="POST">
        <input type="hidden" name="op" value="reset"/>
        <table>
            <tr>
                <td>email address </td>
                <td><input type="text" name="email" value="" /></td>
            </tr>
        </table>
        <input type="submit" value="reset access token"/>
    </form>

</article>
</main>

<?php require "lib/footer.php" ?>
