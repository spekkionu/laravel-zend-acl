<?php namespace Spekkionu\ZendAcl;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Routing\Registrar as Router;
use Zend\Permissions\Acl\Acl;

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
    public function boot(Router $router)
    {
        $this->publishes([
            dirname(dirname(__DIR__)) . '/config/zendacl.php' => config_path('zendacl.php'),
            dirname(dirname(__DIR__)) . '/config/acl.php' => app_path('Http/acl.php'),
            dirname(dirname(__DIR__)) . '/views' => base_path('resources/views/vendor/zendacl'),
        ]);

        $this->loadViewsFrom(dirname(dirname(__DIR__)).'/views', 'zendacl');

        // Register the route filter
        $router->filter('acl', 'Spekkionu\ZendAcl\AclRouteFilter');

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            dirname(dirname(__DIR__)) . '/config/zendacl.php', 'zendacl'
        );

        $this->app['acl'] = $this->app->share(function ($app) {
            $acl = new Acl;
            if (file_exists(app_path('Http/acl.php'))) {
                include app_path('Http/acl.php');
            }
            return $acl;
        });

        $this->app->singleton('Zend\Permissions\Acl\Acl', function ($app) {
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
