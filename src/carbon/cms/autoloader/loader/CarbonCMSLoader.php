<?php

/**
 * CarbonCMSLoader.php
 *
 * An autoloader loader to load Carbon CMS classes and files.
 */

namespace carbon\cms\autoloader\loader;

use carbon\core\autoloader\loader\FileLoader;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CMS_INIT') or die('Access denied!');

class CarbonCMSLoader extends FileLoader {

    /**
     * Constructor.
     *
     * Set up a file loader for Carbon CMS classes and files.
     */
    public function __construct() {
        parent::__construct(CARBON_CMS_NAMESPACE, CARBON_CMS_ROOT);
    }
}
