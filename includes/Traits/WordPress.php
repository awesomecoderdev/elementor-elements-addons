<?php

namespace AwesomeCoder\Traits;

use Closure;
use Exception;
use ReflectionClass;
use ReflectionMethod;
use BadMethodCallException;

trait WordPress{

    /**
     * The plugin version.
     *
     * @var string
     */
    public $version;

    /**
     * The base path for the Laravel installation.
     *
     * @var string
     */
    protected $basePath;

    /**
     * The names of the loaded service providers.
     *
     * @var array
     */
    protected $loadedProviders = [];

    /**
     * The deferred services and their providers.
     *
     * @var array
     */
    protected $deferredServices = [];

    /**
     * The custom public / web path defined by the developer.
     *
     * @var string
     */
    protected $publicPath;

    /**
     * The deferred services and their providers.
     *
     * @var array
     */
    private $properties = [];

    /**
     * Bind the given properties together.
     *
     * @param  string  $key
     * @param  string  $concrete
     * @return string
     */
    public function properties($key, $concrete) {
        if(property_exists($this,$key)){
            $this->{$key} = $concrete;
        }
        $this->properties[$key] = $concrete;
    }

    /**
     * Bind the given properties together.
     *
     * @param  string  $key
     * @param  string  $concrete
     * @return string
     */
    public function property($key) {
        if(property_exists($this,$key)){
            return $this->{$key};
        }

        if (isset($this->properties[$key])) {
            $concrete = $this->properties[$key];

            if (is_callable($concrete)) {
                return $concrete();
            }

            return $concrete;
        }

        throw new Exception("Binding not found for: $key");
    }

    /**
     * The custom property.
     *
     * @var string
     */
    public function __get($property) {
        return $this->get($property);
    }

     /**
     * Extending plugin links
     *
     * @since 3.0.0
     */
    public function i18n()
    {
        load_plugin_textdomain('elements');
    }

    /**
     * Check if a plugin is active
     *
     * @since 3.0.0
     */
    public function is_plugin_active($plugin)
    {
        include_once ABSPATH . 'wp-admin/includes/plugin.php';

        return is_plugin_active($plugin);
    }


	/**
	 * remove_admin_notice
	 *
	 *
	 * @return void
	 */
	public function remove_admin_notice() {
		$current_screen = get_current_screen();
		if ( $current_screen->id == 'toplevel_page_eea-settings' ) {

			remove_all_actions( 'user_admin_notices' );
			remove_all_actions( 'admin_notices' );

            // To showing notice in EA settings page we have to use 'eea_admin_notices' action hook
			add_action( 'admin_notices', function () {
				do_action( 'eea_admin_notices' );
			} );
		}
	}


    /**
     * @param $id
     * @param $global_data
     * @return string
     */
    public function get_typography_data($id, $global_data)
    {
        $typo_data = '';
        $fields_keys = [
            'font_family',
            'font_weight',
            'text_transform',
            'font_style',
            'text_decoration',
            'font_size',
            'letter_spacing',
            'line_height',
        ];

        foreach ($fields_keys as $key => $field) {
            $typo_attr = $global_data[$id . '_' . $field];
            $attr = str_replace('_', '-', $field);

            if (in_array($field, ['font_size', 'letter_spacing', 'line_height'])) {
                if (!empty($typo_attr['size'])) {
                    $typo_data .= "{$attr}:{$typo_attr['size']}{$typo_attr['unit']} !important;";
                }
            } elseif (!empty($typo_attr)) {
                $typo_data .= ($attr == 'font-family') ? "{$attr}:{$typo_attr}, sans-serif;" : "{$attr}:{$typo_attr};";
            }
        }

        return $typo_data;
    }

    /**
     * Check if assets files exists
     *
     * @since 3.0.0
     */
    public function has_assets_files($uid = null, $ext = ['css', 'js'])
    {
        if (!is_array($ext)) {
            $ext = (array) $ext;
        }

        foreach ($ext as $e) {
            $path = EEA_ASSET_PATH . DIRECTORY_SEPARATOR . ($uid ? $uid : 'eea') . '.min.' . $e;

            if (!is_readable($this->safe_path($path))) {
                return false;
            }
        }

        return true;
    }

	/**
	 * Remove files
	 *
	 * @since 3.0.0
	 */
	public function remove_files( $post_id = null, $ext = [ 'css', 'js' ] ) {
		foreach ( $ext as $e ) {
			$path = EEA_ASSET_PATH . DIRECTORY_SEPARATOR . 'eea' . ( $post_id ? '-' . $post_id : '' ) . '.' . $e;
			if ( file_exists( $path ) ) {
				unlink( $path );
			}
		}
		do_action( 'eea_remove_assets', $post_id, $ext );
	}

