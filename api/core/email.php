<?php
require('../vendor/autoload.php');
use Aws\Ses\SesClient;
use Aws\Exception\AwsException;

function send_reset_password_email($recipient)
{
    connect($recipient, function ($recipient, $conn) {
        $q = "SELECT `id` FROM `users` WHERE `email` = '". $recipient . "'";
        $res = $conn->query($q);
        if ($res->num_rows > 0) {
            $reset_code = md5(uniqid());
            $recipient_emails = [$recipient];
            $subject = 'Peap: Reset Password Request';
            $plaintext_body = "Looks like you've requested to reset your password! Follow this link to do so: http://eap.mezaria.com/password-reset/" . $reset_code;
            $html_body = '<h1>Want to Reset your password?</h1>';
            $html_body .= "<p>Looks like you've requested to reset your password!</p>";
            $html_body .= "<p>Follow <a href='http://eap.mezaria.com/password-reset/" . $reset_code . "'>this link</a> to reset it</p>";
            $html_body .= "<p>Or copy this link into your browser: http://eap.mezaria.com/password-reset/" . $reset_code . "</p>";
            $data = [
                'email'=> $recipient,
                'reset_code' => $reset_code
            ];
            send_email('reset_password', $recipient_emails, $subject, $plaintext_body, $html_body, $data);
        } else {
            response(200, "If this address exists, we will send a reset link to it shortly - You may need to check your junk mail");
        }
    });
}

function send_email($action, $recipient_emails, $subject, $plaintext_body, $html_body, $data)
{
    require('password.php');
    $SesClient = SesClient::factory(array(
  'version' => 'latest',
  'region'  => 'us-west-2',
  'credentials' => array(
    'key' => $access_key_id,
    'secret'  => $secret_access_key,
  )
));
    $sender_email = '"Peap" <peap@mezaria.com>';
    $char_set = 'UTF-8';

    try {
        $result = $SesClient->sendEmail([
        'Destination' => [
            'ToAddresses' => $recipient_emails,
        ],
        'ReplyToAddresses' => [$sender_email],
        'Source' => $sender_email,
        'Message' => [
          'Body' => [
              'Html' => [
                  'Charset' => $char_set,
                  'Data' => $html_body,
              ],
              'Text' => [
                  'Charset' => $char_set,
                  'Data' => $plaintext_body,
              ],
          ],
          'Subject' => [
              'Charset' => $char_set,
              'Data' => $subject,
          ],
        ],
    ]);
        $messageId = $result['MessageId'];
        if ($action === 'reset_password') {
            connect($data, function ($data, $conn) {
                $q = "SELECT `id` FROM `users` WHERE `email` = '". $data['email'] . "'";
                $res = $conn->query($q);
                if ($res->num_rows > 0) {
                    $q = "UPDATE `passwords` SET `reset_code` = '" . $data['reset_code'] . "' WHERE user_id = ".$res->fetch_assoc()['id'];
                    if ($conn->query($q)) {
                        response(200, "1If this address exists, we will send a reset link to it shortly");
                    } else {
                        response(200, "2If this address exists, we will send a reset link to it shortly");
                    }
                } else {
                    response(200, "3If this address exists, we will send a reset link to it shortly");
                }
            });
        } else {
            response(200, "4If this address exists, we will send a reset link to it shortly");
        }
    } catch (AwsException $e) {
        response(500, "SES: Failed to send email", $e->getAwsErrorMessage());
    }
}
