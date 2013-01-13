<?php namespace Zizaco\Confide;

use Illuminate\View\Environment;
use Illuminate\Config\Repository;

class Confide
{
    /**
     * Confide Vesion
     */
    const VERSION = '0.2';

    /**
     * Illuminate view environment.
     * 
     * @var Illuminate\View\Environment
     */
    protected $view;

    /**
     * Illuminate config repository.
     * 
     * @var Illuminate\Config\Repository
     */
    protected $config;

    /**
     * Create a new confide instance.
     * 
     * @param  Illuminate\View\Environment  $view
     * @param  Illuminate\Config\Repository  $config
     * @return void
     */
    public function __construct(Environment $view, Repository $config)
    {
        $this->view = $view;
        $this->config = $config;
    }

    /**
     * Returns the current version
     *
     * @return string
     */
    public function Version()
    {
        return 'Confide v'.Confide::VERSION;
    }
}
