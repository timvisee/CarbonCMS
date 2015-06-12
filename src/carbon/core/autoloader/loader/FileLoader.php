<?php

/**
 * FileLoader.php
 */

namespace carbon\core\autoloader\loader;

use carbon\core\exception\CarbonException;
use carbon\core\io\filesystem\directory\Directory;
use carbon\core\io\filesystem\directory\DirectoryHelper;
use carbon\core\io\filesystem\FilesystemObject;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

class FileLoader extends BaseLoader {

    /** @var Directory The root directory to load the files from. */
    private $root;

    /**
     * Constructor.
     *
     * @param FilesystemObject|string $root The root directory to load the classes from.
     *
     * @throws CarbonException Throws if the root directory is invalid.
     */
    public function __construct($root) {
        // Set the root directory
        $this->setRoot($root);
    }

    /**
     * Get the root directory.
     *
     * @return Directory The root directory.
     */
    public function getRoot() {
        return $this->root;
    }

    /**
     * Set the root directory.
     *
     * @param Directory|string $root The root directory to load the classes from.
     *
     * @throws CarbonException Throws if the root directory is invalid.
     */
    public function setRoot($root) {
        // Parse the root directory
        if(($root = DirectoryHelper::asDirectory($root, null)) === null)
            throw new CarbonException("Failed to set the root directory of the loader, the directory is invalid.");

        // Set the root directory
        $this->root = $root;
    }

    public function load($className) {

    }
}
