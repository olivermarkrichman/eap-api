<?php
require('../vendor/autoload.php');
require('./password.php');

use Aws\Ses\SesClient;
use Aws\Exception\AwsException;

$SesClient = new SesClient([
    'version' => 'latest',
    'region'  => 'us-west-2',
    'credentials' => [
        'key'    => $access_key_id,
        'secret' => $secret_access_key,
    ]
]);
$sender_email = 'eap@mezaria.com';

function confirm_account($recipient)
{
    echo $recipient;
}

function sign_up($recipient)
{
    echo $recipient;
}

function reset_password($recipient)
{
    $subject = 'Peap: Reset your password';
    $plaintext_body = 'Reset your password here: ';
    $html_body = `
		<h1>peap</h1>
		<p>Reset your password here:</p>
		<a href="link">link</a>
	`;
    send_email(array($recipient), $subject, $plaintext_body, $html_body);
}

function send_email($recipients, $subject, $plaintext_body, $html_body)
{
    try {
        $result = $SesClient->sendEmail([
            'Destination' => [
                'ToAddresses' => $recipients,
            ],
            'ReplyToAddresses' => [$sender_email],
            'Source' => $sender_email,
            'Message' => [
              'Body' => [
                  'Html' => [
                      'Charset' => 'UTF-8',
                      'Data' => $html_body,
                  ],
                  'Text' => [
                      'Charset' => 'UTF-8',
                      'Data' => $plaintext_body,
                  ],
              ],
              'Subject' => [
                  'Charset' => 'UTF-8',
                  'Data' => $subject,
              ],
            ],
        ]);
        $messageId = $result['MessageId'];
        return true;
    } catch (AwsException $e) {
        return false;
    }
}
