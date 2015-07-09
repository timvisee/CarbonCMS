<?php

// Enable PHPs debugging mode
ini_set('display_errors', 'On');
error_reporting(E_ALL);

/**
 * This file contains test code which will be executed before Carbon CMS starts. This test code is for development only.
 */

// Check whether Carbon's test mode is enabled or not
if(defined('CARBON_CORE_TEST') && CARBON_CORE_TEST) {
    // Show the test header, and return
    echo '[Carbon CORE] Test mode activated.' . PHP_EOL;
    return;
}

// Print the default execution header
?>
<pre>
CARBON CORE + CMS | V0.1 PRE-ALPHA | BUILD 20932 | BY TIM VISEE
---------------------------------------------------------------
CARBON CONSOLE MODE
---------------------------------------------------------------

[Carbon CORE] Loading Carbon CORE and Carbon CMS...
<?php

// Set some time constants for profiling
$GLOBALS['carbon_time'] = microtime(true);
$GLOBALS['carbon_memory'] = memory_get_usage();

// Register a shutdown function to show some profiling status after execution
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