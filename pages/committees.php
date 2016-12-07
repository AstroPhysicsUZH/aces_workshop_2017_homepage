
<h1>Committees</h1>

<h2>Executive committee (provisional)</h2>
<?php
    $news = csv_to_array('pages/data/science_advisory_committee.csv');
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


<h2>Local Organizing Committee</h2>
<ul>
    <li><b>Philippe Jetzer</b></li>
    <li>Yannick Bötzel</li>
    <li>Carmelina Genovese</li>
    <li>Rafael Küng</li>
    <li>Lionel Philippoz</li>
    <li>Andreas Schärer</li>
</ul>
