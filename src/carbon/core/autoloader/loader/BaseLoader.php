<?php

/**
 * BaseLoader.php
 */

namespace carbon\core\autoloader\loader;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

abstract class BaseLoader {

    public abstract function load($className);

}
