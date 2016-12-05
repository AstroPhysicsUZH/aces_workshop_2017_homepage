<?php
require_once 'lib/auth.php';


require "lib/header.php";
require "lib/menu.php";

?>

<main>
<article>
    <h1>Participant's Area</h1>
    <p>
        logged in as <?=$P["title"];?> <?=$P["lastname"];?>; <?=$P["firstname"];?>
    </p>

    <h2>Contact Data</h2>
    <p>
        We use this address to send you letters:<br />
        <span class="small">
            (if you forgot to add your name in the address field, please do so now <a href="edit.php">here</a> or by clicking the address)
        </span>
    </p>
    <a href="edit.php" class="nolink">
        <code>
            <?=nl2br($P['address'])?>
        </code>
    </a>

    <h2>Payment status:</h2>
<?php
    if ($P['hasPayed']) {
        echo '
        <p class="notice centered">
            We received your payment. Thank you very much.<br />
            Get your invoice on the left hand side (Downloads)
        </p>';
    }
    else {
        echo '
        <p class="warning centered">
            We haven\'t received your payment yet<br />
            <span class="small">But it might take several days time for the transfer to actually reach us, so please be patient or contact us by email.</span>
        </p>';
    }

    if ($P['paymentNotes']) {
        echo "
        <p class=\"notice centered\"><span class='small'>
            <b>Notes from the organizer:</b><br />
            <span>
                {$P['paymentNotes']}
            </span></span>
        </p>";
    }
?>
    <ul>
        <li>
            Amount to pay: CHF <?=$P['price']?>.00<br />
            <span class="small">(you bring <?=$P['nPersons']-1>0 ? $P['nPersons']-1 : "no"?> additional person<?=$P['nPersons']>2 ? "s" : ""?>)</span>
        </li>
        <!-- yeah better hide this
        <li>Amount received: CHF <?=$P['amountPayed']>0?$P['amountPayed']:0?>.00</li>
        -->
    </ul>


    <h2>Presentations / Talks / Posters</h2>
    <p>
<?php
if ($P['talkType'] == 0) {
    print "You are not presenting anything. ";
}
else {

    print "You applied to present a "
        . ($P['talkType'] == 1 ? "talk" : "poster")
        . " with title:<br />\n"
        . "<b>&#x3008; " . $P['presentationTitle'] . " &#x3009;</b><br />\n";

    if (!$P["isPresentationChecked"]) {
        print "No decision has been made yet. Please be patient.<br />\n
        You can edit your submission <a href=''>here</a>.\n";
    }
    else {
        if ($P["isPresentationAccepted"]) {
            print "Congratulations, your presentation was accepted. ";

            if (! ($P['talkType']==$P['acceptedType'])) {
                print "Please pay attention, that the organiser decided you should better present in form of a <b>" . ($P['acceptedType']==1? "talk" : "poster") . "</b>. Details will be listed here shortly.";
            }

        }
        else {
            print "We're sorry! Sadly, your request was rejected. For further information, please contact the organiser of the according parallel session directly or the OK.";
        }

    }
}
?>


<!--  <code>
<?php
    print_r($_SESSION);
?>
    </code>-->
</article>
</main>

<?php
require "lib/footer.php";
?>
