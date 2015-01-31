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
            ['username', null, InputOption::VALUE_NONE, 'Includes an unique username column.']
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
            'includeUsername' => true
        ];

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $command->shouldReceive('option')
            ->once()->with('table')
            ->andReturn('users');

        $command->shouldReceive('option')
            ->once()->with('username')
            ->andReturn(true);

        $command->shouldReceive('fire')
            ->passthru();

        $command->shouldReceive('line', 'info', 'comment', 'confirm')
            ->andReturn(true);

        $command->shouldReceive('generateFile')
            ->once()->with(
                'database/migrations/'.date('Y_m_d_His').'_confide_setup_users_table.php',
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
