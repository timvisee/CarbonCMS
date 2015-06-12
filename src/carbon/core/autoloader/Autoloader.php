<?php

/**
 * Autoloader.php
 * The Autoloader class which takes care of all non-loaded classes and tries to load them when being used.
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright (c) Tim Visee 2012-2014. All rights reserved.
 */

namespace carbon\core\autoloader;

use carbon\core\autoloader\loader\BaseLoader;
use carbon\core\autoloader\loader\CarbonCoreLoader;
use carbon\core\exception\CarbonException;

// Prevent direct requests to this file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * Autoloader class
 *
 * @package carbon\core\autoloader
 * @author Tim Visee
 */
class Autoloader {

    /** @var array An array of loaders. */
    private static $loaders = Array();

    /**
     * Initialize.
     */
    public static function init() {
        // Construct the Carbon CORE loader, and add it to the loaders list
        $coreLoader = new CarbonCoreLoader();
        static::addLoader($coreLoader);
    }

    /**
     * Add a loader.
     *
     * @param BaseLoader $loader The loader.
     *
     * @throws CarbonException Throws if the loader is invalid.
     */
    public static function addLoader($loader) {
        // TODO: Make sure this loader isn't added already
        // TODO: Make sure the loader is valid

        // Make sure the loader instance is valid
        if(!($loader instanceof BaseLoader))
            throw new CarbonException("Unable to add loader, the loader is invalid.");

        // Add the loader
        static::$loaders[] = $loader;
    }

    /**
     * Get all loaders.
     *
     * @return array An array of loaders.
     */
    public static function getLoaders() {
        return static::$loaders;
    }

    /**
     * Get the number of available loaders.
     *
     * @return int Number of loaders.
     */
    public static function getLoaderCount() {
        return sizeof(static::getLoaders());
    }

    /**
     * Load a class specified by it's class name.
     *
     * @param string $className The full name of the class to load.
     */
    public static function loadClass($className) {
        // Load the class through all loaders
        foreach(static::$loaders as $loader) {
            // Make sure the loader is of a valid instance
            if(!($loader instanceof BaseLoader))
                continue;

            // Try to load the class
            $loader->load($className);
        }
    }
}
