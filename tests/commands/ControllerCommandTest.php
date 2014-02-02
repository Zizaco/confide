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
            ['name', null, InputOption::VALUE_OPTIONAL, 'Name of the controller.', 'User'],
            ['--restful', '-r', InputOption::VALUE_NONE, 'Generate RESTful controller.'],
            ['--repository', '-R', InputOption::VALUE_NONE, 'Generate a User Repository class.'],
        ];

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $config->shouldReceive('get')
            ->once()->with('auth.model')
            ->andReturn('User');

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertEquals($options, $command->getOptions());
    }
}
