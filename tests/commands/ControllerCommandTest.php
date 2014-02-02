<?php namespace Zizaco\Confide;

use Mockery as m;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ControllerCommandTest extends PHPUnit_Framework_TestCase
{

    /**
     * ConfideRepository instance
     *
     * @var Zizaco\Confide\ConfideRepository
     */
    protected $repo;

    /**
     * Calls Mockery::close
     *
     * @return void
     */
    public function tearDown()
    {
        m::close();
    }

    public function testSouldGetOptions()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $config = m::mock('Config');
        $app = ['config'=>$config];
        $command = m::mock('Zizaco\Confide\ControllerCommand', [$app]);
        $options = [
            ['name', null, InputOption::VALUE_OPTIONAL, 'Name of the controller.', 'Users'],
            ['--restful', '-r', InputOption::VALUE_NONE, 'Generate RESTful controller.'],
            ['--repository', '-R', InputOption::VALUE_NONE, 'Generate a User Repository class.'],
        ];

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertEquals($options, $command->getOptions());
    }

    public function testSouldFire()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $config = m::mock('Config');
        $app = ['config'=>$config];
        $command = m::mock('Zizaco\Confide\ControllerCommand', [$app]);
        $command->shouldAllowMockingProtectedMethods();
        $viewVars = [
            'class' => "UsersController",
            'namespace' => "",
            'model' => "User",
            'restful' => true,
            'repository' => true,
        ];

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $command->shouldReceive('getControllerName')
            ->once()->with('Users')
            ->andReturn('UsersController');

        $command->shouldReceive('getNamespace')
            ->once()->with('Users')
            ->andReturn('');

        $config->shouldReceive('get')
            ->once()->with('auth.model')
            ->andReturn('User');

        $command->shouldReceive('option')
            ->twice()->with('name')
            ->andReturn('Users');

        $command->shouldReceive('option')
            ->once()->with('restful')
            ->andReturn(true);

        $command->shouldReceive('option')
            ->once()->with('repository')
            ->andReturn(true);

        $command->shouldReceive('fire')
            ->passthru();

        $command->shouldReceive('line','info','comment','confirm')
            ->andReturn(true);

        $command->shouldReceive('generateFile')
            ->once()->with(
                'controllers/UsersController.php',
                'generators.controller',
                $viewVars
            )
            ->andReturn(true);

        $command->shouldReceive('generateFile')
            ->once()->with(
                'models/UserRepository.php',
                'generators.repository',
                $viewVars
            )
            ->andReturn(true);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $command->fire();
    }

    public function testSouldGetControlerName()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $app = [];
        $command = m::mock('Zizaco\Confide\ControllerCommand', [$app]);
        $command->shouldAllowMockingProtectedMethods();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $command->shouldReceive('getControllerName')
            ->passthru();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertEquals(
            'UsersController',
            $command->getControllerName('Users')
        );

        $this->assertEquals(
            'UsersController',
            $command->getControllerName('UsersController')
        );

        $this->assertEquals(
            'SomethingController',
            $command->getControllerName('Some\Namespace\Something')
        );

        $this->assertEquals(
            'CamelCaseController',
            $command->getControllerName('Some\Thing\CamelCase')
        );
    }

    public function testSouldGetNamespace()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $app = [];
        $command = m::mock('Zizaco\Confide\ControllerCommand', [$app]);
        $command->shouldAllowMockingProtectedMethods();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $command->shouldReceive('getNamespace')
            ->passthru();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertEquals(
            'Some\Namespace',
            $command->getNamespace('Some\Namespace\Something')
        );

        $this->assertEquals(
            'Some\Thing',
            $command->getNamespace('Some\Thing\Resource')
        );

        $this->assertEquals(
            '',
            $command->getNamespace('Users')
        );
    }
}
