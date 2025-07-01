<?php
// Email sending configuration and function

// Set mail headers and send email using PHP's mail() function or integrate with an SMTP library as needed.
// This example uses PHP mail() for simplicity.

function send_mail($to, $subject, $message, $headers = '') {
    $from_address = MAIL_FROM_ADDRESS ?? 'no-reply@wellbeing-directory.local';
    $from_name = MAIL_FROM_NAME ?? 'Wellbeing Service Directory';

    $default_headers = "From: $from_name <$from_address>\r\n";
    $default_headers .= "MIME-Version: 1.0\r\n";
    $default_headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers = $default_headers . $headers;

    return mail($to, $subject, $message, $headers);
}

// Example usage:
// send_mail('user@example.com', 'Welcome', '<h1>Hello!</h1>Welcome to the directory.');