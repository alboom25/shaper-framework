<?php
$no_direct_script  = array('Status'=> 403,'Data'=> null,'Message'=> 'No direct script access allowed');
defined('BASE_DIR') OR exit(json_encode($no_direct_script));


/*
| -------------------------------------------------------------------
|  Auto-load Models
| -------------------------------------------------------------------
| Prototype:
|
|	$autoload['models'] = array('first_model', 'second_model');
|   or use * to load all the models ie
|   $autoload['models'] = array('*');
*/
$autoload['models'] = array('*');

/*
| -------------------------------------------------------------------
|  Auto-load Libraries
| -------------------------------------------------------------------
| These are the classes located in app/libs/ directory
| Prototype:
|
|	$autoload['libraries'] = array('database', 'email', 'session');
*/

$autoload['libraries'] = array('sms.africastalking', 'mysql.dumper', 'mailer');