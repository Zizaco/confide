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
            '[registerRepository,registerPasswordService,'.
            'registerConfide,registerCommands,'.
            'registerLoginThrottleService]',
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
                'registerCommands','registerPasswordService',
                'registerLoginThrottleService'
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
        $sp = m::mock('Zizaco\Confide\ServiceProvider', [$app]);

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
        $sp->registerRepository();
    }

    public function testShouldRegisterPasswordService()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $test = $this;
        $app = m::mock('LaravelApp');
        $sp = m::mock('Zizaco\Confide\ServiceProvider', [$app]);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $app->shouldReceive('bind')
            ->once()->andReturnUsing(
                // Make sure that the name is 'confide.password'
                // and that the closure passed returns the correct
                // kind of object.
                function($name, $closure) use ($test, $app) {
                    $test->assertEquals('confide.password', $name);
                    $test->assertInstanceOf(
                        'Zizaco\Confide\EloquentPasswordService',
                        $closure($app)
                    );
                }
            );

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $sp->registerPasswordService();
    }

    public function testShouldRegisterLoginThrottleService()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $test = $this;
        $app = m::mock('LaravelApp');
        $sp = m::mock('Zizaco\Confide\ServiceProvider', [$app]);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $app->shouldReceive('bind')
            ->once()->andReturnUsing(
                // Make sure that the name is 'confide.throttle'
                // and that the closure passed returns the correct
                // kind of object.
                function($name, $closure) use ($test, $app) {
                    $test->assertEquals('confide.throttle', $name);
                    $test->assertInstanceOf(
                        'Zizaco\Confide\CacheLoginThrottleService',
                        $closure($app)
                    );
                }
            );

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $sp->registerLoginThrottleService();
    }

    public function testShouldRegisterConfide()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $test = $this;
        $app = m::mock('LaravelApp');
        $sp = m::mock('Zizaco\Confide\ServiceProvider', [$app]);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $app->shouldReceive('make')
            ->once()->with('confide.repository')
            ->andReturn(new EloquentRepository);

        $app->shouldReceive('make')
            ->once()->with('confide.password')
            ->andReturn(new EloquentPasswordService);

        $app->shouldReceive('make')
            ->once()->with('confide.throttle')
            ->andReturn(new CacheLoginThrottleService);

        $app->shouldReceive('bind')
            ->once()->andReturnUsing(
                // Make sure that the name is 'confide'
                // and that the closure passed returns the correct
                // kind of object.
                function($name, $closure) use ($test, $app) {
                    $test->assertEquals('confide', $name);
                    $test->assertInstanceOf(
                        'Zizaco\Confide\Confide',
                        $closure($app)
                    );
                }
            );

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $sp->registerConfide();
    }
}
