
<?php

    $news = csv_to_array('data/news.csv');

    // sort by date, newest on top
    function compare_date($a, $b)
    {
        return strnatcmp($b['date'], $a['date']);
    }
    usort($news, 'compare_date');

    echo "<ul>\n";
    foreach ($news as $v) {
        $strdate = $v['date'];
        echo "<li><span class='small date'>$strdate</span> {$v['comment']}</li>\n";
    }
    echo "</ul>\n";

?>
