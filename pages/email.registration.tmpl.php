<?php
/**
    defines the email message to send to the user to complete the registration
    you can use all the fields of the database, with $X['firstname']

    this should set:
    $from
    $replyto
    $subject
    $message

    Make sure to be RFC5322 compatible:
    - use \r\n linebreaks
    - use quotes in the email address! '"name" <email@inter.net>'
**/

$from    = '"LISA Symposium Website" <relativityUZH@gmail.com>';
$replyto = $from;
$subject = "11th LISA Symposium Registration [{$X["id"]}]";
$message = preg_replace('~\R~u', "\r\n",  # make sure we have RFC 5322 linebreaks

"Dear Mrs/Mr {$X["lastname"]}

Thank you very much for your registration for the 11th LISA Symposium in Zurich.

!! Please confirm your registration by clicking on this link: !!
http://www.physik.uzh.ch/events/lisa2016/activate.php?akey={$X['accessKey']}

You can login into the user center with your email address and access key:
http://www.physik.uzh.ch/events/lisa2016/user/
or use this direct link:
http://www.physik.uzh.ch/events/lisa2016/user/login.php?op=login&email=".urlencode($X['email'])."&akey={$X['accessKey']}&rdir=index.php

Your access key is: {$X['accessKey']}

In the user center you can:
* Download an invoice:
http://www.physik.uzh.ch/events/lisa2016/user/login.php?op=login&email=".urlencode($X['email'])."&akey={$X['accessKey']}&rdir=invoice_.php
* Download an invitation letter:
http://www.physik.uzh.ch/events/lisa2016/user/login.php?op=login&email=".urlencode($X['email'])."&akey={$X['accessKey']}&rdir=invite.php
* Change your details:
http://www.physik.uzh.ch/events/lisa2016/user/login.php?op=login&email=".urlencode($X['email'])."&akey={$X['accessKey']}&rdir=edit.php


Your registration fee is: CHF {$X["price"]}.--

Please transfer it with bank transfer to:

Rechnungswesen der Universitat Zurich
LISA Symposium
8057 Zurich

IBAN-Nr.: CH12 0900 0000 3109 1810 4
Swift/BIC: POFICHBEXXX
Message: ".sprintf('%03d', intval($X["id"]))." {$X["lastname"]}

If there are any questions, simply reply to this email (relativityUZH@gmail.com).

Kind regards,
The local OK
");

?>
