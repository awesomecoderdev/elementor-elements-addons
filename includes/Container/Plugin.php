<?php

namespace AwesomeCoder\Container;

use Closure;
use RuntimeException;
use AwesomeCoder\Support\Arr;
use AwesomeCoder\Support\Str;
use AwesomeCoder\Traits\WordPress;
use AwesomeCoder\Traits\Macroable;
use AwesomeCoder\Support\Collection;
use AwesomeCoder\Container\Container;
use AwesomeCoder\Contracts\Container\Plugin as PluginContract;
use AwesomeCoder\Traits\Widgetable;

class Plugin extends Container implements PluginContract
{
    use Macroable, WordPress, Widgetable;

    /**
     * The plugin namespace.
     *
     * @var string
     */
    protected $namespace;

    /**
     * Create a new AwesomeCoder plugin instance.
     *
     * @param  string|null  $basePath
     * @return void
     */
    public function __construct()
    {
        $this->registerBaseBindings();
        $this->registerConstantsBindings();
        $this->resolveInitializationBindings();
    }

    /**
     * Set the initial var of the plugin.
     *
     * @return string
     */
    public function resolveInitializationBindings()
    {
        $this->version = $this->constant("EEA_PLUGIN_VERSION",'1.0.0');
        $this->basePath = $this->constant("EEA_PLUGIN_FILE");
        $this->publicPath = $this->constant("EEA_PLUGIN_PATH");
        $this->register_hooks();

		/**
		 * EEA init.
		 *
		 * Fires when EEA components are initialized.
		 *
		 * After EEA finished loading but before any headers are sent.
		 *
		 * @since 1.0.0
		 */
		do_action( 'eea/init' );
    }

	/**
	 * Define CONSTANTS
	 *
	 * @since 2.0.0
	 * @return void
	 */
    protected function registerConstantsBindings(){
        $this->define( 'EEA_DEBUG_LOG', apply_filters("eee/debug", false));
        $this->define( 'EEA_ASSETS_PATH', EEA_PLUGIN_PATH."assets");
        $this->define( 'EEA_PLUGIN_URL', EEA_PLUGIN_PATH."assets");
    }

    /**
     * Get the version number of the plugin.
     *
     * @return string
     */
    public function version()
    {
        return static::$version;
    }


	/**
	 * Define constant if not already set.
	 *
	 * @param string      $name  Constant name.
	 * @param mixed $default Constant value.
	 *
	 * @return void
	 */
	private function constant( $name, $default = null) {
		if ( ! defined( $name ) ) {
			define( $name, $default );
		}
	}

	/**
	 * Define constant if not already set.
	 *
	 * @param string      $name  Constant name.
	 * @param mixed $value Constant value.
	 *
	 * @return void
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

    /**
     * Register the basic bindings into the container.
     *
     * @return void
     */
    protected function registerBaseBindings()
    {
        static::setInstance($this);

        $this->instance('app', $this);

        $this->instance(Container::class, $this);

        // $this->properties("pro_enabled", fn()=> apply_filters("eea/pro_enabled", false));
    }

    /**
     * Get the base path of the Laravel installation.
     *
     * @param  string  $path
     * @return string
     */
    public function basePath($path = '')
    {
        return $this->joinPaths($this->basePath, $path);
    }

    /**
     * Get the path to the public / web directory.
     *
     * @param  string  $path
     * @return string
     */
    public function publicPath($path = '')
    {
        return $this->joinPaths($this->publicPath ?: $this->basePath('public'), $path);
    }

    /**
     * Join the given paths together.
     *
     * @param  string  $basePath
     * @param  string  $path
     * @return string
     */
    public function joinPaths($basePath, $path = '')
    {
        return $basePath . ($path != '' ? DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : '');
    }

    /**
     * Register a service provider with the plugin.
     *
     * @param  \AwesomeCoder\Support\ServiceProvider|string  $provider
     * @param  bool  $force
     * @return \AwesomeCoder\Support\ServiceProvider
     */
    public function register($provider, $force = false)
    {
        if (($registered = $this->getProvider($provider)) && !$force) {
            return $registered;
        }

        // If the given "provider" is a string, we will resolve it, passing in the
        // plugin instance automatically for the developer. This is simply
        // a more convenient way of specifying your service provider classes.
        if (is_string($provider)) {
            $provider = $this->resolveProvider($provider);
        }

        $provider->register();

        // If there are bindings / singletons set as properties on the provider we
        // will spin through them and register them with the plugin, which
        // serves as a convenience layer while registering a lot of bindings.
        if (property_exists($provider, 'bindings')) {
            foreach ($provider->bindings as $key => $value) {
                $this->bind($key, $value);
            }
        }

        if (property_exists($provider, 'singletons')) {
            foreach ($provider->singletons as $key => $value) {
                $key = is_int($key) ? $value : $key;

                $this->singleton($key, $value);
            }
        }

        $this->markAsRegistered($provider);

        return $provider;
    }

    /**
     * Get the registered service provider instance if it exists.
     *
     * @param  \AwesomeCoder\Support\ServiceProvider|string  $provider
     * @return \AwesomeCoder\Support\ServiceProvider|null
     */
    public function getProvider($provider)
    {
        return array_values($this->getProviders($provider))[0] ?? null;
    }

