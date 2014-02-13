<?php namespace Zizaco\Confide;

use Zizaco\Confide\Support\GenerateCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * This command dumps some routes at the end of the routes.php
 * file in order to make the actions of the controller generated
 * by the ControllerCommand reachable.
 *
 * @license MIT
 * @package  Zizaco\Confide
 */
class RoutesCommand extends GenerateCommand
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
    protected $name = 'confide:routes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Append the default Confide controller routes to the routes.php';

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
            array('controller', null, InputOption::VALUE_OPTIONAL, 'Name of the controller.', 'UsersController'),
            array('--restful', '-r', InputOption::VALUE_NONE, 'Generate RESTful controller.'),
        );
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        // Prepare variables
        $controllerName = $this->option('controller');
        $restful = $this->option('restful');

        $viewVars = compact(
            'controllerName',
            'restful'
        );

        // Prompt
        $this->line('');
        $this->info( "Routes file: app/routes.php" );

        $message = $this->getFireMessage($restful);

        $this->comment($message);
        $this->line('');

        if ( $this->confirm("Proceed with the append? [Yes|no]") )
        {
            $this->info( "Appending routes..." );
            // Generate
            $filename = 'routes.php';
            $this->appendInFile($filename, 'generators.routes', $viewVars);

            $this->info("app/routes.php Patched successfully!");
        }
    }

    protected function getFireMessage($restful = false)
    {
        if(! $restful) {
            return "The default Confide routes (to use with the Controller template)".
            " will be appended to your routes.php file.";
        } else {
            return "A single route to handle every action in a RESTful controller".
            " will be appended to your routes.php file. This may be used with a confide".
            " controller generated using [-r|--restful] option.";
        }
    }
}
