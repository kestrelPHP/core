<?php
// Version
define('VERSION', '0.1');
define('DIR_ROOT', '');
define('DIR_SITE', 'app/');

// Configuration
if (is_file('config.php')) {
	require_once('config.php');
}

// Install
if (!defined('DIR_ROOT')) {
	header('Location: install/index.php');
	exit;
}

// Startup
require_once(DIR_SYSTEM . 'startup.php');

// Run
require_once(DIR_APP . 'init.php');