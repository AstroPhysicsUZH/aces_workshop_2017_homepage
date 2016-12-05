<?php

require_once 'lib/app.php';

header('Content-Type: text/html; charset=utf-8');

// Sanity checks
if (array_key_exists('page', $_GET)) { $page = $_GET['page']; }
else { $page = NULL; }

if ( in_array(basename($page), $PAGES) || in_array(basename($page), $HIDDEN_PAGES)) {
    $page = basename($page);
}
else { $page = "home"; }

?>

<body>
<div id="wrapper">

<header>
  <h1>11th International LISA Symposium</h1>
  <h2>5. &ndash; 9. September 2016<br>Irchel Campus, University of Zurich, Switzerland</h2>
</header>

<nav>
  <ul>
<?php
  # get menu
  print_menu($page);
?>
  </ul>

  <p class="menuaddition bigtopspace">
    Download Poster:<br />
    <a href='files/poster_lisa11_A4.pdf'>[ pdf, hires, 4 MB ]</a>
    <!-- simply print the A4 version up to A1!!
    <a href='files/poster_lisa11_A1.pdf'>[ pdf, A1, 30 MB]</a>
-->
  </p>
  <p class="menuaddition">
    contact:<br>
    <a href='mailto:relativityUZH@gmail.com'>relativityUZH@gmail.com</a>
  </p>
</nav>

<main>
<article>
<?php
  # get main content
  if (file_exists("$page" . ".php")) {require "$page" . ".php"; }
  else {require "not_found.php"; }
?>
</article>
</main>

<footer>
  <img class="footerimg left" src="img/lisapf_logo.png" alt="lisa_pathfinder_logo" />
  <img class="footerimg" src="img/uzh_logo_e_neg_border.png" alt="uzhlogo" />
  <img class="footerimg" src="img/eth_logo.png" alt="ethlogo" />
  <img class="footerimg" src="img/pauli.png" alt="paulilogo" />
</footer>

</div>
</body>
</html>
