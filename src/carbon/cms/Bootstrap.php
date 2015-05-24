<?php

/**
 * Bootstrap.php
 *
 * The Bootstrap class constructs all the basic classes like the Database and the Config class.
 *
 * @author Tim Visee
 * @version 0.1
 * @website http://timvisee.com/
 * @copyright Copyright (C) Tim Visee 2012-2013, All rights reserved.
 */

namespace carbon\cms;

use carbon\core\Autoloader;
use carbon\core\cache\CacheHandler;
use carbon\core\config\ConfigHandler;
use carbon\core\Core;
use carbon\core\database\driver\mysql\Database;
use carbon\core\database\driver\mysql\DatabaseStatement;
use carbon\core\datetime\DateTime;
use carbon\core\datetime\DateTimeArrayUtils;
use carbon\core\datetime\DateTimeFactory;
use carbon\core\datetime\DateTimeUtils;
use carbon\core\datetime\interval\DateInterval;
use carbon\core\datetime\interval\spec\DateIntervalSpecUtils;
use carbon\core\datetime\zone\DateTimeZone;
use carbon\core\ErrorHandler;
use carbon\core\EventManager;
use carbon\core\io\filesystem\directory\Directory;
use carbon\core\io\filesystem\directory\DirectoryHelper;
use carbon\core\io\filesystem\directory\DirectoryScanner;
use carbon\core\io\filesystem\file\File;
use carbon\core\io\filesystem\FilesystemObject;
use carbon\core\language\util\LanguageTagUtils;
use carbon\core\Listener;
use carbon\core\log\Logger;
use carbon\core\PluginManager;
use carbon\core\RegistryHandler;
use carbon\core\Router;
use carbon\core\time\Profiler;
use carbon\core\UserManager;
use carbon\core\util\DateUtils;
use carbon\core\util\IpUtils;

// Prevent direct requests to this file due to security reasons
defined('CARBON_CMS_INIT') or die('Access denied!');

/**
 * Constructs all the basic classes like the Database and the Config class.
 * @package core
 * @author Tim Visee
 */
class Bootstrap {

    /**
     * Constructor
     * @param bool $init True to initialize the bootstrap immediately
     */
    public function __construct($init = true) {
        // Initialize the bootstrap
        if($init)
            $this->init();
    }

    /**
     * Initialize the Bootstrap
     */
    public function init() {
        // Initialize and load the configuration handler and set the configuration handler instance in the Core class
        $cfg = new ConfigHandler(new FilesystemObject(CARBON_CMS_CONFIG));
        Core::setConfig($cfg);

        // Set up and initialize the error handler
        // TODO: Remove this strange test stuff!
        if(!(defined('CARBON_CORE_TEST') && CARBON_CORE_TEST))
            ErrorHandler::init(true, true, true); // TODO: Read this debug property from the configuration file!

        /**
         **
         **
         ** TEST CODE START
         **
         **
         */

        $dt = new DateTime;

        // TODO: Some PHP unit tests are going one, return here!
        if(defined('CARBON_CORE_TEST') && CARBON_CORE_TEST)
            return;

        echo '[Test] Today\'s Sunrise: ' . DateTime::now()->getSunrise(52.05, 4.19) . '<br />';

        // Set up the logger
        // TODO: Where is this relative path referring to, should this always be the Carbon root?
        $log = new Logger('log.txt');
        $log->setPrintLogs(true);
        Core::setLogger($log);

        // Log a test message
        if(!(defined('CARBON_CORE_TEST') && CARBON_CORE_TEST))
            $log->debug('Request at: ' . DateTime::now()->toCompleteString());

        die();

        /**
         **
         **
         ** TEST CODE END
         **
         **
         */

        // TODO: Should we store the Bootstrap instance in the Core class?

        // TODO: Enable or disable the debug mode in the error handler, based on the configuration file
        // TODO: Try to enable this earlier
        // TODO: Build better debugging system!

        // Should the debug mode be enabled
        if($cfg->getBool('carbon', 'debug', false) === true) {
            // Enable the debug mode in the Error Handler
            ErrorHandler::setDebug(true);

            // Enable PHPs debugging mode
            ini_set('display_errors', 'On');
            error_reporting(E_ALL);
        } else {
            // Disable the debug mode in the Error Handler
            ErrorHandler::setDebug(false);

            // TODO: Do this in bootstrap shutdown method?
            // Turn off the debugging mode (might still be enabled)
            ini_set('display_errors', 'Off');
        }

        // Initialize the router and set the router instance in the Core class
        $router = new Router();
        Core::setRouter($router);

        // Set up the database and set the database instance in the Core class
        $database = $this->setUpDatabase();
        Core::setDatabase($database);

        // Set up the registry handler class and set the registry handler in the Core class
        $options = new RegistryHandler(Core::getDatabase());
        Core::setRegistry($options);

        // Set up the caching system and set the cache instance in the Core class
        $cache = $this->setUpCache();
        Core::setCache($cache);

        // Set the cache instance in the RegistryHandler class
        $options->setCache($cache);

        // Set up and initialize the user manager and set the user manager instance in the Core class
        $user_man = new UserManager($cache, $database);
        Core::setUserManager($user_man);

        // Set the default timezone of the server
        $this->setServerTimezone();

        // Initialize the event manager and set the event manager instance in the Core class
        $event_man = new EventManager();
        Core::setEventManager($event_man);

        // Set up the plugin manager and set the plugin manager instance in the Core class
        $plugin_man = $this->setUpPluginManager();
        Core::setPluginManager($plugin_man);

        // Verify the client requesting the page, make sure this client was not banned
        $this->verifyClient();

        // Route the page request to through the router to the right controller
        $router->route();
    }

