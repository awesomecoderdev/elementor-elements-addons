<?php

namespace AwesomeCoder\Support;

use AwesomeCoder\Core\Module;
use AwesomeCoder\Core\Platform;

abstract class Base {
	/**
	 * Holds the plugin instance.
	 *
	 * @since 2.0.0
	 * @access protected
	 * @static
	 *
	 * @var Base
	 */
	private static $instances = [];

	/**
	 * Disable class cloning and throw an error on object clone.
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object. Therefore, we don't want the object to be cloned.
	 *
	 * @access public
	 * @since 2.0.0
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Something went wrong.', EEA_PLUGIN_TEXTDOMAIN), '1.0.0' );
	}

	/**
	 * Disable unserializing of the class.
	 *
	 * @access public
	 * @since 2.0.0
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Something went wrong.', EEA_PLUGIN_TEXTDOMAIN), '1.0.0' );
	}

	/**
	 * Sets up a single instance of the plugin.
	 *
	 * @since 2.0.0
	 * @access public
	 * @static
	 *
	 * @return static An instance of the class.
	 */
	public static function get_instance( ...$args ) {
		$module = get_called_class();
		if ( ! isset( self::$instances[ $module ] ) ) {
			self::$instances[ $module ] = new $module( ...$args );
		}

		return self::$instances[ $module ];
	}

	/**
	 * Determine the plugin module to make platform based things happened.
	 *
	 * @param  string $id
	 * @return Platform|null
	 */
	public function platform( $id ){
		$platform = Module::get_instance()->active( $id );
		if( ! is_null( $platform ) ) {
			return $platform;
		}

		return null;
	}


	/**
	 * @param $type
	 * @param $parameters
	 *
	 * @return mixed|void
	 */
	public static function __callStatic( $type, $parameters = [] ){
		return ( new static )->call_user_func( $type, $parameters );
	}

	/**
	 * @param $type
	 * @param $parameters
	 *
	 * @return mixed|void
	 */
	public function __call( $type, $parameters = [] ){
		return $this->call_user_func( $type, $parameters );
	}

	/**
	 * @param $type
	 * @param $parameters
	 *
	 * @return mixed|void
	 */
	protected function call_user_func( $type, $parameters = [] ) {
		if( $type === 'http' || $type === 'options' ) {
			$parameters[0] = $type;
			$type = 'utils';
		}
		if( ! empty ( $parameters[0] ) && self::is_allowed( $type, $parameters[0] ) ) {
			return call_user_func( [ '\\Templately\\'. ucfirst( $type ) .'\\' . ucfirst( $parameters[0] ), 'get_instance' ] );
		}

		Helper::trigger_error( $this );
	}
}