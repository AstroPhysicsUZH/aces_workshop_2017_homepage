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
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

  <title>LISA Symposium Meeting; 5. -- 9. Sept. 2016; University of Zurich</title>

  <meta name="description" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="author" content="Rafael Kueng <rafi.kueng@gmx.ch>" >
  <meta name="designer" content="Rafael Kueng <rafi.kueng@gmx.ch>" >

  <link href='https://fonts.googleapis.com/css?family=PT+Sans:400,700,400italic' rel='stylesheet' type='text/css'>

  <link rel='stylesheet' href='css/fullcalendar.min.css' />
  <script src='js/jquery-1.12.1.min.js'></script>
  <script src='js/moment.min.js'></script>

  <link rel="stylesheet" href="js/jquery-ui-1.12.0.custom/jquery-ui.min.css">
  <link rel="stylesheet" href="js/jquery-ui-1.12.0.custom/jquery-ui.theme.min.css">
  <link rel="stylesheet" href="css/fullcalendar.min.css">
  <link rel="stylesheet" href="http://cdn.jsdelivr.net/qtip2/3.0.3/jquery.qtip.min.css">
  <script src="js/jquery-1.12.1.min.js"></script>
  <script src="js/jquery-ui.min.js"></script>
  <script src="js/moment.min.js"></script>
  <script src="js/fullcalendar.min.js"></script>

  <script src="http://cdn.jsdelivr.net/qtip2/3.0.3/jquery.qtip.min.js"></script>


  <link rel="stylesheet" href="css/layout.css">
  <link rel="shortcut icon" href="/favicon.ico">


</head>

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
