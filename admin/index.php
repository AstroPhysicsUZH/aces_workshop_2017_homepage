<?php require "lib/header.php"; ?>

<?php
$nRows = (int)$db->query("SELECT count(*) FROM {$tableName}")->fetchColumn();
#$nPayed = (int)$db->query("SELECT SUM(CASE WHEN hasPayed THEN 1 ELSE 0 END) FROM  {$tableName}")->fetchColumn();


$nLunch = (int)$db->query("SELECT SUM(nPersons) FROM {$tableName}")->fetchColumn();
$nVeggies = (int)$db->query("SELECT SUM(nVeggies) FROM {$tableName}")->fetchColumn();
$nMeat = $nLunch - $nVeggies;

$nWLAN = (int)$db->query("SELECT SUM(CASE WHEN needsInet THEN 1 ELSE 0 END) FROM  {$tableName}")->fetchColumn();
#$nImpared = (int)$db->query("SELECT SUM(CASE WHEN isImpaired THEN 1 ELSE 0 END) FROM  {$tableName}")->fetchColumn();


#$nContributions = (int)$db->query("SELECT SUM(CASE WHEN talkType>0 THEN 1 ELSE 0 END) FROM  {$tableName}")->fetchColumn();

$nTalks = (int)$db->query("SELECT SUM(CASE WHEN wantsPresentTalk=1 THEN 1 ELSE 0 END) FROM  {$tableName}")->fetchColumn();

#$nPosters = (int)$db->query("SELECT SUM(CASE WHEN talkType=2 THEN 1 ELSE 0 END) FROM  {$tableName}")->fetchColumn();

#$nContributionsReal = (int)$db->query("SELECT SUM(CASE WHEN acceptedType>0 THEN 1 ELSE 0 END) FROM  {$tableName}")->fetchColumn();

#$nTalksReal = (int)$db->query("SELECT SUM(CASE WHEN acceptedType=1 THEN 1 ELSE 0 END) FROM  {$tableName}")->fetchColumn();

#$nPostersReal = (int)$db->query("SELECT SUM(CASE WHEN acceptedType=2 THEN 1 ELSE 0 END) FROM  {$tableName}")->fetchColumn();

$nAccepted = (int)$db->query("SELECT SUM(CASE WHEN isTalkAccepted<>'' THEN 1 ELSE 0 END) FROM {$tableName}")->fetchColumn();

#$nCommunicated = (int)$db->query("SELECT SUM(CASE WHEN isPresentationChecked<>'' THEN 1 ELSE 0 END) FROM {$tableName}")->fetchColumn();

#$nCategorised = (int)$db->query("SELECT SUM(CASE WHEN presentationCategories<>'' THEN 1 ELSE 0 END) FROM {$tableName}")->fetchColumn();

#$nSession = (int)$db->query("SELECT SUM(CASE WHEN assignedSession<>'' THEN 1 ELSE 0 END) FROM  {$tableName}")->fetchColumn();


?>

<h1>Welcome</h1>
<h2>We currently know, that...</h2>
<p>
    ... <?=$nRows?> Persons registered <br />
</p>
<p>
    ... <?=$nLunch?> dinners ordered (meat: <?=$nMeat?> / veggies: <?=$nVeggies?>) <br />
    ... <?=$nWLAN?> WLAN accounts have to be ordered. <br />
</p>
<p>
    ... <?=$nTalks?> talks requested<br />
    ... <?=$nAccepted?> accepted talks<br />
</p>



<?php require "lib/footer.php" ?>
