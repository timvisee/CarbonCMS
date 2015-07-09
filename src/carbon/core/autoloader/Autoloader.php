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

    // TODO: No other class can be loaded before the autoloader is initialized, make sure no other classes are used before this process is finished!
    // TODO: Maybe somehow pre-load the required classes before the initialization process.

    /** @var bool Set whether the autoloader is initialized or not. */
    protected static $init = false;

    /** @var array An array of loaders. */
    protected static $loaders = Array();

    // TODO: Update these constants below!
    /** @const string The core namespace, used by the fallback autoloader. */
    const CORE_NAMESPACE = "carbon\\core";
    /** @const string The core namespace directory, used by the fallback autoloader. */
    const CORE_NAMESPACE_DIR = CARBON_CORE_ROOT;
    const CORE_NAMESPACE_FILE_EXTENSION = '.php';

    /**
     * Initialize the autoloader.
     * The autoloader must be initialized before it may be used.
     *
     * @return bool True on success, false on failure. True will also be returned if the autoloader was initialized already.
     */
    // TODO: Throw exception if the initialization failed!
    public static function init() {
        // Make sure the autoloader isn't initialized already
        if(static::isInit())
            return true;

        // Register the auto loader method
        if(spl_autoload_register(__CLASS__ . '::loadClass', false, true) === false)
            return false;

        // Clear the list of loaders
        static::removeAllLoaders();

        // Construct the Carbon CORE loader, and add it to the loaders list
        // TODO: Should we add the core loader here?
        $coreLoader = new CarbonCoreLoader();
        static::addLoader($coreLoader);

        // TODO: Fall back to basic autoloader here?

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
    // TODO: Rename this method to exit?
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

            // Re-index the loaders list and return the result
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
     *
     * @return bool True if the class was loaded, false if not.
     */
    public static function loadClass($className) {
        // Show a debug message
        // TODO: Remove this on release!
        if(!(defined('CARBON_CORE_TEST') && CARBON_CORE_TEST))
            echo '[AutoLoader] Loading class: ' . $className . '<br />';

        // Make sure the class isn't loaded already
        if(static::isClassLoaded($className))
            return true;

        // Load the class through all loaders
        foreach(static::$loaders as $loader) {
            // Make sure the loader is of a valid instance
            if(!($loader instanceof BaseLoader))
                continue;

            // Try to load the class, return true if it's loaded successfully
            if($loader->load($className))
                return true;
        }

        // Unable to load the class, try to load with fallback autoloader

        // Get the core namespace and it's length, suffixed with backslash
        $coreNamespace = rtrim(static::CORE_NAMESPACE, '\\') . '\\';
        $coreNamespaceLen = strlen($coreNamespace);

        // Check whether the class being loaded is in the namespace
        if(substr($className, 0, $coreNamespaceLen) == $coreNamespace) {
            // Remove the namespace prefix from the class name
            $strippedClassName = substr($className, $coreNamespaceLen);

            // Determine the path to load the class file from
            $classFile = rtrim(static::CORE_NAMESPACE_DIR, '/\\') . DIRECTORY_SEPARATOR . $strippedClassName . static::CORE_NAMESPACE_FILE_EXTENSION;

            // Load the file if it exists
            if(is_file($classFile)) {
                // Load the class file
                /** @noinspection PhpIncludeInspection */
                require_once($classFile);

                // Check whether the class is loaded successfully
                if(static::isClassLoaded($className))
                    return true;
            }
        }

        // Failed to load the class, return false
        return false;
    }

    /**
     * Check whether a class is loaded.
     *
     * @param string $className The full name of the class, with it's namespace included.
     *
     * @return bool True if the class is loaded, false if not.
     */
    // TODO: Handle invalid instances.
    public static function isClassLoaded($className) {
        return class_exists($className, false);
    }

    /**
     * Load a class file.
     *
     * @param string $classFile The file path of the class file to load.
     *
     * @return bool True on success, false on failure.
     *
     * @throws CarbonException Throws if the class file path is invalid.
     */
    public static function loadClassFile($classFile) {
        // Make sure the file exists
        if(!is_file($classFile))
            throw new CarbonException("Failed to load class file, the file path is invalid.");

        // Try to load the class file
        /** @noinspection PhpIncludeInspection */
        require_once($classFile);
        return true;
    }
}
