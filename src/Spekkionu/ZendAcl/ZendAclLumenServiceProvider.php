<?php
namespace Spekkionu\ZendAcl;

use Illuminate\Support\ServiceProvider;
use Zend\Permissions\Acl\Acl;
use Illuminate\Contracts\Foundation\Application;

class ZendAclLumenServiceProvider extends ServiceProvider
{

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
        $this->loadViewsFrom(dirname(dirname(__DIR__)).'/views', 'zendacl');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->configure('zendacl');

        $this->app->singleton(function (Application $app) {
            $acl = new Acl;
            if (file_exists(base_path('app/Http/acl.php'))) {
                include base_path('app/Http/acl.php');
            }
            return $acl;
        });

        $this->app->singleton('Zend\Permissions\Acl\Acl', function (Application $app) {
            return $app->make('acl');
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('acl', 'Zend\Permissions\Acl\Acl');
    }
}
