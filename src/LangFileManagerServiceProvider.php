<?php namespace Backpack\LangFileManager;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Backpack\LangFileManager\Services\LangFiles;

class LangFileManagerServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // LOAD THE VIEWS
        // - first the published/overwritten views (in case they have any changes)
        $this->loadViewsFrom(resource_path('views/vendor/backpack/langfilemanager'), 'langfilemanager');
        // - then the stock views that come with the package, in case a published view might be missing
        $this->loadViewsFrom(realpath(__DIR__.'/resources/views'), 'langfilemanager');

        // publish config file
        $this->publishes([ __DIR__.'/config/langfilemanager.php' => config_path('backpack/langfilemanager.php') ], 'config');
        // publish views
        $this->publishes([ __DIR__.'/resources/views' => resource_path('views/vendor/backpack/langfilemanager'), ], 'views');

        // use the vendor configuration file as fallback
        $this->mergeConfigFrom( __DIR__.'/config/langfilemanager.php', 'langfilemanager' );
    }
    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function setupRoutes(Router $router)
    {
        $router->group(['namespace' => 'Backpack\LangFileManager\Http\Controllers'], function($router)
        {
            require __DIR__.'/Http/routes.php';
        });
    }
    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerLangFileManager();
        $this->setupRoutes($this->app->router);

        $this->app->singleton('langfile', function($app){ return new LangFiles($app['config']['app']['locale']); });

        // use this if your package has a config file
        // config([
        //         'config/langfilemanager.php',
        // ]);
    }
    private function registerLangFileManager()
    {
        $this->app->bind('langfilemanager',function($app){
            return new LangFileManager($app);
        });
    }
}