<?php
$no_direct_script  = array('Status'=> 403,'Data'=> null,'Message'=> 'No direct script access allowed');
defined('BASE_DIR') OR exit(json_encode($no_direct_script));

//set default timezone


$config['timezone'] = 'Africa/Nairobi';

$config['log_errors'] = true;

$config['base_url'] = 'http://localhost/sharper/';

// email details
$config['mail_host'] = 'mail.smtp.com';
$config['mail_port'] = '465';
$config['mail_username'] = 'admin@smtp.com';
$config['mail_password'] = 'secure_password';
$config['mail_encryption'] = 'ssl'; //ie ssl, tls
$config['mail_sender'] = 'My company name';


?>