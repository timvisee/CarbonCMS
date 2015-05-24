<?php
if(!(defined('CARBON_CORE_TEST') && CARBON_CORE_TEST)):
?>
<pre>
CARBON CORE + CMS | V0.1 PRE-ALPHA | BUILD 20932 | BY TIM VISEE
---------------------------------------------------------------
CARBON CONSOLE MODE
---------------------------------------------------------------

[Carbon CORE] Loading Carbon CORE and Carbon CMS...
<?php
$GLOBALS['carbon_time'] = microtime(true);
$GLOBALS['carbon_memory'] = memory_get_usage();
register_shutdown_function(function() {
?>
<pre>
---------------------------------------------------------------
CARBON EXIT
---------------------------------------------------------------

CARBON REQUEST PERFORMANCE REPORT
MEMORY USAGE: <?=((memory_get_usage() - $GLOBALS['carbon_memory']) / (1024 * 1024)); ?> MB
PROCESS TIME: <?=(microtime(TRUE) - $GLOBALS['carbon_time']); ?> S
<?php
});
else:
    echo '[Carbon CORE] Test mode activated.' . PHP_EOL;
endif;





/**
 * index.php
 *
 * This file handles all page requests to the website in the current directory.
 * This file initializes Carbon CMS and starts the bootstrap.
 *
 * @author Tim Visee
 * @version 0.1
 * @website http://timvisee.com/
 * @copyright Copyright (C) Tim Visee 2012-2014. All rights reserved.
 */

// Define some constants
/** Defines the root directory for the website */
define('CARBON_SITE_ROOT', __DIR__);

// Load and initialize Carbon CMS
require(CARBON_SITE_ROOT . '/carbon/cms/init.php');

// Load, construct and initialize the Bootstrap
use carbon\cms\Bootstrap;
$bootstrap = new Bootstrap(true);

// Stop the Bootstrap
$bootstrap->shutdown();