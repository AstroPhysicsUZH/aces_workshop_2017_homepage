<?php

#require_once 'lib/app.php';

header('Content-Type: text/html; charset=utf-8');

// Sanity checks
if (array_key_exists('page', $_GET)) { $page = $_GET['page']; }
else { $page = NULL; }

if ( in_array(basename($page), $PAGES) || in_array(basename($page), $HIDDEN_PAGES)) {
    $page = basename($page);
}
else { $page = "home"; }

?>


<main>
<article>
<?php
  # get main content
  if (file_exists("$page" . ".php")) {require "$page" . ".php"; }
  else {require "not_found.php"; }
?>
</article>
</main>
