<?php
require_once "lib/header.php";
?>

<h1>Admin user Management</h1>

<h2>Create new user</h2>
<form id="registration" class="controlbox"
      name="new user registration" method="POST"
      action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>"
      >
	<input type="hidden" name="op" value="register"/>
	<input type="hidden" name="sha1" value=""/>
	<table>
		<tr><td>user name </td><td><input type="text" name="username" value="" /></td></tr>
		<tr><td>email address </td><td><input type="text" name="email" value="" /></td></tr>
		<tr><td>password </td><td><input type="password" name="password1" value="" /></td></tr>
        <tr><td>password (again) </td><td><input type="password" name="password2" value="" /></td></tr>
        <tr><td>role </td><td><input type="text" name="role" value="" /></td></tr>
	</table>
	<input class="save" type="button" value="register" onclick="User.processRegistration()"/>
</form>

<h2>Reset user passwords</h2>

<table class="line_bot">

<?php
$dbfile = User::DATABASE_LOCATION  . User::DATABASE_NAME . ".db";
$udb = new PDO("sqlite:" . $dbfile);
$query = "SELECT * FROM users";


foreach($udb->query($query) as $data) {
    print "
    <tr>
        <td>{$data['username']}</td>
        <td>{$data['email']}</td>
        <td>{$data['role']}</td>
        <td>
            <form class='controlbox' name='unregister'
                id='unregister{$data['username']}'
                action='".htmlspecialchars($_SERVER["PHP_SELF"])."'
                method='POST'>
                <input type='hidden' name='op' value='unregister'/>
                <input type='hidden' name='username' value='{$data['username']}' />
                <input type='submit' value='DEL' class='warn'/>
            </form>
        </td>
    </tr>";
}

?>

</table>

<?php
require_once "lib/footer.php";
?>
