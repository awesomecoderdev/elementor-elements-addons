<?php

/**
 * The plugin core file
 *
 * @link              https://awesomecoder.dev
 * @since             1.0.0
 * @package           Elements
 *
 * @wordpress-plugin
 * Plugin Name:       Elements Addons for Elementor
 * Plugin URI:        https://orioca.com
 * Description:	      The Elementor Elements plugin you install after Elementor! Packed with custom stunning free elements including Advanced Data Table, Event Calendar, Filterable Gallery, WooCommerce, and many more.
 * Version:           1.0.0
 * Author:            Mohammad Ibrahim
 * Author URI:        https://awesomecoder.dev/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       elements
 * Domain Path:       /languages
 *
 *
 * WC tested up to: 8.2.1
 * Elementor tested up to: 3.17.1
 * Elementor Pro tested up to: 3.17.0
 *
 *																__
 *	                                                           |  |
 *	  __ ___      _____  ___  ___  _ __ ___   ___  ___ ___   __|  | ___ _ ____
 *	 / _` \ \ /\ / / _ \/ __|/ _ \| '_ ` _ \ / _ \/ __/ _ \ / _`  |/ _ \ ' __|
 *	| (_| |\ V  V /  __/\__ \ (_) | | | | | |  __/ (_| (_) | (_|  |  __/  |
 *	\__,_| \_/\_/ \___||___/\___/|_| |_| |_|\___|\___\___/ \__,___|\___|__|
 *
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly


// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}


/**
 * Currently plugin constants.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * @since 1.0.0
 */
define('EEA_PLUGIN_FILE', __FILE__);
define('EEA_PLUGIN_URL', trailingslashit(plugin_dir_url(__FILE__)));
define('EEA_PLUGIN_PATH', trailingslashit(plugin_dir_path(__FILE__)));
define('EEA_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('EEA_PLUGIN_TEXTDOMAIN', basename(EEA_PLUGIN_BASENAME, ".php"));
define('EEA_PLUGIN_VERSION', '1.0.0');

/**
 * Including composer autoloader globally.
 *
 * @since 3.0.0
 */
if (file_exists(EEA_PLUGIN_PATH . "autoload.php")) {
    require_once  EEA_PLUGIN_PATH . "autoload.php";
}

/**
 * Register New Widget.
 *
 * Include widget file and register widget class.
 *
 * @since 1.0.0
 * @param \Elementor\Widgets_Manager $widget Elementor widgets manager.
 * @return void
 */
add_action('elementor/widgets/register', 'register_eea_widget');
if (!function_exists("register_eea_widget")) {
    function register_eea_widget($widget = null)
    {
        require_once(EEA_PLUGIN_PATH . 'includes/Elements/Widget.php');
        $widget?->register(new \AwesomeCoder\Elements\Elementor_Elements_Widget());
    }
}
