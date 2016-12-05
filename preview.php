<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

  <title>Abstract Preview</title>

  <meta name="description" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="author" content="Rafael Kueng <rafi.kueng@gmx.ch>" >
  <meta name="designer" content="Rafael Kueng <rafi.kueng@gmx.ch>" >

  <script src='js/jquery-1.12.1.min.js'></script>
  <script src="js/intercom.min.js"></script>
  <script type="text/javascript" async
    src="https://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-MML-AM_CHTML">
  </script>

  <script type="text/x-mathjax-config">
    MathJax.Hub.Config({tex2jax: {inlineMath: [['$','$'], ['\\(','\\)']]}});
  </script>
</head>

<body>

<h1 id="title"></h1>
<p id="authors"></p>
<p id="affil" style="font-style:italic;"></p>
<p id="abstract"></p>

<script>

var entityMap = {
    "&": "&amp;",
    "<": "&lt;",
    ">": "&gt;",
    '"': '&quot;',
    "'": '&#39;',
    "/": '&#x2F;'
};

function escapeHtml(string) {
    return String(string).replace(/[&<>"'\/]/g, function (s) {
        return entityMap[s];
    });
}

$( document ).ready(function(){

    var intercom = Intercom.getInstance();
    var timer = null;
    var changeRate = 1000; // waits X ms until received the last update for rerun of mathjax

    var $title = $('#title');
    var $authors = $('#authors');
    var $affil = $('#affil');
    var $abstract = $('#abstract');

    intercom.on('notice', function(data) {

        $title.html(data['title']);
        $authors.html(data['authors']);
        $affil.html(data['affil']);
        $abstract.html(escapeHtml(data['abstract']).replace(/(?:\r\n|\r|\n)/g, '<br />'));

        if (timer) {
            clearTimeout(timer);
            timer = null;
        }

        timer = setTimeout(function() {
            //console.log("timeup");
            MathJax.Hub.Queue(["Typeset",MathJax.Hub]);
        }, changeRate);
    });
});
</script>
</body>
</html>
