<?php require "lib/header.php"; ?>

<?php
$nRows = (int)$db->query("SELECT count(*) FROM {$tableName}")->fetchColumn();
$nPayed = (int)$db->query("SELECT SUM(CASE WHEN hasPayed THEN 1 ELSE 0 END) FROM  {$tableName}")->fetchColumn();


$nLunch = (int)$db->query("SELECT sum(nPersons) from {$tableName}")->fetchColumn();
$nVeggies = (int)$db->query("SELECT SUM(CASE WHEN isVeggie THEN nPersons ELSE 0 END) FROM {$tableName}")->fetchColumn();
$nWLAN = (int)$db->query("SELECT SUM(CASE WHEN needInet THEN 1 ELSE 0 END) FROM  {$tableName}")->fetchColumn();
$nImpared = (int)$db->query("SELECT SUM(CASE WHEN isImpaired THEN 1 ELSE 0 END) FROM  {$tableName}")->fetchColumn();


$nContributions = (int)$db->query("SELECT SUM(CASE WHEN talkType>0 THEN 1 ELSE 0 END) FROM  {$tableName}")->fetchColumn();

$nTalks = (int)$db->query("SELECT SUM(CASE WHEN talkType=1 THEN 1 ELSE 0 END) FROM  {$tableName}")->fetchColumn();

$nPosters = (int)$db->query("SELECT SUM(CASE WHEN talkType=2 THEN 1 ELSE 0 END) FROM  {$tableName}")->fetchColumn();

$nContributionsReal = (int)$db->query("SELECT SUM(CASE WHEN acceptedType>0 THEN 1 ELSE 0 END) FROM  {$tableName}")->fetchColumn();

$nTalksReal = (int)$db->query("SELECT SUM(CASE WHEN acceptedType=1 THEN 1 ELSE 0 END) FROM  {$tableName}")->fetchColumn();

$nPostersReal = (int)$db->query("SELECT SUM(CASE WHEN acceptedType=2 THEN 1 ELSE 0 END) FROM  {$tableName}")->fetchColumn();

$nAccepted = (int)$db->query("SELECT SUM(CASE WHEN isPresentationAccepted<>'' THEN 1 ELSE 0 END) FROM {$tableName}")->fetchColumn();

$nCommunicated = (int)$db->query("SELECT SUM(CASE WHEN isPresentationChecked<>'' THEN 1 ELSE 0 END) FROM {$tableName}")->fetchColumn();

$nCategorised = (int)$db->query("SELECT SUM(CASE WHEN presentationCategories<>'' THEN 1 ELSE 0 END) FROM {$tableName}")->fetchColumn();

$nSession = (int)$db->query("SELECT SUM(CASE WHEN assignedSession<>'' THEN 1 ELSE 0 END) FROM  {$tableName}")->fetchColumn();


?>

<h1>Welcome</h1>
<h2>We currently know, that...</h2>
<p>
    ... <?=$nRows?> Persons registered <br />
    ... of those, <?=$nPayed?> Persons already payed the bill.<br />
    ... But that means, <b><?=$nRows-$nPayed?> are still open to be payed!</b>
</p>
<p>
    ... <?=$nLunch?> Lunchs to be served (of that <?=$nVeggies?> Veggies) <br />
    ... <?=$nWLAN?> WLAN accounts have to be ordered. <br />
    ... <?=$nImpared?> people need eTaxi transport to the dinner restaurant.
</p>
<p>
    ... <?=$nContributions?> Contributions requested (<?=$nTalks?> talks; <?=$nPosters?> posters)<br />
    ... <?=$nContributionsReal?> Contributions accepted (<?=$nTalksReal?> talks [<?=sprintf("%+d",-$nTalks+$nTalksReal)?>]; <?=$nPostersReal?> posters [<?=sprintf("%+d",$nPostersReal-$nPosters)?>])<br />
    ... from <?=$nContributions?> decisions to take, <?=$nAccepted?> decisions are already taken and <?=$nCommunicated?> decisions are communicated <br />
    ... <?=$nCategorised?> are categorized, <?=$nSession?> even already have a session assigned <br />
    ... But that means, <b><?=$nContributions-$nSession?> are still open to be assigned!</b><br />





<?php require "lib/footer.php" ?>
