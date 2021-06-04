<?php
$no_direct_script  = array('Status'=> 403,'Data'=> null,'Message'=> 'No direct script access allowed');
defined('BASE_DIR') OR exit(json_encode($no_direct_script));

class DBConfig{
    public $defaultGroup = 'default';
    
    public $default = [
		'DSN'      => '',
		'hostname' => 'localhost',
		'username' => 'root',
		'password' => '@SecureRoot#2020',
		'database' => 'clients',
		'driver' => 'mysql',
		'DBPrefix' => '',
		'pConnect' => false,	
		'charset'  => 'utf8',
		'DBCollat' => 'utf8_general_ci',
		'swapPre'  => '',
		'encrypt'  => false,
		'compress' => false,
		'strictOn' => false,
		'failover' => [],
		'port'     => 3306,
	];

	public $sandbox = [
		'DSN'      => '',
		'hostname' => 'localhost',
		'username' => '',
		'password' => '',
		'database' => 'clients',
		'driver' => 'mysql',
		'DBPrefix' => 'beamsc_',
		'pConnect' => false,	
		'charset'  => 'utf8',
		'DBCollat' => 'utf8_general_ci',
		'swapPre'  => '',
		'encrypt'  => false,
		'compress' => false,
		'strictOn' => false,
		'failover' => [],
		'port'     => 3306,
	];

    
}