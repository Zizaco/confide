<?php namespace Zizaco\Confide;

use Zizaco\Confide\Support\GenerateCommand;
use Symfony\Component\Console\Input\InputOption;

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
     */
    public function fire()
    {
        // Prepare variables
        $controllerName = $this->option('controller');
        $restful = $this->option('restful');

        $url = 'users';

        $viewVars = compact(
            'controllerName',
            'url',
            'restful'
        );

        // Prompt
        $this->line('');
        $this->info("Routes file: app/routes.php");

        $message = $this->getFireMessage($restful);

        $this->comment($message);
        $this->line('');

        if ($this->confirm("Proceed with the append? [Yes|no]")) {
            $this->info("Appending routes...");
            // Generate
            $filename = 'routes.php';
            $this->appendInFile($filename, 'generators.routes', $viewVars);

            $this->info("app/routes.php Patched successfully!");
        }
    }

    /**
     * Returns a message that should explain what is about to be done.
     *
     * @param boolean $restful If the restful option is being used.
     *
     * @return string The message.
     */
    protected function getFireMessage($restful = false)
    {
        if (! $restful) {
            return "The default Confide routes (to use with the Controller template)".
            " will be appended to your routes.php file.";
        } else {
            return "A single route to handle every action in a RESTful controller".
            " will be appended to your routes.php file. This may be used with a confide".
            " controller generated using [-r|--restful] option.";
        }
    }
}
