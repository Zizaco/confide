<?php namespace Zizaco\Confide;

use Mockery as m;
use PHPUnit_Framework_TestCase;

class ConfideTest extends PHPUnit_Framework_TestCase
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

    public function testShouldGetModel()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $app = [];
        $repo = m::mock('Zizaco\Confide\RepositoryInterface');
        $passService = m::mock('Zizaco\Confide\PasswordServiceInterface');
        $confide = new Confide($repo, $passService, $app);
        $modelInstance = m::mock('_mockedUser');

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $repo->shouldReceive('model')
            ->once()->andReturn($modelInstance);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertEquals($modelInstance, $confide->model());
    }

    public function testShouldGetUser()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $app = [];
        $app['auth'] = m::mock('Auth');
        $repo = m::mock('Zizaco\Confide\RepositoryInterface');
        $passService = m::mock('Zizaco\Confide\PasswordServiceInterface');
        $confide = new Confide($repo, $passService, $app);
        $user = m::mock('_mockedUser');

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $app['auth']->shouldReceive('user')
            ->once()->andReturn($user);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertEquals($user, $confide->user());
    }

    public function testShouldConfirm()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $app = [];
        $repo = m::mock('Zizaco\Confide\RepositoryInterface');
        $passService = m::mock('Zizaco\Confide\PasswordServiceInterface');
        $confide = new Confide($repo, $passService, $app);
        $modelInstance = m::mock('_mockedUser');
        $code = '12345';

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $repo->shouldReceive('confirmByCode')
            ->once()->with($code)
            ->andReturn(true);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertTrue($confide->confirm($code));
    }
}
