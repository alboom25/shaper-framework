<?php
$no_direct_script  = array('Status'=> 403,'Data'=> null,'Message'=> 'No direct script access allowed');
defined('BASE_DIR') OR exit(json_encode($no_direct_script));

class SMTPMailer {
    private $mailer;

    public function __construct() {
        global $config;
        $transport    = (new Swift_SmtpTransport($config['mail_host'], $config['mail_port'], $config['mail_encryption']))->setUsername($config['mail_username'])->setPassword($config['mail_password']);
        $this->mailer = new Swift_Mailer($transport);
    }

    public function send($subject, $body, $recepient, $cc=null, $bcc=null, $attachments=null) {
        global $config;
        $message      = new Swift_Message($subject, $body);
        $message->setFrom([$config['mail_username'] => $config['mail_sender']]);
        if ($attachments) {
            foreach ($attachments as $c) {                
                $attachment = Swift_Attachment::fromPath($c);
                $message->attach($attachment);
            }
        }
        $message->addTo($recepient);

        if ($cc) {
            foreach ($cc as $c) {
                $message->addCc($c);
            }
        }

        if ($bcc) {
            foreach ($bcc as $c) {
                $message->addBcc($c);
            }
        }
        //$message->setReadReceiptTo('alboomdj@gmail.com');
        // Send the message
        $result = $this->mailer->send($message);
        return true;
    }
}

