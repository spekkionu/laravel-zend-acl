<?php namespace Spekkionu\ZendAcl;

use Illuminate\Support\ServiceProvider;
use Zend\Permissions\Acl\Acl;

class ZendAclServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('spekkionu/laravel-zend-acl');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['acl'] = $this->app->share(function($app) {
    	            return new Acl;
                });
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('acl');
	}

}
