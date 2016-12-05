<?php
    date_default_timezone_set("Europe/Zurich");
	$data = false;
	ini_set("display_errors", 1);
	ini_set("error_reporting", E_ALL | E_STRICT);

    // this php script can only bootstrap an admin/admin user, only if not already exists!
    // so here we override if bob wrote something else into the fields...
    // DONT change the hardcoded username / role, otherwise BOB can create admin users as he likes...
    // for security we rely on that the legid admin executes this script first!
    if(isset($_POST["op"]))
    {
        $operation = $_POST["op"];
        if($operation == "register") {
            $_POST["username"] = "admin";
            $_POST["role"] = "admin";
        }
        else {
            die(); // very important for security!! otherwise we could post anything and it would be executed
        }
    }

	require_once("../lib/user.php");
	$USER = new User();
?>
<!DOCTYPE html>
<html>
	<head>
		<title>init admin user</title>
		<meta charset="utf-8"/>
		<script type="text/javascript" src="../js/sha1.js"></script>
		<script type="text/javascript" src="../js/user.js"></script>
		<link rel="stylesheet" type="text/css" href="../css/style.css"></link>
	</head>

	<body>
		<h1>Init Admin User</h1>

		<?php if($USER->error!="") { ?>
		<p class="error">Error: <?php echo $USER->error; ?></p>
		<?php } ?>

		<p>
            This bootstratps the admin user system.<br />
            It actually makes no sense to change the username and role, since they are hardcoded.
        </p>

		<!-- Allow a new user to register -->
		<form class="controlbox" name="new user registration" id="registration" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
			<input type="hidden" name="op" value="register"/>
			<input type="hidden" name="sha1" value=""/>
			<table>
				<tr><td>user name </td><td><input type="text" name="username" value="admin" /></td></tr>
				<tr><td>email address </td><td><input type="text" name="email" value="" /></td></tr>
				<tr><td>password </td><td><input type="password" name="password1" value="" /></td></tr>
                <tr><td>password (again) </td><td><input type="password" name="password2" value="" /></td></tr>
                <tr><td>role </td><td><input type="text" name="role" value="admin" /></td></tr>
			</table>
			<input type="button" value="register" onclick="User.processRegistration()"/>
		</form>

		<!-- Request a new password from the system -->
		<form class="controlbox" name="forgotten passwords" id="reset" action="index.php" method="POST">
			<input type="hidden" name="op" value="reset"/>
			<table>
				<tr><td>email address </td><td><input type="text" name="email" value="<?php $USER->email; ?>" /></td></tr>
			</table>
			<input type="submit" value="reset password"/>
		</form>

		<hr/>

		<p>POST: <?php echo str_replace("\n", "<br/>\n\t\t\t", print_r($_POST, true)); ?></p>
		<hr/>
		<p>INFO LOG: <?php echo str_replace("\n", "<br/>\n\t\t\t", print_r($USER->info_log, true)); ?></p>
		<hr/>
		<p>ERROR LOG: <?php echo str_replace("\n", "<br/>\n\t\t\t", print_r($USER->error_log, true)); ?></p>

	</body>
</html>
