<?php namespace Zizaco\Confide;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

/**
 * This class is used by Laravel in order to register confide
 * services into the IoC container.
 */
class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('zizaco/confide');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerRepository();

        $this->registerPasswordService();

        $this->registerConfide();

        $this->registerCommands();
    }

    /**
     * Register the repository that will handle all the database interaction.
     *
     * @return void
     */
    protected function registerRepository()
    {
        $this->app->bind('confide.repository', function($app)
        {
            return new EloquentRepository($app);
        });
    }

    /**
     * Register the repository that will handle all the database interaction.
     *
     * @return void
     */
    protected function registerPasswordService()
    {
        $this->app->bind('confide.password', function($app)
        {
            return new EloquentPasswordService($app);
        });
    }

    /**
     * Register the application bindings.
     *
     * @return void
     */
    protected function registerConfide()
    {
        $this->app->bind('confide', function($app)
        {
            return new Confide(
                $app->make('confide.repository'),
                $app->make('confide.password'),
                $app
            );
        });
    }

    /**
     * Register the artisan commands.
     *
     * @return void
     */
    protected function registerCommands()
    {

    }
}
