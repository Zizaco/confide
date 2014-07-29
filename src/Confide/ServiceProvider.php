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
     */
    public function boot()
    {
        $this->package('zizaco/confide', 'confide', __DIR__.'/../');

        $this->commands(
            'command.confide.controller',
            'command.confide.routes',
            'command.confide.migration'
        );
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->registerRepository();

        $this->registerPasswordService();

        $this->registerLoginThrottleService();

        $this->registerUserValidator();

        $this->registerConfide();

        $this->registerCommands();
    }

    /**
     * Register the repository that will handle all the database
     * interaction.
     */
    protected function registerRepository()
    {
        $this->app->bind('confide.repository', function ($app) {
            return new EloquentRepository($app);
        });
    }

    /**
     * Register the service that abstracts all user password management
     * related methods
     */
    protected function registerPasswordService()
    {
        $this->app->bind('confide.password', function ($app) {
            return new EloquentPasswordService($app);
        });
    }

    /**
     * Register the service that Throttles login after
     * too many failed attempts. This is a secure measure in
     * order to avoid brute force attacks.
     */
    protected function registerLoginThrottleService()
    {
        $this->app->bind('confide.throttle', function ($app) {
            return new CacheLoginThrottleService($app);
        });
    }

    /**
     * Register the UserValidator class. The default class that
     * used for user validation
     */
    protected function registerUserValidator()
    {
        $this->app->bind('confide.user_validator', function ($app) {
            return new UserValidator();
        });
    }

    /**
     * Register the application bindings.
     */
    protected function registerConfide()
    {
        $this->app->bind('confide', function ($app) {
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
     */
    protected function registerCommands()
    {
        $this->app->bind('command.confide.controller', function ($app) {
            return new ControllerCommand($app);
        });

        $this->app->bind('command.confide.routes', function ($app) {
            return new RoutesCommand($app);
        });

        $this->app->bind('command.confide.migration', function ($app) {
            return new MigrationCommand($app);
        });
    }

    /**
     * Get the services provided.
     *
     * @return array
     */
    public function provides()
    {
        return array(
            'confide',
            'confide.repository',
            'confide.password',
            'confide.throttle',
            'confide.user_validator',
            'command.confide.controller',
            'command.confide.routes',
            'command.confide.migration'
        );
    }
}
