<?php
namespace Spekkionu\ZendAcl;

use Illuminate\Support\ServiceProvider;
use Zend\Permissions\Acl\Acl;
use Illuminate\Contracts\Foundation\Application;

class ZendAclServiceProvider extends ServiceProvider
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
        $this->publishes([
            dirname(dirname(__DIR__)) . '/config/zendacl.php' => config_path('zendacl.php'),
            dirname(dirname(__DIR__)) . '/config/acl.php' => base_path('routes/acl.php'),
            dirname(dirname(__DIR__)) . '/views' => base_path('resources/views/vendor/zendacl'),
        ]);

        $this->loadViewsFrom(dirname(dirname(__DIR__)).'/views', 'zendacl');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            dirname(dirname(__DIR__)) . '/config/zendacl.php',
            'zendacl'
        );

        $this->app->singleton('acl', function (Application $app) {
            $acl = new Acl;
            if (file_exists(base_path('routes/acl.php'))) {
                include base_path('routes/acl.php');
            } elseif (file_exists(app_path('Http/acl.php'))) {
                include app_path('Http/acl.php');
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
