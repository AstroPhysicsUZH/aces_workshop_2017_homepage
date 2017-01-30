<?php
/**
    defines the email message to send to the user to complete the registration
    you can use all the fields of the database, with $X['firstname']

    this should set:
    $subject
    $message

    $subject
    $message

    and maybe the ones with prefix admin to be sent to the admin mail address

    Make sure to be RFC5322 compatible:
    - use \r\n linebreaks
    - use quotes in the email address! '"name" <email@inter.net>'
**/

include "../lib/settings.inc.php";

$subject = "$confEmailKey Registration - $confShortTitle [{$X["id"]}]";
$message = preg_replace('~\R~u', "\r\n",  # make sure we have RFC 5322 linebreaks

"Dear Mrs/Mr {$X["lastname"]}

Thank you very much for your registration for the $confShortTitle at $confLocation in $confDate.

You can login into the user center with your email address and access key:
$BASEURL/user/
or use this direct link:
$BASEURL/user/login.php?op=login&email=".urlencode($X['email'])."&akey={$X['accessKey']}&rdir=index.php

Your access key is: {$X['accessKey']}

If there are any questions, simply reply to this email (relativityUZH@gmail.com).

Kind regards,
The local OK
");


/******************************************************************************/

$admin_subject = "[aces17-log] new registration";
$admin_message = preg_replace('~\R~u', "\r\n",
"new registration
{$X['id']}; {$X['lastname']}; {$X['firstname']}; {$X['email']}

" . json_encode($X));

?>
