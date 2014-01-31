<?php namespace Zizaco\Confide;

use Mockery as m;
use PHPUnit_Framework_TestCase;

class ServiceProviderTest extends PHPUnit_Framework_TestCase
{
    /**
     * Calls Mockery::close
     *
     * @return void
     */
    public function tearDown()
    {
        m::close();
    }

    public function testShouldBoot()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $sp = m::mock('Zizaco\Confide\ServiceProvider[package]', ['something']);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $sp->shouldReceive('package')
            ->with('zizaco/confide')
            ->once();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $sp->boot();
    }

    public function testShouldRegister()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $sp = m::mock(
            'Zizaco\Confide\ServiceProvider'.
            '[registerRepository,registerConfide,registerCommands]',
            ['something']
        );
        $sp->shouldAllowMockingProtectedMethods();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $sp->shouldReceive(
                'registerRepository','registerConfide',
                'registerCommands'
            )
            ->once();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $sp->register();
    }

    public function testShouldRegisterRepository()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $test = $this;
        $app = m::mock('LaravelApp');
        $sp = new ServiceProvider($app);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $app->shouldReceive('bind')
            ->once()->andReturnUsing(
                // Make sure that the name is 'confide.repository'
                // and that the closure passed returns the correct
                // kind of object.
                function($name, $closure) use ($test, $app) {
                    $test->assertEquals('confide.repository', $name);
                    $test->assertInstanceOf(
                        'Zizaco\Confide\EloquentRepository',
                        $closure($app)
                    );
                }
            );

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $sp->register();
    }
}
