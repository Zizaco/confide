<?php namespace Zizaco\Confide;

use Illuminate\Support\Facades\Facade as IlluminateFacade;

/**
 * @see \Zizaco\Confide\Facade
 * @package Zizaco\Confide
 */
class Facade extends IlluminateFacade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'confide';
    }
}
