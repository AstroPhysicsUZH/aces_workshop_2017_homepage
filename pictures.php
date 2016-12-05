<?php


$basedir = "photos";

$cats = [];
$imgs = [];

$subpaths = glob($basedir . '/*' , GLOB_ONLYDIR);
natsort($subpaths);

foreach($subpaths as $subpath) {

    $dir = explode('/', $subpath);
    $subdir = $dir[1];

    #print_r($subdir);

    if ($subdir=="_thumbnails"){continue;}

    $cats[] = $subdir;

    $images = glob($subpath . "/*.[jJ][pP][gG]");
    # $imgs = '';
    foreach($images as $image){
        $imgs[] = ["$image", basename($image), $subpath, $subdir];
    }
}
#print_r($imgs);
?>



<article>

<h1>Pictures</h1>


<ul>
<?php

foreach ($cats as $cat) {
    print "<li><a href='#$cat'>$cat</a></li>";

}
print "</ul>";

foreach ($cats as $cat) {
    print "<h2 id='$cat'>$cat</h2>";

    foreach ($imgs as $img_d) {
        if ($cat==$img_d[3]) {
            echo "
      <p>
        <a href='{$img_d[0]}'>
          <img src='{$basedir}/_thumbnails/" . $img_d[1] ."' height='200' alt='conference image'/>
        </a>
      </p>";
        }
    }
}

 ?>



</article>
