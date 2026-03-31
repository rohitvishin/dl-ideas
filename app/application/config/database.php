<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS (Loaded from config.php)
| -------------------------------------------------------------------
*/

$active_group = 'default';
$db['default'] = $GLOBALS['config']['db_default'] ?? array(
	'dsn'	=> '',
	'hostname' => 'db',
	'username' => 'ci_user',
	'password' => 'ci_pass',
	'database' => 'ci_db',
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => (ENVIRONMENT !== 'production'),
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);
