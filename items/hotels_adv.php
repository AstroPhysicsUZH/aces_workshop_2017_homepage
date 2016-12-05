
<p>
  We recommend the following hotels.<br>
  For more choice consult <a href="https://www.zuerich.com/en/visit/accommodation-hotel-bed-and-breakfast-hostel"> the webpage of Zurich tourism</a>.
  <br>
  To profit from the reservations and the special prices printed below, please use the keyword <b>LISA Conference 2016</b> while booking and book before the deadline.
  Rates are in CHF and include city tax.
  If two rates are given, then the first is the weekend price, the second for during the week.
  The number in brackets after the room type show the total of available rooms
  (Not the number of free rooms left).
</p>

<p class="warning">
  Please make sure to book <b>way in advance</b>.<br>
  September is a crowded time in Zurich and Hotels are likely to book out very early.
</p>


<?php /*
Link to edit the map:
    http://umap.openstreetmap.fr/en/map/anonymous-edit/78775%3AQazSqz7NxNtECK5cabetfRbeKfU
*/ ?>

<iframe width="100%" height="600px" frameBorder="0" src="http://umap.openstreetmap.fr/en/map/hotels-near-irchel_78775?scaleControl=true&miniMap=false&scrollWheelZoom=false&zoomControl=true&allowEdit=false&moreControl=false&datalayersControl=false&onLoadPanel=undefined&captionBar=false"></iframe>
<p>
    <a class="small" href="http://umap.openstreetmap.fr/en/map/hotels-near-irchel_78775">
        See full screen
    </a>
</p>


<?php

$hotels = csv_to_array('data/hotels.csv');

foreach ($hotels as $hotel) {
    #var_dump($hotel);
    if (!$hotel['show']=="Y") {continue;}

    $shorttel = str_replace(' ', '', $hotel['tel']);
    $img = (isset($hotel['img']) && $hotel['img']) ? $hotel['img'] : 'generic_hotel.jpg';

    echo "<div class='hotel bordered'>\n";
    echo "  <a class='imga' href='img/hotels/$img'>\n";
    echo "    <img src='img/hotels/_$img' />\n";

    if ($hotel['aircon'] === 'Y') {
        echo "    <img title='has air conditioning' class='ico x1' src='img/aircon.png' />\n";
    }
    if ($hotel['wifi'] === 'Y') {
        echo "    <img title='has free wifi' class='ico x2' src='img/wifi.png' />\n";
    }
    if ($hotel['breakfast'] === 'Y') {
        echo "    <img title='breakfast included' class='ico x3' src='img/breakfast.png' />\n";
    }
    echo "  </a>\n";
    echo "  <h3 class='linebot centered'>{$hotel['name']}</h3>\n";
    echo "  <div class='small linebot'>\n";
    if (isset($hotel['street'])) { echo "    {$hotel['street']}<br>\n"; }
    if (isset($hotel['loc']))    { echo "    {$hotel['loc']}\n"; }
    echo "  </div>\n";

    echo "<div class='centered'>";
    if (isset($hotel['url']))   { echo "  <a class='web' href='{$hotel['url']}'>{$hotel['url']}</a>\n"; }
    if (isset($hotel['email'])) { echo "  <a class='mail' href='mailto:{$hotel['email']}'>{$hotel['email']}</a>\n"; }
    if (isset($hotel['tel']))   { echo "  <a class='tel' href='tel:$shorttel'>{$hotel['tel']}</a>\n"; }
    echo "</div>";

    if (isset($hotel['name1']) && $hotel['name1'] ) {
        echo "  <div class='small linetop'>\n";
        echo "    <span>{$hotel['name1']} ({$hotel['nr1']})</span>\n";
        echo "    <span class='rightib'>{$hotel['rates1']}</span>\n";
        echo "  </div>\n";
    }
    if (isset($hotel['name2']) && $hotel['name2'] ) {
        echo "  <div class='small'>\n";
        echo "    <span>{$hotel['name2']} ({$hotel['nr2']})</span>\n";
        echo "    <span class='rightib'>{$hotel['rates2']}</span>\n";
        echo "  </div>\n";
    }
    echo "    <div class='small linetop'>\n";
    echo "        <span>Deadline:</span>\n";
    echo "        <span class='rightib'>{$hotel['deadline']}</span>\n";
    echo "    </div>\n";
    echo "</div>\n";
}
?>
