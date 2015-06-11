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