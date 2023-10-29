<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 *
 * @link              https://awesomecoder.dev
 * @since             1.0.0
 * @package           Elements
 *
 *																__
 *	                                                           |  |
 *	  __ ___      _____  ___  ___  _ __ ___   ___  ___ ___   __|  | ___ _ ____
 *	 / _` \ \ /\ / / _ \/ __|/ _ \| '_ ` _ \ / _ \/ __/ _ \ / _`  |/ _ \ ' __|
 *	| (_| |\ V  V /  __/\__ \ (_) | | | | | |  __/ (_| (_) | (_|  |  __/  |
 *	\__,_| \_/\_/ \___||___/\___/|_| |_| |_|\___|\___\___/ \__,___|\___|__|
 *
 */


// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}
