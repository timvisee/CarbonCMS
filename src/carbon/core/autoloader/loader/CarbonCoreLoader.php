<?php

/**
 * CarbonCoreLoader.php
 */

namespace carbon\core\autoloader\loader;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

class CarbonCoreLoader extends FileLoader {

    /**
     * Constructor.
     */
    public function __construct() {
        // Initialize the file loader with the Carbon CORE root directory
        parent::__construct(CARBON_CORE_ROOT);
    }
}
