<?php namespace Zizaco\Confide;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ControllerCommandTest extends TestCase
{

    /**
     * ConfideRepository instance
     *
     * @var Zizaco\Confide\ConfideRepository
     */
    protected $repo;

    /**
     * Calls Mockery::close
     */
    protected function tearDown(): void
    {
        $this->addToAssertionCount(m::getContainer()->mockery_getExpectationCount());
        m::close();
    }

    public function testShouldGetOptions()
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
            ['username', null, InputOption::VALUE_NONE, 'Includes username as a required parameter.']
        ];

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertEquals($options, $command->getOptions());
    }

    public function testShouldFire()
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
            'namespace' => "The\\Namespace",
            'model' => "User",
            'restful' => true,
            'includeUsername' => true
        ];

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $command->shouldReceive('getControllerName')
            ->once()->with('The\\Namespace\\Users')
            ->andReturn('UsersController');

        $command->shouldReceive('getNamespace')
            ->once()->with('The\\Namespace\\Users')
            ->andReturn('The\\Namespace');

        $config->shouldReceive('get')
            ->once()->with('auth.model')
            ->andReturn('User');

        $command->shouldReceive('option')
            ->twice()->with('name')
            ->andReturn('The\\Namespace\\Users');

        $command->shouldReceive('option')
            ->once()->with('restful')
            ->andReturn(true);

        $command->shouldReceive('option')
            ->once()->with('username')
            ->andReturn(true);

        $command->shouldReceive('fire')
            ->passthru();

        $command->shouldReceive('line', 'info', 'comment', 'confirm')
            ->andReturn(true);

        $command->shouldReceive('generateFile')
            ->once()->with(
                'controllers/The/Namespace/UsersController.php',
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

    public function testShouldGetControlerName()
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

    public function testShouldGetNamespace()
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
