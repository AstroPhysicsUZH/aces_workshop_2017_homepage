<?php
# require_once "lib/app.php";
#require_once "pages/registration_handler.php";
?>


<h1>Registration</h1>

<p>
    Please register using the following form in time untill <?=$dateRegistrationDeadline->format($date_fstr);?>.
    There is no confernece fee, but the dinner costs CHF <?=$feeDinnerRegular;?>.&mdash; to be payed upon arrival.
</p>
<p>
    Please remember to book your hotel in time as well!
</p>

<?php if ($isItTooLate) { ?>
    <div class='bookedout'>
        BOOKING CLOSED
    </div>
<?php } ?>


<p>
    For any special requests, please register anyways and contact us by email by replaying to the registration email or by writing an email to <a href="mailto:relativityuzh@gmail.com">relativityUZH@gmail.com</a>.
</p>
<p>
    Please note that we publish a list of registered participants publically on the webpage.
    This entry contains your first name, last name and affiliation.
    Other data will not be public and shall never be used for anything else.
</p>

<?php require "pages/registration_form.php"; ?>
