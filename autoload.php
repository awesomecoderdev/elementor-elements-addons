<?php

/**
 * The plugin core autoload file
 *
 * @link              https://awesomecoder.dev
 * @since             1.0.0
 * @package           Elementor Elements Addons
 *																__
 *	                                                           |  |
 *	  __ ___      _____  ___  ___  _ __ ___   ___  ___ ___   __|  | ___ _ ____
 *	 / _` \ \ /\ / / _ \/ __|/ _ \| '_ ` _ \ / _ \/ __/ _ \ / _`  |/ _ \ ' __|
 *	| (_| |\ V  V /  __/\__ \ (_) | | | | | |  __/ (_| (_) | (_|  |  __/  |
 *	\__,_| \_/\_/ \___||___/\___/|_| |_| |_|\___|\___\___/ \__,___|\___|__|
 *
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

spl_autoload_register(function ($class) {
    // project-specific namespace prefix
    $prefix = 'AwesomeCoder\\';

    // base directory for the namespace prefix
    $base = EEA_PLUGIN_PATH . "includes/";

    // does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }

    // get the relative class name
    $classes = substr($class, $len);

    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = $base . str_replace('\\', '/', $classes) . '.php';

    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});
