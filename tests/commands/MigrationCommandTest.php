<?php namespace Zizaco\Confide;

use Mockery as m;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class MigrationCommandTest extends PHPUnit_Framework_TestCase
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
        $app = [];
        $command = m::mock('Zizaco\Confide\MigrationCommand', [$app]);
        $options = [
            ['table', null, InputOption::VALUE_OPTIONAL, 'Table name.', 'users'],
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
        $app = [];
        $command = m::mock('Zizaco\Confide\MigrationCommand', [$app]);
        $command->shouldAllowMockingProtectedMethods();
        $viewVars = [
            'table' => "users",
        ];

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $command->shouldReceive('option')
            ->once()->with('table')
            ->andReturn('users');

        $command->shouldReceive('fire')
            ->passthru();

        $command->shouldReceive('line','info','comment','confirm')
            ->andReturn(true);

        $command->shouldReceive('generateFile')
            ->once()->with(
                'database/migrations/'.date('Y_m_d_His').'_confide_setup_users.php',
                'generators.migration',
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
}
