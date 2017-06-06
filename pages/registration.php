<?php
# require_once "lib/app.php";
#require_once "pages/registration_handler.php";
?>


<h1>Registration</h1>

<p>
    Please register using the following form in time until <?=$dateRegistrationDeadline->format($date_fstr);?>.
    There is no conference fee, but the dinner costs CHF <?=$feeDinnerRegular;?>.&mdash; to be payed upon arrival.
</p>
<p>
    Please remember to book your hotel in time as well!
</p>

<p>
    For any special requests, please register anyways and contact us by email by replying to the registration email or by writing an email to <a href="mailto:relativityuzh@gmail.com">relativityUZH@gmail.com</a>.
</p>
<p>
    Please note that we publish a list of registered participants on the web page.
    This entry contains your first name, last name and affiliation.
    Other data will not be public and shall never be used for anything else.
</p>

<?php require "pages/registration_form.php"; ?>
