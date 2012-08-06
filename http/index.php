<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/**
 * Maintenance Flag
 */
$allowableIps = array('127.0.0.1', '98.223.124.152');
$allowableIps = array();
if (file_exists('maintenance.flag') && !in_array($_SERVER['REMOTE_ADDR'], $allowableIps)) {
    include_once dirname(__FILE__) . '/errors/503.php';
    exit;
}

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

// Start session
//Zend_Session::start();

// Run it!
$application->bootstrap()->run();