    /**
     * Remove files in dir
     *
     * @since 3.0.0
     */
    public function empty_dir($path)
    {
        if (!is_dir($path) || !file_exists($path)) {
            return;
        }

        foreach (scandir($path) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            unlink($this->safe_path($path . DIRECTORY_SEPARATOR . $item));
        }
    }

    /**
     * Clear cache files
     *
     * @since 3.0.0
     */


    /**
     * Check if wp running in background
     *
     * @since 3.0.0
     */
    public function is_running_background()
    {
        if (wp_doing_cron()) {
            return true;
        }

        if (wp_doing_ajax()) {
            return true;
        }

        if (!empty($_REQUEST['action']) && !$this->check_background_action( sanitize_text_field( $_REQUEST['action'] ) )) {
            return true;
        }

        return false;
    }

    /**
     * Check if elementor edit mode or not
     *
     * @since 3.0.0
     */
    public function is_edit_mode()
    {
        if (isset($_REQUEST['elementor-preview'])) {
            return true;
        }

        return false;
    }

    /**
     * Check if elementor edit mode or not
     *
     * @since 3.0.0
     */
    public function is_preview_mode()
    {
        if (isset($_REQUEST['elementor-preview'])) {
            return false;
        }

        if (!empty($_REQUEST['action']) && !$this->check_background_action( sanitize_text_field( $_REQUEST['action'] ) )) {
            return false;
        }


        return true;
    }

    /**
     * Check if a plugin is installed
     *
     * @since v3.0.0
     */
    public function is_plugin_installed($basename)
    {
        if (!function_exists('get_plugins')) {
            include_once ABSPATH . '/wp-admin/includes/plugin.php';
        }

        $installed_plugins = get_plugins();

        return isset($installed_plugins[$basename]);
    }

    /**
     * Generate safe path
     *
     * @since v3.0.0
     */
    public function safe_path($path)
    {
        $path = str_replace(['//', '\\\\'], ['/', '\\'], $path);

        return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    }

    /**
     * Generate safe url
     *
     * @since v3.0.0
     */
    public function safe_url($url)
    {
        if (is_ssl()) {
            $url = wp_parse_url($url);

            if (!empty($url['host'])) {
                $url['scheme'] = 'https';
            }

            return $this->unparse_url($url);
        }

        return $url;
    }

    public function unparse_url($parsed_url)
    {
        $scheme = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
        $host = isset($parsed_url['host']) ? $parsed_url['host'] : '';
        $port = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
        $user = isset($parsed_url['user']) ? $parsed_url['user'] : '';
        $pass = isset($parsed_url['pass']) ? ':' . $parsed_url['pass'] : '';
        $pass = ($user || $pass) ? "$pass@" : '';
        $path = isset($parsed_url['path']) ? $parsed_url['path'] : '';
        $query = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
        $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';

        return "$scheme$user$pass$host$port$path$query$fragment";
    }

    /**
     * Allow to load asset for some pre defined action query param in elementor preview
     * @return bool
     */
    public function check_background_action($action_name){
        $allow_action = [
        	'subscriptions',
	        'mepr_unauthorized',
	        'home',
	        'subscriptions',
	        'payments',
	        'newpassword',
	        'manage_sub_accounts',
	        'ppw_postpass',
        ];
        if (in_array($action_name, $allow_action)){
            return true;
        }
        return false;
    }

	/*
	 * Check some other cookie for solve asset loading issue
	 */
	public function check_third_party_cookie_status($id='') {
		global $Password_Protected;
		if ( is_object( $Password_Protected ) && method_exists( $Password_Protected, 'cookie_name' ) && isset( $_COOKIE[ $Password_Protected->cookie_name() ] ) ) {
			return true;
		}
		return false;
	}

    /**
     * Extending plugin links
     *
     * @since 3.0.0
     */
    public function insert_plugin_links($links)
    {
        // settings
        $links[] = sprintf('<a href="admin.php?page=eea-settings">' . __('Settings', 'essential-addons-for-elementor-lite') . '</a>');

        // go pro
        // if (!$this->pro_enabled) {
            $links[] = sprintf('<a href="https://wpdeveloper.com/in/upgrade-essential-addons-elementor" target="_blank" style="color: #524cff; font-weight: bold;">' . __('Go Pro', 'essential-addons-for-elementor-lite') . '</a>');
        // }

        return $links;
    }

