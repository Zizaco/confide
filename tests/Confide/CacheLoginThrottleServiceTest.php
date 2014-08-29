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

        $throttleService = m::mock('Zizaco\Confide\CacheLoginThrottleService[countThrottle, parseIdentity]', []);
        $throttleService->shouldAllowMockingProtectedMethods();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $throttleService->shouldReceive('parseIdentity')
            ->once()->with($identity)
            ->andReturn('someone@somewhere.com');

        $throttleService->shouldReceive('countThrottle')
            ->once()->with('someone@somewhere.com')
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
        $app = ['config'=>$config];

        $throttleService = m::mock('Zizaco\Confide\CacheLoginThrottleService[countThrottle,parseIdentity]', [$app]);
        $throttleService->shouldAllowMockingProtectedMethods();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $throttleService->shouldReceive('parseIdentity')
            ->once()->with($identity)
            ->andReturn('someone@somewhere.com');

        $throttleService->shouldReceive('countThrottle')
            ->once()->with('someone@somewhere.com', 0)
            ->andReturn(10); // More than the limit specified bellow

        $config->shouldReceive('get')
            ->once()->with('confide::throttle_limit')
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
        $app = ['config'=>$config];

        $throttleService = m::mock('Zizaco\Confide\CacheLoginThrottleService[countThrottle,parseIdentity]', [$app]);
        $throttleService->shouldAllowMockingProtectedMethods();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $throttleService->shouldReceive('parseIdentity')
            ->once()->with($identity)
            ->andReturn('someone@somewhere.com');

        $throttleService->shouldReceive('countThrottle')
            ->once()->with('someone@somewhere.com', 0)
            ->andReturn(5); // Less than the limit specified bellow

        $config->shouldReceive('get')
            ->once()->with('confide::throttle_limit')
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
        $config = m::mock('Config');
        $app = ['config'=>$config];
        $throttleService = m::mock('Zizaco\Confide\CacheLoginThrottleService[parseIdentity]', [$app]);
        $throttleService->shouldAllowMockingProtectedMethods();
        $identity = ['email'=>'someone@somewhere.com','password'=>'123'];

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $config->shouldReceive('get')
           ->once()->with('confide::login_throttle_field')
           ->andReturn('email');
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

    public function testShouldParseIdentityWithUsername()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $config = m::mock('Config');
        $app = ['config'=>$config];
        $throttleService = m::mock('Zizaco\Confide\CacheLoginThrottleService[parseIdentity]', [$app]);
        $throttleService->shouldAllowMockingProtectedMethods();
        $identity = ['email'=>'someone@somewhere.com','username'=>'someone','password'=>'123'];

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $config->shouldReceive('get')
           ->once()->with('confide::login_throttle_field')
           ->andReturn('username');
        $throttleService->shouldReceive('parseIdentity')
            ->passthru();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertEquals(
            'someone',
            $throttleService->parseIdentity($identity)
        );
    }

    public function testShouldParseIdentityWithSpecialInput()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $config = m::mock('Config');
        $app = ['config'=>$config];
        $throttleService = m::mock('Zizaco\Confide\CacheLoginThrottleService[parseIdentity]', [$app]);
        $throttleService->shouldAllowMockingProtectedMethods();
        $identity = ['mail'=>'someone@somewhere.com','name'=>'someone','password'=>'123','remember'=>1,'_token'=>'anything'];

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $config->shouldReceive('get')
           ->once()->with('confide::login_throttle_field')
           ->andReturn('username');
        $throttleService->shouldReceive('parseIdentity')
            ->passthru();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $expected = ['mail'=>'someone@somewhere.com','name'=>'someone'];
        $this->assertEquals(
            serialize($expected),
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
        $idString = 'someone@somewhere.com';
        $cache = m::mock('Cache');
        $config = m::mock('Config');
        $app = ['cache'=>$cache, 'config'=>$config];

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
            ->once()->with('confide::throttle_time_period')
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
