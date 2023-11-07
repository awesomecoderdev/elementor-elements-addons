<?php

namespace AwesomeCoder\Traits;

use Closure;
use ReflectionClass;
use ReflectionMethod;
use BadMethodCallException;

trait Widgetable
{
    /**
     * The registered string macros.
     *
     * @var array
     */
    protected $widgets = [];

    /**
     * Mix another object into the class.
     *
     * @return void
     *
     */
    protected function register_hooks()
    {
        // Core
        add_action('init', [$this, 'i18n']);

        add_filter('eea/active_plugins', [$this, 'is_plugin_active'], 10, 1);
        add_filter('eea/is_plugin_active', [$this, 'is_plugin_active'], 10, 1);

        // add_action('elementor/editor/after_save', array($this, 'save_global_values'), 10, 2);
        // add_action('trashed_post', array($this, 'save_global_values_trashed_post'), 10, 1);

        // Enqueue
        // add_action('elementor/editor/before_enqueue_scripts', [$this, 'editor_enqueue_scripts']);
        // add_action('elementor/frontend/before_register_scripts', [$this, 'frontend_enqueue_scripts']);

	    if ( defined( 'ELEMENTOR_VERSION' ) ) {
		    if ( version_compare( ELEMENTOR_VERSION, '3.5.0', '>=' ) ) {
                add_action('elementor/controls/register', fn($e) => call_user_func("do_action", 'eea/controls/register', $e));
                add_action('elementor/widgets/register', fn($e) => call_user_func("do_action", 'eea/widgets/register', $e));
		    } else {
                add_action('elementor/controls/controls_registered', fn($e) => call_user_func("do_action", 'eea/controls/register', $e));
                add_action('elementor/widgets/widgets_registered', fn($e) => call_user_func("do_action", 'eea/widgets/register', $e));
		    }
	    }

        // Elements
        // add_action('elementor/elements/categories_registered', array($this, 'register_widget_categories'));
        // add_filter('elementor/editor/localize_settings', [$this, 'promote_pro_elements']);


	    if ( is_admin() ) {
            // add_action('admin_menu', array($this, 'admin_menu'));
            // add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));

            // Core
            add_filter('plugin_action_links_' . EEA_PLUGIN_BASENAME, array($this, 'insert_plugin_links'));
            add_filter('plugin_row_meta', array($this, 'insert_plugin_row_meta'), 10, 2);

            // removed activation redirection temporarily
            // add_action('admin_init', array($this, 'redirect_on_activation'));

	        if ( ! did_action( 'elementor/loaded' ) ) {
		        add_action( 'admin_notices', array( $this, 'elementor_not_loaded' ) );
		        add_action( 'eea_admin_notices', array( $this, 'elementor_not_loaded' ) );
	        }

	        add_action( 'in_admin_header', [ $this, 'remove_admin_notice' ], 99 );

        }

    }

}
