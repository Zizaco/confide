<?php

use Zizaco\Confide\Confide;
use Zizaco\Confide\ConfideRepository;
use Mockery as m;

class ConfideTest extends PHPUnit_Framework_TestCase {

    /**
     * ConfideRepository instance
     *
     * @var Zizaco\Confide\ConfideRepository
     */
    protected $repo;

    public function setUp()
    {
        $app = $this->mockApp();
        $this->repo = new ConfideRepository();

        // Set the app attribute with mock
        $this->repo->app = $app;
    }

    public function tearDown()
    {
        m::close();
    }

    public function testGetModel()
    {
        // Make shure it grabbed the model from config
        $config = m::mock('Illuminate\Config\Repository');
        $config->shouldReceive('get')
            ->with('auth.model')
            ->andReturn( '_mockedUser' )
            ->once();
        $this->repo->app['config'] = $config;

        // Mocks an user
        $confide_user = $this->mockConfideUser();

        // Runs the `model()` method
        $user = $this->repo->model();

        // Assert the result
        $this->assertInstanceOf('_mockedUser', $user);
    }

    /**
     * Returns a mocked ConfideUser object for testing purposes
     * only
     * 
     * @return Illuminate\Auth\UserInterface A mocked confide user
     */
    private function mockConfideUser()
    {
        $confide_user = m::mock( 'Illuminate\Auth\UserInterface' );
        $confide_user->username = 'uname';
        $confide_user->password = '123123';
        $confide_user->confirmed = 0;
        $confide_user->shouldReceive('where','get', 'orWhere','first', 'all','getUserFromCredsIdentity')
            ->andReturn( $confide_user );

        return $confide_user;
    }

    /**
     * Mocks the application components that
     * are not Confide's responsibility
     * 
     * @return object Mocked laravel application
     */
    private function mockApp()
    {
        // Mocks the application components that
        // are not Confide's responsibility
        $app = array();

        $app['config'] = m::mock( 'Config' );
        $app['config']->shouldReceive( 'get' )
            ->with( 'auth.table' )
            ->andReturn( 'users' );

        $app['config']->shouldReceive( 'get' )
            ->with( 'auth.model' )
            ->andReturn( 'User' );

        $app['config']->shouldReceive( 'get' )
            ->with( 'app.key' )
            ->andReturn( '123' );

        $app['config']->shouldReceive( 'get' )
            ->with( 'confide::throttle_limit' )
            ->andReturn( 9 );

        $app['config']->shouldReceive( 'get' )
            ->andReturn( 'confide::login' );

        $app['mail'] = m::mock( 'Mail' );
        $app['mail']->shouldReceive('send')
            ->andReturn( null );

        $app['hash'] = m::mock( 'Hash' );
        $app['hash']->shouldReceive('make')
            ->andReturn( 'aRandomHash' );

        $app['cache'] = m::mock( 'Cache' );
        $app['cache']->shouldReceive('get')
            ->andReturn( 0 );
        $app['cache']->shouldReceive('put');

        $app['auth'] = m::mock( 'Auth' );
        $app['auth']->shouldReceive('login')
            ->andReturn( true );

        $app['request'] = m::mock( 'Request' );
        $app['request']->shouldReceive('server')
            ->andReturn( null );

        $app['db'] = m::mock( 'DatabaseManager' );
        $app['db']->shouldReceive('connection')
            ->andReturn( $app['db'] );
        $app['db']->shouldReceive('table')
            ->andReturn( $app['db'] );
        $app['db']->shouldReceive('select')
            ->andReturn( $app['db'] );
        $app['db']->shouldReceive('where')
            ->andReturn( $app['db'] );
        $app['db']->shouldReceive('first')
            ->andReturn( $app['db'] );
        $app['db']->email = 'test@example.com';

        $app['db']->shouldReceive('delete')
            ->andReturn( true );

        return $app;
    }

}

class _mockedUser {}
