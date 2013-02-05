<?php

use Zizaco\Confide\Confide;
use Mockery as m;

class ConfideTest extends PHPUnit_Framework_TestCase {

    /**
     * Confide instance
     *
     * @var Zizaco/Confide/Confide
     */
    protected $confide;

    public function tearDown()
    {
        m::close();
    }

    public function setUp()
    {
        $app = $this->mockApp();

        $this->confide = new Confide($app);
    }

    public function testGetVersion()
    {
        if( ! defined('CONFIDE_VERSION') )
            define('CONFIDE_VERSION','test');

        $is = $this->confide->version();
        $should_be = 'Confide v'.CONFIDE_VERSION;

        $this->assertEquals( $should_be, $is );
    }

    public function testGetModel()
    {
        // Make shure it grabbed the model from config
        $config = m::mock('Illuminate\Config\Repository');
        $config->shouldReceive('get')
            ->with('auth.model')
            ->andReturn( 'User' )
            ->once();
        $this->confide->_app['config'] = $config;

        // Mocks an user
        $confide_user = $this->mockConfideUser();

        // Make shure the object provider returns the
        // user object when called
        $this->objProviderShouldReturn( 'User', $confide_user );

        $user = $this->confide->model();

        $this->assertNotNull( $user );
    }

    public function testShouldGetUser()
    {
        $confide_user = $this->mockConfideUser();

        // Laravel auth component should return user
        $auth = m::mock('Illuminate\Auth\Guard');
        $auth->shouldReceive('user')
            ->andReturn( $confide_user )
            ->once();
        $this->confide->_app['auth'] = $auth;

        $this->assertEquals( $confide_user, $this->confide->user() );
    }

    public function testShouldConfirm()
    {
        $confide_user = $this->mockConfideUser();
        $confide_user->shouldReceive('confirm')
            ->andReturn( true )
            ->once();

        $this->objProviderShouldReturn( 'User', $confide_user );

        $this->assertTrue( $this->confide->confirm( '123123' ) );
    }

    public function testShouldlogAttempt()
    {
        $confide_user = $this->mockConfideUser();

        // Considering a valid hash check from hash component
        $hash = m::mock('Illuminate\Hashing\HasherInterface');
        $hash->shouldReceive('check')
            ->andReturn( true )
            ->times(2); // 2 successfull logins
        $this->confide->_app['hash'] = $hash;

        $this->objProviderShouldReturn( 'User', $confide_user );

        $this->assertTrue( 
            $this->confide->logAttempt( array( 'email'=>'username', 'password'=>'123123' ) )
        );

        // Should not login with unconfirmed user.
        $this->assertFalse( 
            $this->confide->logAttempt( array( 'email'=>'username', 'password'=>'123123' ), true )
        );

        $confide_user->confirmed = 1;

        // Should login because now the user is confirmed
        $this->assertTrue( 
            $this->confide->logAttempt( array( 'email'=>'username', 'password'=>'123123' ), true )
        );
    }

    public function testShouldResetPassword()
    {
        $confide_user = $this->mockConfideUser();
        $confide_user->shouldReceive('resetPassword')
            ->andReturn( true )
            ->once();

        $this->objProviderShouldReturn( 'User', $confide_user );

        $this->assertTrue( $this->confide->resetPassword( 'mail@sample.com' ) );
    }

    public function testShouldLogout()
    {
        // Make shure auth logout method is called
        $auth = m::mock('Illuminate\Auth\Guard');
        $auth->shouldReceive('logout')
            ->once();

        $this->confide->_app['auth'] = $auth;
        $this->assertEquals( null, $this->confide->logout() );
    }

    public function testShouldMakeForms()
    {
        // Make shure view make method is called 3 times
        $view = m::mock('Illuminate\View\Environment\View');
        $view->shouldReceive('make')
            ->andReturn( 'Content' )
            ->times( 3 );

        $this->confide->_app['view'] = $view;

        $this->assertNotEquals( null, $this->confide->MakeLoginForm() );
        $this->assertNotEquals( null, $this->confide->makeSignupForm() );
        $this->assertNotEquals( null, $this->confide->makeForgetPasswordForm() );
    }

    private function mockConfideUser()
    {
        $confide_user = m::mock( 'Illuminate\Auth\UserInterface' );
        $confide_user->username = 'uname';
        $confide_user->password = '123123';
        $confide_user->confirmed = 0;
        $confide_user->shouldReceive('where','get', 'orWhere','first', 'all')
            ->andReturn( $confide_user );

        return $confide_user;
    }

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

        $app['mail'] = m::mock( 'Mail' );
        $app['mail']->shouldReceive('send')
            ->andReturn( null );

        $app['hash'] = m::mock( 'Hash' );
        $app['hash']->shouldReceive('make')
            ->andReturn( 'aRandomHash' );

        $app['auth'] = m::mock( 'Auth' );
        $app['auth']->shouldReceive('login')
            ->andReturn( true );

        return $app;
    }

    /**
     * the ObjectProvider getObject method should
     * be called with $class to return $object.
     *
     * @param string $class
     * @param mixed $obj
     * @return void
     */
    private function objProviderShouldReturn( $class, $obj )
    {
        $obj_provider = m::mock('ObjectProvider');
        $obj_provider->shouldReceive('getObject')
            ->with($class)
            ->andReturn( $obj );
        
        $this->confide->_obj_provider = $obj_provider;
    }
}
