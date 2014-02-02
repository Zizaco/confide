<?php namespace Zizaco\Confide;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ControllerCommand extends Command
{
    /**
     * Laravel application
     *
     * @var \Illuminate\Foundation\Application
     */
    public $app;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'confide:controller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a controller template that uses Confide.';

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

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array('name', null, InputOption::VALUE_OPTIONAL, 'Name of the controller.', $this->app['config']->get('auth.model')),
            array('--restful', '-r', InputOption::VALUE_NONE, 'Generate RESTful controller.'),
            array('--repository', '-R', InputOption::VALUE_NONE, 'Generate a User Repository class.'),
        );
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {

    }
}
