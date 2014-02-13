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

        $this->registerLoginThrottleService();

        $this->registerConfide();

        $this->registerCommands();
    }

    /**
     * Register the repository that will handle all the database
     * interaction.
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
     * Register the service that abstracts all user password management
     * related methods
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
     * Register the service that Throttles login after
     * too many failed attempts. This is a secure measure in
     * order to avoid brute force attacks.
     *
     * @return void
     */
    protected function registerLoginThrottleService()
    {
        $this->app->bind('confide.throttle', function($app)
        {
            return new CacheLoginThrottleService($app);
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
                $app->make('confide.throttle'),
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
        $this->app->bind('command.confide.controller', function($app)
        {
            return new ControllerCommand($app);
        });

        $this->app->bind('command.confide.routes', function($app)
        {
            return new RoutesCommand($app);
        });

        $this->app->bind('command.confide.migration', function($app)
        {
            return new MigrationCommand($app);
        });

        $this->commands(
            'command.confide.controller',
            'command.confide.routes',
            'command.confide.migration'
        );
    }
}
