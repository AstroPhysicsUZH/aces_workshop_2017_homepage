<?php
/**
    this has all the email sending facilities
    the email templates set variables:
    - from
    - relpyto
    - subject
    - message

    and rely on $X being the dump from the database to send the email to
 */


require dirname(__FILE__).'/../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';


function _send_mail($X, $subject="(no subject)", $message="- no message -") {

    $mail = new PHPMailer;

    $mail->setFrom('relativityUZH@gmail.com', 'ACES2017 Webpage');
    $mail->addAddress($X['email'], "{$X['firstname']} {$X['lastname']}");     // Add a recipient
    $mail->addReplyTo('relativityUZH@gmail.com', 'ACES2017 OK');
    #$mail->addCC('cc@example.com');
    #$mail->addBCC('bcc@example.com');

    #$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
    #$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
    $mail->isHTML(false);                                  // Set email format to HTML

    $mail->Subject = $subject;
    $mail->Body    = $message;
    #$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    if(!$mail->send()) {
        echo 'Confirmation message could not be sent.';
        # echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        # echo 'Message has been sent';
    }
}


function _send_admin_email($X, $subject, $message) {
    $mail = new PHPMailer;

    $mail->setFrom('relativityUZH@gmail.com', 'ACES2017 Webpage');
    $mail->addAddress('relativityUZH@gmail.com');     // Add a recipient
    $mail->isHTML(false);                             // Set email format to HTML

    $mail->Subject = $subject;
    $mail->Body    = $message;

    if(!$mail->send()) {
        echo 'Admin message could not be sent.';
    } else {
    }

}





function send_registration_email($X, $BASEURL) {

    require dirname(__FILE__)."/../pages/email.registration.tmpl.php";

    _send_mail($X, $subject, $message);
    _send_admin_email($X, $admin_subject, $admin_message);

}


?>
