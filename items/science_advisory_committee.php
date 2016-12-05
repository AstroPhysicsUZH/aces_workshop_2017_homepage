
<?php
    $news = csv_to_array('data/science_advisory_committee.csv');
    // sort by date, newest on top
    function compare_lastname($a, $b)
    {
        return strnatcmp($a['last_name'], $b['last_name']);
    }
    usort($news, 'compare_lastname');

    echo "<ul>\n";
    foreach ($news as $v) {
        echo "  <li>{$v['first_name']} {$v['last_name']}</li>\n";
    }
    echo "</ul>\n";
?>
