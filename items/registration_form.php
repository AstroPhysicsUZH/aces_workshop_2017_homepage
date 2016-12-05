<?php /* use this for intertab communication for the preview */ ?>
<script src="js/intercom.min.js"></script>


<script>
$( document ).ready(function(){

    /*
        Setup automatic price update
    */
    // set initial value
    var baseprice = <?= $baseFee; ?>; // hey genious, this is for display only, the real value will be calculated server side anyways ;)
    var dinnerprice = <?= $dinnerFee; ?>;
    var price = baseprice + dinnerprice;
    $('#price').val("CHF " + price + ".00");

    //register update handler
    $("form :input").change(function() {
        price = baseprice + (parseInt($('#npers').val())+1) * dinnerprice;
        $("#price").val("CHF " + price + ".00");
    });

    // trigger an change for inital calculation
    $("form").change();

    /*
        setup intertab com for preview
    */
	if (Intercom.supported) {
		var title = document.title;

        var $first    = $('#firstname');
        var $last     = $('#lastname');
		var $abstract = $('#abstract');
		var $title    = $('#presentationTitle');
		var $authors  = $('#coauthors');
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
            .on('change keyup paste', function() {
            /* this function is rate limited! because mathjax reloads.. */
            if (canFireRequest) {
                canFireRequest = false;

                var authorslist = "<b>" + $last.val() + ', ' + $first.val() + "<sup>1</sup></b>";
                if ($authors.val().length > 0) {
                    authorslist += "; " + $authors.val();
                }

                intercom.emit('notice', {
                    title: $title.val(),
                    authors: authorslist,
                    affil: "<sup>1</sup>"+$affil.val(),
                    abstract: $abstract.val(),
                })
                setTimeout(function() {
                    canFireRequest = true;
                }, changeRate);
            }
        });
	} else {
		alert('intercom.js is not supported by your browser. The preview function will not work');
	}
});
</script>


<form action="lib/register.php" method="post">

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
                <input id="needInet" class="left" type="checkbox" name="needInet" value="X">
            </td>
            <td>
                <label for="needInet">I require WiFi access / <br />I don't have EDUROAM</label>
            </td>
        </tr>
        <!--
        <tr>
            <td>
                <input id="c0" class="left" type="checkbox" name="stud" value="stud"> </td>
            <td>
                <label for="c0">Student</label>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="text-align:left;"> If you want to get the students rabatte, please send us a copy of your student ID by mail. </td>
        </tr>
        -->
        <thead>
            <th colspan="2">
                <h2>Presentation</h2>
            </th>
        </thead>
        <tr>
            <td colspan="2" style="text-align:left;">
                Do you want to present a poster / talk?
                <br />
                If so, please provide a short abstract (up to 200 words).
                Please note that the the selection of contributed talk and poster presentation will be made by the Session Chairs.
                Because on the limited number of speaking slots, not all requests to speak can be accommodated.
                After registration, you will be notified in due time on its acceptance.
                You will be able to change your submission after completing the registration.
                <br />
                (Deadline for abstract submission: <?=$abstractSubmissionDate->format($date_fstr);?>)
            </td>
        </tr>
        <tr>
            <td>
                <input id="r1" type="radio" name="talkType" value="none" checked>
            </td>
            <td>
                <label for="r1">None</label>
            </td>
        </tr>
        <tr>
            <td>
                <input id="r2" type="radio" name="talkType" value="poster">
            </td>
            <td>
                <label for="r2">Poster</label>
            </td>
        </tr>
        <tr>
            <td>
                <input id="r3" type="radio" name="talkType" value="talk">
            </td>
            <td>
                <label for="r3">Talk</label>
            </td>
        </tr>
        <tr>
            <td>
                <label for="presentationTitle" class="left">Titel</label>
            </td>
            <td>
                <input id="presentationTitle" type="text" name="presentationTitle" placeholder="Titel of Presentation">
                <span></span>
            </td>
        </tr>
        <tr>
            <td>
                <label for="coauthors" class="left">Co-Authors</label>
            </td>
            <td>
                <input id="coauthors" type="text" name="coauthors" placeholder="Last, First; Last, First; ...">
                <span></span>
            </td>
        </tr>
        <tr>
            <td>
                <label for="abstract" class="left">Abstract</label>
            </td>
            <td>
                <textarea id="abstract" name="abstract"
                          style="height:10em;"
                          placeholder="Short abstract (max 200 words). You can use basic latex commands (MathJax), check the preview."
                          ></textarea>
                <br />
                <?php /* open popup and trigger initial update for datatransfer */ ?>
                <a href="preview.php"  style="font-size: 80%;" onclick="window.open('preview.php', 'newwindow', 'width=400, height=600'); setTimeout(function() {$('#abstract').change()},500); return false;">open preview (disable popup blocker)</a>
            </td>
        </tr>

        <thead>
            <th colspan="2">
                <h2>Conference Dinner</h2>
            </th>
        </thead>
        <tr>
            <td colspan="2" style="text-align:left;">
                The conference dinner is included in the registration fee.
                Mobility impaired people please let us know, such that we can organize transport between the train station and the restaurant.
            </td>
        </tr>
        <tr>
            <td>
                <input id="npers"
                    class="left" type="number" name="nPersons"
                    value="0" style="width:5em;height:2em;text-align:center;" min="0" max="5">
            </td>
            <td>
                <label for="nPersons">Accompanying persons (+ CHF 100.&mdash; each)</label>
            </td>
        </tr>
        <tr>
            <td>
                <input id="c1" class="left" type="checkbox" name="isVeggie" value="checked">
            </td>
            <td>
                <label for="c1">Vegetarian meal</label>
            </td>
        </tr>
        <tr>
            <td>
                <input id="isImpaired" class="left" type="checkbox" name="isImpaired" value="checked">
            </td>
            <td>
                <label for="isImpaired">Mobility impaired</label>
            </td>
        </tr>

        <thead>
            <th colspan="2">
                <h2>Accommodation</h2>
            </th>
        </thead>
        <tr>
            <td colspan="2" style="text-align:left;">
                Please organize accomodation yourself and in time!<br />
                If you are looking for a shared appartement and possible room mates, tick the box and we will add your email to a google group where you can reach others.
                (your email adress will be seen by other participants)
            </td>
        </tr>
        <tr>
            <td>
                <input id="lookingForRoomMate" class="left" type="checkbox" name="lookingForRoomMate" value="checked">
            </td>
            <td>
                <label for="lookingForRoomMate">Looking for room mates</label>
            </td>
        </tr>

<?php if ($isItTooLate) { ?>
    <tr><td colspan="2">
    <div class='bookedout'>
        BOOKED OUT
    </div>
</td></tr>
<?php } ?>

        <thead>
            <th colspan="2">
                <h2>Send</h2>
            </th>
        </thead>
        <tr id="tr_price">
            <td>
                <label for="price" class="left">Total amount to pay:</label>
            </td>
            <td>
                <input id="price" type="text" name="price" readonly placeholder="Resulting Price...">
            </td>
        </tr>
        <tr>
            <td> Spam protection: </td>
            <td> Please enter the <b>result</b> of this equation: </td>
        </tr>
        <tr>
            <td>
                <label class="left">5 + 32 = </label>
            </td>
            <td>
                <input class="right" type="text" name="robot" style="width:5em;" required pattern="37">
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

</form>
