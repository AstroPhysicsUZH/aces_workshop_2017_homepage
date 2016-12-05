<?php
header('Content-Type: text/html; charset=utf-8');

require_once 'lib/app.php';


// Sanity checks
if (array_key_exists('page', $_GET)) { $page = $_GET['page']; }
else { $page = NULL; }

if ( in_array(basename($page), $PAGES) || in_array(basename($page), $HIDDEN_PAGES)) {
    $page = basename($page);
}
else { $page = "home"; }


require "lib/header.inc.php";
require "lib/body_start.inc.php";

?>

<main>
<article>
<?php
    $page_path = $pages_dir . "/" .$page . ".php";
    if (file_exists($page_path)) {require $page_path; }
    else {require "not_found.php"; }
?>
</article>
</main>

<?php require "lib/body_end.inc.php"; ?>
