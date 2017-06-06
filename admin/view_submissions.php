<?php

require_once "lib/headerphp.php";
require_once "../data/events.php";
require_once "../lib/app.php";



function print_subm($p){
    $pid = sprintf("%03u", $p->id);
    $name = substr($p->firstname,0,1) . ". " . $p->lastname;

    print "<span class='pid small'><code>[{$pid}]</code></span> ";
    print "<span class='name'>{$name}</span> ";
    print "&mdash; <span class='title'>{$p->presentationTitle}</span>";
    print "<br>\n";
}

?>
<html>
<head>
    <link rel="stylesheet" href="../js/jquery-ui-1.12.0.custom/jquery-ui.min.css">
    <link rel="stylesheet" href="../js/jquery-ui-1.12.0.custom/jquery-ui.theme.min.css">
    <!--<link rel="stylesheet" href="../css/layout_hack.css">-->
    <script src="../js/jquery-1.12.1.min.js"></script>
    <script src="../js/jquery-ui.min.js"></script>
    <script src="../js/moment.min.js"></script>

    <script type="text/javascript" async
      src="https://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-MML-AM_CHTML">
    </script>

    <script type="text/x-mathjax-config">
      MathJax.Hub.Config({
          tex2jax: {inlineMath: [['$','$'], ['\\(','\\)']]},
          webFont: "Neo-Euler",
          "HTML-CSS": {
              webFont: "Neo-Euler"
          }
      });
    </script>

<style>
    p {
        display: block;
    }
    .small {
        font-size: 80%;
    }
</style>


</head>
<body>
    <h1>Submissions - Overview</h1>
<?php

foreach ($talkSubmissions as $TS) {
    $pid = sprintf("%03u", $TS->id);
    $aff = "";
    foreach (array_values(explode("\n", $TS->talkCoauthorsAffil)) as $i => $val) {
        $j = $i+1;
        $aff .= "[{$j}] $val <br />";
    }
    $email = "";
    if (array_key_exists('email', $TS)) {$email = $TS->email;}

    print "<h3>{$TS->talkTitle}</h3>\n";
    print "<p class=''>{$TS->talkCoauthors}</p>\n";
    print "<p class='small'>{$aff}</p>\n";
    print "<p class='small'>(submitted by: {$TS->lastname}, {$TS->firstname} <code>[{$pid}]</code> email: <a href='mailto:$email'>$email</a>)</p>\n";
    print "<p>{$TS->talkAbstract}</p>\n";
    print "<hr />";

}

?>

</body></html>