    /**
     * Extending plugin row meta
     *
     * @since 3.0.0
     */
    public function insert_plugin_row_meta($links, $file)
    {
        if (EEA_PLUGIN_BASENAME == $file) {
            // docs & faq
            $links[] = sprintf('<a href="https://essential-addons.com/elementor/docs/?utm_medium=admin&utm_source=wp.org&utm_term=ea" target="_blank">' . __('Docs & FAQs', 'essential-addons-for-elementor-lite') . '</a>');

            // video tutorials
            $links[] = sprintf('<a href="https://www.youtube.com/channel/UCOjzLEdsnpnFVkm1JKFurPA?utm_medium=admin&utm_source=wp.org&utm_term=ea" target="_blank">' . __('Video Tutorials', 'essential-addons-for-elementor-lite') . '</a>');
        }

        return $links;
    }

    /**
     * Redirect to options page
     *
     * @since v1.0.0
     */
    public function redirect_on_activation()
    {
        if (get_transient('eea_do_activation_redirect')) {
            delete_transient('eea_do_activation_redirect');

            if (!isset($_GET['activate-multi'])) {
                wp_redirect("admin.php?page=eea-settings");
            }
        }
    }

    /**
     * Check if elementor plugin is activated
     *
     * @since v1.0.0
     */
    public function elementor_not_loaded()
    {
        if (!current_user_can('activate_plugins')) {
            return;
        }

        $elementor = 'elementor/elementor.php';

        if ($this->is_plugin_installed($elementor)) {
            $activation_url = wp_nonce_url('plugins.php?action=activate&amp;plugin=' . $elementor . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $elementor);

            $message = sprintf(__('%1$sEssential Addons for Elementor%2$s requires %1$sElementor%2$s plugin to be active. Please activate Elementor to continue.', 'essential-addons-for-elementor-lite'), "<strong>", "</strong>");

            $button_text = __('Activate Elementor', 'essential-addons-for-elementor-lite');
        } else {
            $activation_url = wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=elementor'), 'install-plugin_elementor');

            $message = sprintf(__('%1$sEssential Addons for Elementor%2$s requires %1$sElementor%2$s plugin to be installed and activated. Please install Elementor to continue.', 'essential-addons-for-elementor-lite'), '<strong>', '</strong>');
            $button_text = __('Install Elementor', 'essential-addons-for-elementor-lite');
        }

        $button = '<p><a href="' . esc_url( $activation_url ) . '" class="button-primary">' . esc_html( $button_text ) . '</a></p>';

        printf('<div class="error"><p>%1$s</p>%2$s</div>', __($message), $button);
    }

    /**
     * Optional usage tracker
     *
     * @since v1.0.0
     */
    public function start_plugin_tracking()
    {
        $tracker = Plugin_Usage_Tracker::get_instance( eea_PLUGIN_FILE, [
            'opt_in'       => true,
            'goodbye_form' => true,
            'item_id'      => '760e8569757fa16992d8'
        ] );
        $tracker->set_notice_options(array(
            'notice' => __( 'Want to help make <strong>Essential Addons for Elementor</strong> even more awesome? You can get a <strong>10% discount coupon</strong> for Pro upgrade if you allow.', 'essential-addons-for-elementor-lite' ),
            'extra_notice' => __( 'We collect non-sensitive diagnostic data and plugin usage information.
            Your site URL, WordPress & PHP version, plugins & themes and email address to send you the
            discount coupon. This data lets us make sure this plugin always stays compatible with the most
            popular plugins and themes. No spam, I promise.', 'essential-addons-for-elementor-lite' ),
        ));
        $tracker->init();
    }

    /**
     * Save default values to db
     *
     * @since v3.0.0
     */
    public function set_default_values()
    {
        $defaults = array_fill_keys(array_keys(array_merge($GLOBALS['eea_config']['elements'], $GLOBALS['eea_config']['extensions'])), 1);
        $values = get_option('eea_save_settings');

        return update_option('eea_save_settings', wp_parse_args($values, $defaults));
    }

    /**
     * Save setup wizard data
     *
     * @since v4.0.0
     */
    public function enable_setup_wizard()
    {
        if ( !get_option( 'eea_version' ) && !get_option( 'eea_setup_wizard' ) ) {
            update_option( 'eea_setup_wizard', 'redirect' );
        }
    }

}
