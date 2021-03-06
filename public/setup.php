<?php
/**
 * ep-3 Hotelsystem Setup Tool
 *
 * This tool helps to initialize and setup the database.
 */

ob_start();

chdir(dirname(__DIR__));

/**
 * Quickly check the current PHP version.
 */
if (version_compare(PHP_VERSION, '5.4.0') < 0) {
    exit('PHP 5.4+ is required (currently running PHP ' . PHP_VERSION . ')');
}

/**
 * We are using composer (getcomposer.org) to install and autoload the dependencies.
 * Composer will create the entire vendor directory for us, including the autoloader.
 */
$autoloader = 'vendor/autoload.php';

if (! is_readable($autoloader)) {

    $charon = 'module/Base/Charon.php';

    if (! is_readable($charon)) {
        exit('Base module not found');
    }

    /**
     * Display an informative error page.
     */
    require $charon;

    Base\Charon::carry('application', 'installation', 1);
}

/**
 * Load and prepare the autoloader.
 */
require $autoloader;

/**
 * Initialize our application with the setup configuration file and run!
 */
Zend\Mvc\Application::init(require 'config/setup.config.php')->run();