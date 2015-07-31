<?php namespace Zizaco\Confide;

use Mockery as m;
use PHPUnit_Framework_TestCase;

class CacheLoginThrottleServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * Calls Mockery::close
     */
    public function tearDown()
    {
        m::close();
    }

    public function testShouldThrottleIdentity()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $identity = ['email'=>'someone@somewhere.com','password'=>'123'];

        $throttleService = m::mock(
            'Zizaco\Confide\CacheLoginThrottleService[countThrottle, parseIdentity]',
            [m::mock('Illuminate\Foundation\Application')]
        );
        $throttleService->shouldAllowMockingProtectedMethods();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $throttleService->shouldReceive('parseIdentity')
            ->once()->with($identity)
            ->andReturn(serialize(['email'=>'someone@somewhere.com']));

        $throttleService->shouldReceive('countThrottle')
            ->once()->with(serialize(['email'=>'someone@somewhere.com']))
            ->andReturn(5);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertEquals(5, $throttleService->throttleIdentity($identity));
    }

    public function testShouldCheckIsThrottled()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $identity = ['email'=>'someone@somewhere.com','password'=>'123'];
        $config = m::mock('Config');
        $app = m::mock('Illuminate\Foundation\Application');
        $app->shouldReceive('make')->with('config')->once()->andReturn($config);

        $throttleService = m::mock('Zizaco\Confide\CacheLoginThrottleService[countThrottle,parseIdentity]', [$app]);
        $throttleService->shouldAllowMockingProtectedMethods();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $throttleService->shouldReceive('parseIdentity')
            ->once()->with($identity)
            ->andReturn(serialize(['email'=>'someone@somewhere.com']));

        $throttleService->shouldReceive('countThrottle')
            ->once()->with(serialize(['email'=>'someone@somewhere.com']), 0)
            ->andReturn(10); // More than the limit specified bellow

        $config->shouldReceive('get')
            ->once()->with('confide.throttle_limit')
            ->andReturn(9);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertTrue($throttleService->isThrottled($identity));
    }

    public function testShouldCheckIsThrottledOnNonThrottled()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $identity = ['email'=>'someone@somewhere.com','password'=>'123'];
        $config = m::mock('Config');
        $app = m::mock('Illuminate\Foundation\Application');
        $app->shouldReceive('make')->with('config')->once()->andReturn($config);

        $throttleService = m::mock('Zizaco\Confide\CacheLoginThrottleService[countThrottle,parseIdentity]', [$app]);
        $throttleService->shouldAllowMockingProtectedMethods();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $throttleService->shouldReceive('parseIdentity')
            ->once()->with($identity)
            ->andReturn(serialize(['email'=>'someone@somewhere.com']));

        $throttleService->shouldReceive('countThrottle')
            ->once()->with(serialize(['email'=>'someone@somewhere.com']), 0)
            ->andReturn(5); // Less than the limit specified bellow

        $config->shouldReceive('get')
            ->once()->with('confide.throttle_limit')
            ->andReturn(9);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertFalse($throttleService->isThrottled($identity));
    }

    public function testShouldParseIdentity()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $app = m::mock('Illuminate\Foundation\Application');
        $throttleService = m::mock('Zizaco\Confide\CacheLoginThrottleService[parseIdentity]', [$app]);
        $throttleService->shouldAllowMockingProtectedMethods();
        $identity = [
            'email'=>'someone@somewhere.com',
            'password'=>'123',
            '_token'=>'somethingusual',
            'remember'=>true
        ];

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $throttleService->shouldReceive('parseIdentity')
            ->passthru();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertEquals(
            'someone@somewhere.com',
            $throttleService->parseIdentity($identity)
        );
    }

    public function testShouldParseIdentityUsername()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $app = m::mock('Illuminate\Foundation\Application');
        $throttleService = m::mock('Zizaco\Confide\CacheLoginThrottleService[parseIdentity]', [$app]);
        $throttleService->shouldAllowMockingProtectedMethods();
        $identity = [
            'username'=>'someuser',
            'password'=>'123',
            '_token'=>'somethingusual',
            'remember'=>true
        ];

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $throttleService->shouldReceive('parseIdentity')
            ->passthru();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertEquals(
            'someuser',
            $throttleService->parseIdentity($identity)
        );
    }

    public function testShouldParseIdentitySerialize()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $app = m::mock('Illuminate\Foundation\Application');
        $throttleService = m::mock('Zizaco\Confide\CacheLoginThrottleService[parseIdentity]', [$app]);
        $throttleService->shouldAllowMockingProtectedMethods();
        $identity = [
            'password'=>'123',
            '_token'=>'somethingusual',
            'remember'=>true
        ];

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $throttleService->shouldReceive('parseIdentity')
            ->passthru();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertEquals(
            serialize($identity),
            $throttleService->parseIdentity($identity)
        );
    }

    public function testShouldParseIdentityNotArray()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $app = m::mock('Illuminate\Foundation\Application');
        $throttleService = m::mock('Zizaco\Confide\CacheLoginThrottleService[parseIdentity]', [$app]);
        $throttleService->shouldAllowMockingProtectedMethods();
        $identity = 'someuser';

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $throttleService->shouldReceive('parseIdentity')
            ->passthru();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertEquals(
            $identity,
            $throttleService->parseIdentity($identity)
        );
    }

    public function testShouldCountThrottle()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $idString = serialize(['email'=>'someone@somewhere.com']);
        $cache = m::mock('Cache');
        $config = m::mock('Config');
        $app = m::mock('Illuminate\Foundation\Application');
        $app->shouldReceive('make')->with('config')->once()->andReturn($config);
        $app->shouldReceive('make')->with('cache')->twice()->andReturn($cache);

        $throttleService = m::mock('Zizaco\Confide\CacheLoginThrottleService[countThrottle]', [$app]);

        $ttl = 3;

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $cache->shouldReceive('get')
            ->once()->with('login_throttling:'.md5($idString), 0)
            ->andReturn(1);

        $config->shouldReceive('get')
            ->once()->with('confide.throttle_time_period')
            ->andReturn($ttl);

        $cache->shouldReceive('put')
            ->once()->with('login_throttling:'.md5($idString), 2, $ttl)
            ->andReturn(1);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertEquals(2, $throttleService->countThrottle($idString));
    }
}
