<?php namespace Zizaco\Confide;

class ConfideRepository
{

    /**
     * Name of the model that should be used to retrieve your users.
     * You may specify an specific object. Then that object will be
     * returned when calling `model()` method.
     * 
     * @var string
     */
    public $model;

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

        if(is_object($this->model))
        {
            return $this->model;
        }
        elseif(is_string($this->model))
        {
            return new $this->model;
        }

        throw new \Exception("Model not specified in config/auth.php", 639);
    }

    /**
     * Get the currently authenticated user or null.
     *
     * @return Zizaco\Confide\ConfideUser|null
     */
    public function user()
    {
        return $this->app['auth']->user();
    }

    /**
     * Set the user confirmation to true.
     *
     * @param string $code
     * @return bool
     */
    public function confirm( $code )
    {
        $user = $this->model()->where('confirmation_code', '=', $code)->get()->first();
        if( $user )
        {
            return $user->confirm();
        }
        else
        {
            return false;
        }
    }
}
