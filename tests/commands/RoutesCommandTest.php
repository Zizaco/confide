<?php namespace Zizaco\Confide;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class RoutesCommandTest extends TestCase
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
        $app = [];
        $command = m::mock('Zizaco\Confide\RoutesCommand', [$app]);
        $options = [
            ['controller', null, InputOption::VALUE_OPTIONAL, 'Name of the controller.', 'UsersController'],
            ['--restful', '-r', InputOption::VALUE_NONE, 'Generate RESTful controller.'],
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
        $app = [];
        $command = m::mock('Zizaco\Confide\RoutesCommand', [$app]);
        $command->shouldAllowMockingProtectedMethods();
        $viewVars = [
            'controllerName' => 'UsersController',
            'url' => 'users',
            'restful' => true
        ];

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $command->shouldReceive('option')
            ->once()->with('controller')
            ->andReturn('UsersController');

        $command->shouldReceive('option')
            ->once()->with('restful')
            ->andReturn(true);

        $command->shouldReceive('getFireMessage')
            ->once()
            ->andReturn("Some message about appending the routes...");

        $command->shouldReceive('fire')
            ->passthru();

        $command->shouldReceive('line', 'info', 'comment', 'confirm')
            ->andReturn(true);

        $command->shouldReceive('appendInFile')
            ->once()->with(
                'routes.php',
                'generators.routes',
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

    public function testShouldGetFireMessage()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $app = [];
        $command = m::mock('Zizaco\Confide\RoutesCommand', [$app]);
        $command->shouldAllowMockingProtectedMethods();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $command->shouldReceive('getFireMessage')
            ->passthru();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertTrue(is_string($command->getFireMessage(true)));
        $this->assertTrue(is_string($command->getFireMessage(false)));
    }
}
