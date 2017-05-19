<?php /* use this for intertab communication for the preview */ ?>
<script src="js/intercom.min.js"></script>


<script>
$( document ).ready(function(){

    /*
        Setup automatic price update
    */
    // set initial value
    var baseprice = 0; // hey genious, this is for display only, the real value will be calculated server side anyways ;)
    var dinnerprice = <?= $feeDinnerRegular; ?>;
    var price = baseprice + dinnerprice;
    $('#price').val("CHF " + price + ".00");

    //register update handler
    $("form :input").change(function() {
        price = baseprice + (parseInt($('#nPersons').val())) * dinnerprice;
        $("#price").val("CHF " + price + ".00");
    });

    // trigger an change for inital calculation
    $("form").change();

/* setup intertab com for preview */
    if (Intercom.supported) {
        var title = document.title;

        var $first    = $('#firstname');
        var $last     = $('#lastname');
        var $abstract = $('#talkAbstract');
        var $title    = $('#talkTitle');
        var $authors  = $('#talkCoauthors');
        var $authorsaffil = $('#talkCoauthorsAffil');
        var $affil    = $("#affiliation");

        var intercom = new Intercom();
        var changeRate = 200; // only send each X ms an update
        var canFireRequest = true;

        $abstract
        .add($title)
        .add($authors)
        .add($first)
        .add($last)
        .add($affil)
        .add($authorsaffil)
        .on('change keyup paste', function() {
            /* this function is rate limited! because mathjax reloads.. */
            if (canFireRequest) {
                canFireRequest = false;

                var authorslist = "<b>" + $last.val() + ', ' + $first.val() + " [1]</b>";
                if ($authors.val().length > 0) {
                    var s = $authors.val().split(';');
                    s.forEach(function (value, i) {
                        authorslist += "<br />\n" + value;
                    });
                }
                var affilia = "<sup>[1]</sup> "+$affil.val();
                if ($authorsaffil.val().length > 0) {
                    var s = $authorsaffil.val().replace(/(?:\r\n|\r|\n)/g, '\n').split('\n');
                    s.forEach(function (value, i) {
                        console.log('%d: %s', i, value);
                        affilia += "<br />\n<sup>[" + (i+2) + "]</sup> "+value;
                    });
                }

                intercom.emit('notice', {
                    title: $title.val(),
                    authors: authorslist,
                    affil: affilia,
                    abstract: $abstract.val(),
                })
                setTimeout(function() {
                    canFireRequest = true;
                }, changeRate);
            }
        });
        }
        else {
          alert('intercom.js is not supported by your browser. The preview function will not work');
        }
    });
</script>


<form action="pages/registration_handler.php" method="post">

