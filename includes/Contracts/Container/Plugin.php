<?php

namespace AwesomeCoder\Contracts\Container;

use AwesomeCoder\Contracts\Container\Container;

interface Plugin extends Container
{
    /**
     * Get the version number of the application.
     *
     * @return string
     */
    public function version();

    /**
     * Get the base path of the Laravel installation.
     *
     * @param  string  $path
     * @return string
     */
    public function basePath($path = '');

    /**
     * Get the path to the public directory.
     *
     * @param  string  $path
     * @return string
     */
    public function publicPath($path = '');

    /**
     * Get the path to the storage directory.
     *
     * @param  string  $path
     * @return string
     */
    public function storagePath($path = '');

    /**
     * Register a service provider with the application.
     *
     * @param  \AwesomeCoder\Support\ServiceProvider|string  $provider
     * @param  bool  $force
     * @return \AwesomeCoder\Support\ServiceProvider
     */
    public function register($provider, $force = false);

    /**
     * Register a deferred provider and service.
     *
     * @param  string  $provider
     * @param  string|null  $service
     * @return void
     */
    public function registerDeferredProvider($provider, $service = null);

    /**
     * Resolve a service provider instance from the class name.
     *
     * @param  string  $provider
     * @return \AwesomeCoder\Support\ServiceProvider
     */
    public function resolveProvider($provider);

    /**
     * Get the application namespace.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    public function getNamespace();

    /**
     * Terminate the application.
     *
     * @return void
     */
    public function terminate();
}
