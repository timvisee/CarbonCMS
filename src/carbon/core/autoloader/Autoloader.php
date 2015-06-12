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

    /** @var bool Set whether the autoloader is initialized or not. */
    protected static $init = false;

    /** @var array An array of loaders. */
    protected static $loaders = Array();

    /**
     * Initialize the autoloader.
     * The autoloader must be initialized before it may be used.
     *
     * @return bool True on success, false on failure. True will also be returned if the autoloader was initialized already.
     */
    public static function init() {
        // Make sure the autoloader isn't initialized already
        if(static::isInit())
            return true;

        // Clear the list of loaders
        static::removeAllLoaders();

        // Construct the Carbon CORE loader, and add it to the loaders list
        $coreLoader = new CarbonCoreLoader();
        static::addLoader($coreLoader);

        // Register the auto loader method
        if(spl_autoload_register(__CLASS__ . '::loadClass', false, true) === false)
            return false;

        // Set the initialization flag to true and return
        static::$init = true;
        return true;
    }

    /**
     * Check whether the autoloader is initialized or not.
     *
     * @return bool True if the autoloader is initialized, false otherwise.
     */
    public static function isInit() {
        return static::$init;
    }

    /**
     * Finalize the autoloader. The autoloader should be initialized before it may be finalized.
     * The autoloader may not be used after it has been finalized unless it's initialized again.
     *
     * @return bool True on success, false on failure. True will also be returned if the autoloader wasn't initialized.
     */
    public static function finalize() {
        // Make sure the autoloader is initialized
        if(!static::isInit())
            return true;

        // Unregister the autoloader function
        if(spl_autoload_unregister(__CLASS__ . '::loadClass') === false)
            return false;

        // Clear the list of loaders
        static::removeAllLoaders();

        // Set the initialization flag, return the result
        static::$init = false;
        return true;
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
     * Check whether a specific loader is available.
     *
     * @param BaseLoader $loader The loader to check for.
     *
     * @return bool True if this loader is available, false otherwise.
     *
     * @throws CarbonException Throws if the <var>$loader</var> instance is invalid.
     */
    public static function hasLoader($loader) {
        // Make sure the loader instance is valid
        if(!($loader instanceof BaseLoader))
            throw new CarbonException("Failed to check whether a loader is available, because the instance is invalid.");

        // Check whether this is one of the available loaders, return the result
        return in_array($loader, static::$loaders);
    }

    /**
     * Remove a loader.
     *
     * @param BaseLoader|int $loader The loader instance, or the index of the loader.
     *
     * @return bool True if any loader was removed, false otherwise.
     *
     * @throws CarbonException Throws if the loader index is out of bound.
     */
    public static function removeLoader($loader) {
        // Remove the loader by index if the param is an integer
        if(is_int($loader)) {
            // Make sure the index is in-bound
            if($loader < 0 || $loader >= static::getLoaderCount())
                throw new CarbonException("Failed to remove loader, index out of bound.");

            // Remove the actual loader, and re-index the array
            unset(static::$loaders[$loader]);
            static::$loaders = array_values(static::$loaders);
            return true;
        }

        // Remove an actual loader instance
        else if($loader instanceof BaseLoader) {
            // Get all array elements that contain this loader, return false if there's none
            $removeLoaders = array_keys(static::$loaders, $loader);
            if(sizeof($removeLoaders) == 0)
                return false;

            // Remove the loaders
            foreach($removeLoaders as $key)
                unset(static::$loaders[$key]);

            // Re-index the loaders list and returh the result
            static::$loaders = array_values(static::$loaders);
            return true;
        }

        // Failed to remove loader, return false
        return false;
    }

    /**
     * Remove all the available loaders.
     */
    public static function removeAllLoaders() {
        static::$loaders = Array();
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