    // TODO: Move this method to the Core class
    /**
     * Set up and initialize the database system
     * @return Database Database instance
     */
    public function setUpDatabase() {
        // Get the ConfigHandler instance
        $cfg = Core::getConfig();

        // Retrieve the database connection details from the config
        $db_host = $cfg->getValue('database', 'host');
        $db_port = $cfg->getValue('database', 'port');
        $db_database = $cfg->getValue('database', 'database');
        $db_username = $cfg->getValue('database', 'username');
        $db_password = $cfg->getValue('database', 'password');

        // Get the database table prefix
        $table_prefix = $cfg->getValue('database', 'table_prefix', '');

        // Construct the database class
        $db = new Database($table_prefix);

        // TODO: Error handling for wrong database credentials and similar stuff

        // Connect to the database, try to reconnect if failed
        try {
            // Try to connect to the database
            $db->connectDatabase($db_host, $db_port, $db_database, $db_username, $db_password);
        } catch(\PDOException $ex) {
            // The connection to the database failed, try to connect once again
            try {
                // Try to connect to the database
                $db->connectDatabase($db_host, $db_port, $db_database, $db_username, $db_password);
            } catch(\PDOException $ex) {
                // The connection to the database failed twice, show an error message
                // TODO: Show proper error message
                die('Failed to connect to the database!<br />' . $ex->getMessage());
            }
        }

        // Return the database instance
        return $db;
    }

    // TODO: Move this method to the Core class
    /**
     * Set up and initialize the caching system
     * @return CacheHandler Cache instance
     */
    public function setUpCache() {
        // Get the RegistryHandler instance
        $options = Core::getRegistry();

        // Get the cache dir to use
        $cache_dir = CARBON_SITE_ROOT . DIRECTORY_SEPARATOR . ltrim($options->getString('cache.directory', DIRECTORY_SEPARATOR . 'cache'), '/\\');

        // Initialize the caching system
        $cache = new CacheHandler($cache_dir);

        // Set if cache is enabled or not
        $cache->setEnabled($options->getBool('cache.enabled', true));

        // Return the cache instance
        return $cache;
    }

    // TODO: Move this method to the Core class
    /**
     * Set up and initialize the plugin manager
     * @return PluginManager Instance of the plugin manager
     */
    public function setUpPluginManager() {
        // Get the plugins directory path from the registry
        $plugins_dir = CARBON_SITE_ROOT . DIRECTORY_SEPARATOR . ltrim(Core::getRegistry()->getString('plugin.directory', DIRECTORY_SEPARATOR . 'plugin'), '/\\');

        // Initialize/construct the Module Manager and store it in a variable
        $plugin_mngr = new PluginManager($plugins_dir, Core::getEventManager(), Core::getCache(), Core::getDatabase());

        // Load and enable all plugins
        $plugin_mngr->loadPlugins();
        $plugin_mngr->enablePlugins();

        // Return the plugin manager instance
        return $plugin_mngr;
    }

    // TODO: Should this method be moved to the core class?
    /**
     * Set the default timezone of the server
     */
    public function setServerTimezone() {
        // Retrieve the default timezone from the Options database and trim the value
        $timezone = trim(Core::getRegistry()->getString("time.def_timezone", ""));

        // If the timezone is valid, set the timezone of the server
        if($timezone != null)
            if(DateUtils::isValidTimezone($timezone))
                DateUtils::setTimezone($timezone);
    }

    // TODO: Move this method to a utilities class?
    /**
     * Verify the client requesting the page
     */
    public function verifyClient() {
        // Check if the IP of the client is unknown or not
        if(IpUtils::isClientIpUnknown()) {
            // TODO: Check if clients from unknown IP'statements are blocked or not
            // TODO: Check for country, hosting provider, check if is proxy, etc...
        } else {
            // Get the IP address of the client
            //$client_ip = IpUtils::getClientIp();

            // TODO: Check if the IP address of the client is blocked

            /*if(fsockopen($client_ip, 80, $errstr, $errno, 1)) {
                die("Proxy access not allowed");
            }*/
        }

        // TODO: Should the client language be verified?

        // TODO: Check if proxies should be blocked

        // TODO: Show info message when using site through localhost
    }

    /**
     * Stop the Bootstrap, should only be called after the Bootstrap has been initialized.
     */
    // TODO: Rename these methods to start and stop?
    public function shutdown() {
        // Get the PluginsManager instance
        $plugin_mngr = Core::getPluginManager();

        // Disable/shutdown all running plugins
        if($plugin_mngr != null)
            $plugin_mngr->disablePlugins();

        // TODO: Unregister all registered events (probably already done!)

        // TODO: Deinitialize all classes if required

        // TODO: Disconnect from database
        // TODO: Other shutdown stuff here...

        // TODO: Disable PHPstatements debug stuff?
    }
}