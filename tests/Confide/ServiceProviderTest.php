<?php namespace Zizaco\Confide;

use Mockery as m;
use PHPUnit_Framework_TestCase;

class ServiceProviderTest extends PHPUnit_Framework_TestCase
{
    /**
     * Calls Mockery::close
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
        $sp = m::mock('Zizaco\Confide\ServiceProvider[package,commands]', ['something']);
        $test = $this;

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $sp->shouldReceive('package')
            ->with('zizaco/confide', 'confide', m::any())
            ->once()
            ->andReturnUsing(function ($a, $b, $c) use ($test) {
                $test->assertContains('confide/src/Confide/../', $c);
            });

        $sp->shouldReceive('commands')
            ->with('command.confide.controller', 'command.confide.routes', 'command.confide.migration')
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
            'registerLoginThrottleService,'.
            'registerUserValidator]',
            ['something']
        );
        $sp->shouldAllowMockingProtectedMethods();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $sp->shouldReceive(
            'registerRepository',
            'registerConfide',
            'registerCommands',
            'registerPasswordService',
            'registerLoginThrottleService',
            'registerUserValidator'
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
                function ($name, $closure) use ($test, $app) {
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
                function ($name, $closure) use ($test, $app) {
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
                function ($name, $closure) use ($test, $app) {
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

    public function testShouldRegisterUserValidator()
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
                // Make sure that the name is 'confide.user_validator'
                // and that the closure passed returns the correct
                // kind of object.
                function ($name, $closure) use ($test, $app) {
                    $test->assertEquals('confide.user_validator', $name);
                    $test->assertInstanceOf(
                        'Zizaco\Confide\UserValidator',
                        $closure($app)
                    );
                }
            );

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $sp->registerUserValidator();
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
                function ($name, $closure) use ($test, $app) {
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

    public function testShouldRegisterCommands()
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
            ->times(3)->andReturnUsing(
                // Make sure that the commands are being registered
                // with a closure that returns the correct
                // object.
                function ($name, $closure) use ($test, $app) {

                    $shouldBe = [
                        'command.confide.controller' => 'Zizaco\Confide\ControllerCommand',
                        'command.confide.routes'     => 'Zizaco\Confide\RoutesCommand',
                        'command.confide.migration'  => 'Zizaco\Confide\MigrationCommand',
                    ];

                    $test->assertInstanceOf(
                        $shouldBe[$name],
                        $closure($app)
                    );
                }
            );

        $sp->shouldReceive('commands')
            ->with(
                'command.confide.controller',
                'command.confide.routes',
                'command.confide.migration'
            );

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $sp->registerCommands();
    }

    public function testShouldProvide()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $app = m::mock('LaravelApp');
        $sp = m::mock('Zizaco\Confide\ServiceProvider', [$app]);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $shouldProvide = [
            'confide',
            'confide.repository',
            'confide.password',
            'confide.throttle',
            'confide.user_validator',
            'command.confide.controller',
            'command.confide.routes',
            'command.confide.migration'
        ];

        $sp->shouldReceive('provides')
            ->passthru();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertEquals(
            $shouldProvide,
            $sp->provides()
        );
    }
}
