<?php namespace Zizaco\Confide;

use Zizaco\Confide\Support\GenerateCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * This command renders the package view generator.contoller and also
 * generator.repository into a file within the application directory
 * in order to save some time.
 *
 * @license MIT
 * @package  Zizaco\Confide
 */
class ControllerCommand extends GenerateCommand
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
            array('name', null, InputOption::VALUE_OPTIONAL, 'Name of the controller.', 'Users'),
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
        // Prepare variables
        $class = $this->getControllerName($this->option('name'));
        $namespace = $this->getNamespace($this->option('name'));
        $model = $this->app['config']->get('auth.model');
        $restful = $this->option('restful');
        $repository = $this->option('repository');

        $viewVars = compact(
            'class','namespace','model','restful','repository'
        );

        // Prompt
        $this->line('');
        $this->info("Controller name: $class".(($restful) ? "\nRESTful: Yes" : '') );
        $this->comment("An authentication ".(($restful) ? 'RESTful ' : '')."controller template with the name ".($namespace ? $namespace.'\\' : '')."$class.php".
        " will be created in app/controllers directory");
        $this->line('');

        if ( $this->confirm("Proceed with the controller creation? [Yes|no]") )
        {
            $this->info( "Creating $class..." );
            // Generate
            $filename = 'controllers/'.($namespace ? $namespace.'/' : '').$class.'.php';
            $this->generateFile($filename, 'generators.controller', $viewVars);
            $this->info( "$class.php Successfully created!" );

            if ($repository) {
                $filename = 'models/'.$model.'Repository.php';
                $this->generateFile($filename, 'generators.repository', $viewVars);
                $this->info( "$model.'Repository.php Successfully created!" );
            }
        }
    }

    /**
     * Returns the name of the controller class that will handle a
     * resource with the given name.
     * @param  string $name Resource name
     * @return string       Controller class name
     */
    protected function getControllerName($name)
    {
        if (strstr($name, '\\'))
        {
            $name = explode('\\', $name);
            $name = array_pop($name);
        }

        $name = ( $name != '') ? ucfirst($name) : 'Users';

        if( substr(strtolower($name),-10) == 'controller' )
        {
            $name = substr($name, 0, -10).'Controller';
        }
        else
        {
            $name .= 'Controller';
        }

        return $name;
    }

    /**
     * Returns the namespace of the given class name
     * @param  string $name Class name
     * @return string       Namespace
     */
    protected function getNamespace($name)
    {
        if (strstr($name, '\\'))
        {
            $name = explode('\\', $name);
            array_pop($name);
            $name = implode('\\', $name);
        } else {
            $name = '';
        }

        return $name;
    }
}
