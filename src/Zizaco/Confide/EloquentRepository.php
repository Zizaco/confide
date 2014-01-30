<?php namespace Zizaco\Confide;

/**
 * A layer that abstracts all database interactions that happens
 * in Confide using Eloquent
 */
class EloquentRepository
{
    /**
     * Laravel application
     *
     * @var Illuminate\Foundation\Application
     */
    public $app;

    /**
     * Name of the model that should be used to retrieve your users.
     * You may specify an specific object. Then that object will be
     * returned when calling `model()` method.
     *
     * @var string
     */
    public $model;

    /**
     * Create a new ConfideRepository
     *
     * @param  \Illuminate\Foundation\Application $app Laravel application object
     * @return void
     */
    public function __construct($app = null)
    {
        $this->app = $app ?: app();
    }

    /**
     * Returns the model set in auth config
     *
     * @return mixed Instantiated object of the 'auth.model' class
     */
    public function model()
    {
        if (! $this->model)
        {
            $this->model = $this->app['config']->get('auth.model');
        }

        if ($this->model) {
            return $this->app[$this->model];
        }

        throw new \Exception("Wrong model specified in config/auth.php", 639);
    }
}
