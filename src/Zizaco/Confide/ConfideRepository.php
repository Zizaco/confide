<?php namespace Zizaco\Confide;

class ConfideRepository
{
    /**
     * Returns the model set in auth config
     *
     * @return mixed Instantiated object of the 'auth.model' class
     */
    public function model()
    {
        $model = $this->app['config']->get('auth.model');

        return new $model;
    }    
}
