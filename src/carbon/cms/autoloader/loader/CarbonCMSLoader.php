<?php

/**
 * CarbonCMSLoader.php
 */

namespace carbon\cms\autoloader\loader;

use carbon\core\autoloader\loader\FileLoader;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CMS_INIT') or die('Access denied!');

class CarbonCMSLoader extends FileLoader {

    /** @const string The Carbon CMS namespace. */
    // TODO: Define this as a constant in the initialization file!
    const CARBON_CMS_NAMESPACE = 'carbon\\cms\\';

    /**
     * Constructor.
     */
    public function __construct() {
        // Initialize the file loader with the Carbon CMS namespace and root directory
        parent::__construct(static::CARBON_CMS_NAMESPACE, CARBON_CMS_ROOT);
    }
}
