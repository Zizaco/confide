<?php namespace Zizaco\Confide;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

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
            return new EloquentRepository;
        });
    }

    /**
     * Register the application bindings.
     *
     * @return void
     */
    protected function registerConfide()
    {

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