    /**
     * Get the registered service provider instances if any exist.
     *
     * @param  \AwesomeCoder\Support\ServiceProvider|string  $provider
     * @return array
     */
    public function getProviders($provider)
    {
        $name = is_string($provider) ? $provider : get_class($provider);

        return Arr::where($this->serviceProviders, fn ($value) => $value instanceof $name);
    }

    /**
     * Resolve a service provider instance from the class name.
     *
     * @param  string  $provider
     * @return \AwesomeCoder\Support\ServiceProvider
     */
    public function resolveProvider($provider)
    {
        return new $provider($this);
    }

    /**
     * Mark the given provider as registered.
     *
     * @param  \AwesomeCoder\Support\ServiceProvider  $provider
     * @return void
     */
    protected function markAsRegistered($provider)
    {
        $this->serviceProviders[] = $provider;

        $this->loadedProviders[get_class($provider)] = true;
    }

    /**
     * Load and boot all of the remaining deferred providers.
     *
     * @return void
     */
    public function loadDeferredProviders()
    {
        // We will simply spin through each of the deferred providers and register each
        // one and boot them if the plugin has booted. This should make each of
        // the remaining services available to this plugin for immediate use.
        foreach ($this->deferredServices as $service => $provider) {
            $this->loadDeferredProvider($service);
        }

        $this->deferredServices = [];
    }

    /**
     * Load the provider for a deferred service.
     *
     * @param  string  $service
     * @return void
     */
    public function loadDeferredProvider($service)
    {

        $provider = $this->deferredServices[$service];

        // If the service provider has not already been loaded and registered we can
        // register it with the plugin and remove the service from this list
        // of deferred services, since it will already be loaded on subsequent.
        if (!isset($this->loadedProviders[$provider])) {
            $this->registerDeferredProvider($provider, $service);
        }
    }

    /**
     * Register a deferred provider and service.
     *
     * @param  string  $provider
     * @param  string|null  $service
     * @return void
     */
    public function registerDeferredProvider($provider, $service = null)
    {
        // Once the provider that provides the deferred service has been registered we
        // will remove it from our local list of the deferred services with related
        // providers so that this container does not try to resolve it out again.
        if ($service) {
            unset($this->deferredServices[$service]);
        }

        $this->register($instance = new $provider($this));

        if (!$this->isBooted()) {
            $this->booting(function () use ($instance) {
                $this->bootProvider($instance);
            });
        }
    }

    /**
     * Resolve the given type from the container.
     *
     * @param  string  $abstract
     * @param  array  $parameters
     * @return mixed
     */
    public function make($abstract, array $parameters = [])
    {
        $this->loadDeferredProviderIfNeeded($abstract = $this->getAlias($abstract));

        return parent::make($abstract, $parameters);
    }

    /**
     * Resolve the given type from the container.
     *
     * @param  string  $abstract
     * @param  array  $parameters
     * @param  bool  $raiseEvents
     * @return mixed
     */
    protected function resolve($abstract, $parameters = [], $raiseEvents = true)
    {
        $this->loadDeferredProviderIfNeeded($abstract = $this->getAlias($abstract));

        return parent::resolve($abstract, $parameters, $raiseEvents);
    }

    /**
     * Load the deferred provider if the given type is a deferred service and the instance has not been loaded.
     *
     * @param  string  $abstract
     * @return void
     */
    protected function loadDeferredProviderIfNeeded($abstract)
    {
        if (!isset($this->instances[$abstract])) {
            $this->loadDeferredProvider($abstract);
        }
    }


    /**
     * Register a binding if it hasn't already been registered.
     *
     * @param  string  $abstract
     * @param  \Closure|string|null  $concrete
     * @param  bool  $shared
     * @return void
     */
    public function bindIf($abstract, $concrete = null, $shared = false)
    {
        if (! $this->bound($abstract)) {
            $this->bind($abstract, $concrete, $shared);
        }
    }

    /**
     * Register a shared binding in the container.
     *
     * @param  string  $abstract
     * @param  \Closure|string|null  $concrete
     * @return void
     */
    public function singleton($abstract, $concrete = null)
    {
        $this->bind($abstract, $concrete, true);
    }

    /**
     * Register a shared binding if it hasn't already been registered.
     *
     * @param  string  $abstract
     * @param  \Closure|string|null  $concrete
     * @return void
     */
    public function singletonIf($abstract, $concrete = null)
    {
        if (! $this->bound($abstract)) {
            $this->singleton($abstract, $concrete);
        }
    }

    /**
     * Determine if the given abstract type has been bound.
     *
     * @param  string  $abstract
     * @return bool
     */
    public function bound($abstract)
    {
        return parent::bound($abstract);
    }

    /**
     * Get the namespace.
     *
     * @return string
     *
     */
    public function getNamespace()
    {
        return __NAMESPACE__;
    }


    /**
     * Terminate the plugin.
     *
     * @return void
     */
    public function terminate(){
        die(__("Unauthorized Access.", EEA_PLUGIN_TEXTDOMAIN));
    }
}
