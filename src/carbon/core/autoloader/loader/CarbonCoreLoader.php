<?php

/**
 * CarbonCoreLoader.php
 */

namespace carbon\core\autoloader\loader;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

class CarbonCoreLoader extends FileLoader {

    /** @const string The Carbon CORE namespace. */
    // TODO: Define this as a constant in the initialization file!
    const CARBON_CORE_NAMESPACE = 'carbon\\core\\';

    /**
     * Constructor.
     */
    public function __construct() {
        // Initialize the file loader with the Carbon CORE namespace and root directory
        parent::__construct(static::CARBON_CORE_NAMESPACE, CARBON_CORE_ROOT);
    }
}
