<?php

// Prevent direct requests to this file due to security reasons
defined('CARBON_SITE_ROOT') or die('Access denied!');

// Make sure Carbon CMS is only initialized once
if(defined('CARBON_CMS_INIT_DONE') && CARBON_CMS_INIT_DONE === true)
    return;

// Define various Carbon CMS constants
/** The Carbon CMS namespace. */
define('CARBON_CMS_NAMESPACE', 'carbon\\cms\\');
/** The required PHP version to run Carbon CMS. */
define('CARBON_CMS_PHP_VERSION_REQUIRED', '5.3.1');
/** The root directory of Carbon CMS. */
define('CARBON_CMS_ROOT', __DIR__);
/** The version name of the currently installed Carbon CMS instance. */
define('CARBON_CMS_VERSION_NAME', '0.1');
/** The version code of the currently installed Carbon CMS instance. */
define('CARBON_CMS_VERSION_CODE', 1);
/** The path to the configuration file. */
define('CARBON_CMS_CONFIG', CARBON_SITE_ROOT . '/config/config.php');

// Make sure the current PHP version is supported
if(version_compare(phpversion(), CARBON_CMS_PHP_VERSION_REQUIRED, '<'))
    // PHP version the server is running is not supported, show an error message
    // TODO: Show proper error message
    die('This server is running PHP ' . phpversion() . ', the required PHP version to start Carbon CMS is PHP 5.3.1 or higher,
            please install PHP 5.3.1 or higher on your server!');

/** Defines whether Carbon CMS is initializing or initialized. */
define('CARBON_CMS_INIT', true);

// TODO: Improve the statement below!
/** Defines the file path of the Carbon Core configuration file */
define('CARBON_CORE_CONFIG', CARBON_CMS_CONFIG);

// Load and initialize Carbon Core
require(CARBON_SITE_ROOT . '/carbon/core/init.php');

// Make sure Carbon Core is initialized successfully
if(!(defined('CARBON_CORE_INIT_DONE') && CARBON_CORE_INIT_DONE === true))
    return;

// Set up the autoloader for Carbon CMS
require_once(CARBON_CMS_ROOT . '/autoloader/loader/CarbonCMSLoader.php');

use carbon\cms\autoloader\loader\CarbonCMSLoader;
use carbon\core\autoloader\Autoloader;

Autoloader::addLoader(new CarbonCMSLoader());

// Carbon CMS initialized successfully, define the CARBON_CMS_INIT_DONE constant to store the initialization state
/** Defines whether Carbon CMS is initialized successfully. */
define('CARBON_CMS_INIT_DONE', true);
