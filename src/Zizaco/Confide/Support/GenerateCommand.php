<?php namespace Zizaco\Confide\Support;

use Illuminate\Console\Command;

/**
 * This is a support class that abstracts some of the file generation
 * behaviors for the confide commands that generates files.
 *
 * @license MIT
 * @package  Zizaco\Confide
 */
abstract class GenerateCommand extends Command
{
    /**
     * Laravel application
     *
     * @var \Illuminate\Foundation\Application
     */
    public $app;

    /**
     * Create a new command instance.
     *
     * @param  \Illuminate\Foundation\Application $app Laravel application object
     * @return void
     */
    public function __construct($app = null)
    {
        if (! is_array($app))
            parent::__construct();

        $this->app = $app ?: app();
    }

    protected function generateFile($filename, $view, $viewVars)
    {

    }
}