<?php if ($isItTooLate) { ?>
    <div class='bookedout'>
        BOOKED OUT
    </div>
    <fieldset disabled="disabled" >
<?php } ?>

    <table class="registration">
        <thead>
            <th colspan="2">
                <h2>Personal Details</h2>
            </th>
        </thead>
        <tr>
            <td colspan="2" style="text-align:left;">
                Please enter your personal details.
            </td>
        </tr>
        <tr>
            <td>
                <label for="title" class="left">Title</label>
            </td>
            <td>
                <input id="title" type="text" name="title" placeholder="- / PhD / Dr / Prof">
                <span></span>
            </td>
        </tr>
        <tr>
            <td>
                <label for="firstname" class="left">First name</label>
            </td>
            <td>
                <input id="firstname" type="text" name="firstname" required placeholder="Enter First Name">
                <span></span>
            </td>
        </tr>
        <tr>
            <td>
                <label for="lastname" class="left">Last name</label>
            </td>
            <td>
                <input id="lastname" type="text" name="lastname" required placeholder="Enter Last Name">
                <span></span>
            </td>
        </tr>
        <tr>
            <td>
                <label for="email" class="left">Email</label>
            </td>
            <td>
                <input type="email" name="email" required placeholder="Enter Email">
                <span></span>
            </td>
        </tr>
        <tr>
            <td>
                <label for="affiliation" class="left">Affiliation</label>
            </td>
            <td>
                <input id="affiliation" type="text" name="affiliation" placeholder="Enter Affiliation">
                <span></span>
            </td>
        </tr>
        <tr>
            <td>
                <label for="address" class="left">Full Address</label>
            </td>
            <td>
                <textarea name="address"
                          style="height:6em;"
                          placeholder="Enter your FULL ADDRESS, including your FULL NAME and country, as it should be written on a letter."
                          required></textarea>
                <span></span>
            </td>
        </tr>
        <tr>
            <td>
                <input type="hidden" name="needsInet" value="FALSE">
                <input id="needsInet" class="left" type="checkbox" name="needsInet" value="TRUE">
            </td>
            <td>
                <label for="needsInet">I require WiFi access / <br />I don't have EDUROAM</label>
            </td>
        </tr>



        <thead>
            <th colspan="2">
                <h2>Presentation</h2>
            </th>
        </thead>
        <tr>
            <td colspan="2" style="text-align:left;">
                Do you want to present a talk?
                <br />
                If so, please provide a short abstract (up to 200 words).
                After registration, you will be notified in due time on its acceptance.
                You will be able to change your submission after completing the registration.
                <br />
                You can leave the "authors affiliation" field empty, then your own affiliation will be used.
                Otherwise write one affiliation per line, they will be numbered automatically.
                Check the preview!
                <br />
                (Deadline for abstract submission: <?=$dateAbstractSubmissionDeadline->format($date_fstr);?>)
            </td>
        </tr>
        <tr>
            <td>
                <input type="hidden" name="wantsPresentTalk" value="FALSE">
                <input id="wantsPresentTalk" class="left" type="checkbox" name="wantsPresentTalk" value="TRUE">
            </td>
            <td>
                <label for="wantsPresentTalk">I'd like to present a talk</label>
            </td>
        </tr>
        <tr>
            <td>
                <label for="talkTitle" class="left">Titel</label>
            </td>
            <td>
                <input id="talkTitle" type="text" name="talkTitle" placeholder="Titel of Presentation">
                <span></span>
            </td>
        </tr>
        <tr>
            <td>
                <label for="talkCoauthors" class="left">Co-Authors</label>
            </td>
            <td>
                <input id="talkCoauthors" type="text" name="talkCoauthors" placeholder="Last, First [#]; Last, First [#]; ...">
                <span></span>
            </td>
        </tr>
        <tr>
            <td>
                <label for="talkCoauthorsAffil" class="left">Additional Affiliations</label>
            </td>
            <td>
                <textarea id="talkCoauthorsAffil" name="talkCoauthorsAffil"
                          style="height:4em;"
                          placeholder="list additional affiliations here, one per line, will be automatically numbered. Start with second (first is your affiliation)"
                          ></textarea>
                <br />
            </td>
        </tr>
        <tr>
            <td>
                <label for="talkAbstract" class="left">Abstract</label>
            </td>
            <td>
                <textarea id="talkAbstract" name="talkAbstract"
                          style="height:10em;"
                          placeholder="Short abstract (max 200 words). You can use basic latex commands (MathJax), check the preview."
                          ></textarea>
                <br />
                <?php /* open popup and trigger initial update for datatransfer */ ?>
                <a href="preview.php"  style="font-size: 80%;" onclick="window.open('preview.php', 'newwindow', 'width=400, height=600'); setTimeout(function() {$('#talkAbstract').change()},500); return false;">open preview (disable popup blocker)</a>
            </td>
        </tr>

        <thead>
            <th colspan="2">
                <h2>Conference Dinner</h2>
            </th>
        </thead>
        <tr>
            <td colspan="2" style="text-align:left;">
                The conference dinner takes place in the evening of the first day.
                It costs CHF <?=$feeDinnerRegular;?>.&mdash; per person and has to be payed in cash upon arrival.
            </td>
        </tr>
        <tr>
            <td>
                <input id="nPersons"
                    class="left" type="number" name="nPersons"
                    value="1" style="width:5em;height:2em;text-align:center;" min="0" max="5">
            </td>
            <td>
                <label for="nPersons">Total persons (including yourself)</label>
            </td>
        </tr>
        <tr>
            <td>
                <input id="nVeggies"
                    class="left" type="number" name="nVeggies"
                    value="0" style="width:5em;height:2em;text-align:center;" min="0" max="5">
            </td>
            <td>
                <label for="nVeggies">Vegetarian meals</label>
            </td>
        </tr>


        <thead>
            <th colspan="2">
                <h2>Accommodation</h2>
            </th>
        </thead>
        <tr>
            <td colspan="2" style="text-align:left;">
                Please organize accomodation yourself and in time!
            </td>
        </tr>

        <thead>
            <th colspan="2">
                <h2>Send</h2>
            </th>
        </thead>
        <tr id="tr_price">
            <td>
                <label for="price" class="left">
                    Total amount to pay:<br />
                    upon arrival
                </label>
            </td>
            <td>
                <input id="price" type="text" name="price" readonly placeholder="Resulting Price...">
            </td>
        </tr>
        <tr>
            <td> Spam protection: </td>
            <td> Please enter the <b>middle digit</b> of this number:</td>
        </tr>
<?php
$rnd = rand(100,999);
?>
        <tr>
            <td>
                <label class="left"><?=$rnd;?></label>
            </td>
            <td>
                <input class="right" type="text" name="robot" style="width:5em;" required pattern="[0-9]">
                <span></span>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <input type="submit" value="Submit">
            </td>
        </tr>
    </table>
<?php if ($isItTooLate) { ?>
</fieldset>
<?php } ?>

<input type="hidden" name="check" value="<?=$rnd;?>" >
<input type="hidden" name="action" value="save" >

</form>
