<?php

use Zizaco\Confide\Confide;
use Mockery as m;

class ConfideTest extends PHPUnit_Framework_TestCase {

    protected $confide;

    public function tearDown()
    {
        m::close();
    }

    public function setUp()
    {
        $app = array();

        $this->confide = new Confide($app);
    }

    public function testGetVersion()
    {
        $is = $this->confide->version();
        $should_be = 'Confide v'.Confide::VERSION;

        $this->assertEquals( $should_be, $is );
    }

    public function testGetModel()
    {
        $config = m::mock('Illuminate\Config\Repository');
        $config->shouldReceive('get')
            ->with('auth.model')
            ->andReturn( 'User' )
            ->once();

        $confide_user = m::mock('ConfideUser');
        $obj_provider = m::mock('ObjectProvider');
        $obj_provider->shouldReceive('getObject')
            ->with('User')
            ->andReturn( $confide_user )
            ->once();

        $this->confide->_app['config'] = $config;
        $this->confide->_obj_provider = $obj_provider;

        $user = $this->confide->model();
        $this->assertNotNull( $user );
    }

    public function testShouldGetUser()
    {
        $confide_user = m::mock('ConfideUser');

        $auth = m::mock('Illuminate\Auth\Guard');
        $auth->shouldReceive('user')
            ->andReturn( $confide_user )
            ->once();

        $this->confide->_app['auth'] = $auth;
        $this->assertEquals( $confide_user, $this->confide->user() );
    }

    public function testShouldConfirm()
    {
        $config = m::mock('Illuminate\Config\Repository');
        $config->shouldReceive('get')
            ->with('auth.model')
            ->andReturn( 'User' )
            ->once();

        $confide_user = m::mock('ConfideUser');
        $confide_user->shouldReceive('where','get', 'orWhere','first', 'all')
            ->andReturn( $confide_user );
        $confide_user->shouldReceive('confirm')
            ->andReturn( true )
            ->once();

        $obj_provider = m::mock('ObjectProvider');
        $obj_provider->shouldReceive('getObject')
            ->with('User')
            ->andReturn( $confide_user )
            ->once();

        $this->confide->_app['config'] = $config;
        $this->confide->_obj_provider = $obj_provider;

        $this->assertTrue( $this->confide->confirm( '123123' ) );
    }

    public function testShouldlogAttempt()
    {
        $config = m::mock('Illuminate\Config\Repository');
        $config->shouldReceive('get')
            ->with('auth.model')
            ->andReturn( 'User' )
            ->once();

        $confide_user = m::mock('ConfideUser' );
        $confide_user->username = 'uname';
        $confide_user->password = '123123';
        $confide_user->shouldReceive('where','get', 'orWhere','first', 'all')
            ->andReturn( $confide_user );

        $obj_provider = m::mock('ObjectProvider');
        $obj_provider->shouldReceive('getObject')
            ->with('User')
            ->andReturn( $confide_user )
            ->once();

        $hash = m::mock('Illuminate\Hashing\HasherInterface');
        $hash->shouldReceive('check')
            ->andReturn( true )
            ->once();

        $auth = m::mock('Illuminate\Auth\Guard');
        $auth->shouldReceive('login')
            ->with( $confide_user, null )
            ->once();

        $this->confide->_app['config'] = $config;
        $this->confide->_app['hash'] = $hash;
        $this->confide->_app['auth'] = $auth;
        $this->confide->_obj_provider = $obj_provider;

        $this->assertTrue( 
            $this->confide->logAttempt( array( 'email'=>'username', 'password'=>'123123' ) )
        );
    }

    public function testShouldResetPassword()
    {
        $config = m::mock('Illuminate\Config\Repository');
        $config->shouldReceive('get')
            ->with('auth.model')
            ->andReturn( 'User' )
            ->once();

        $confide_user = m::mock('ConfideUser');
        $confide_user->shouldReceive('where','get','first')
            ->andReturn( $confide_user );
        $confide_user->shouldReceive('resetPassword')
            ->andReturn( true )
            ->once();

        $obj_provider = m::mock('ObjectProvider');
        $obj_provider->shouldReceive('getObject')
            ->with('User')
            ->andReturn( $confide_user )
            ->once();

        $this->confide->_app['config'] = $config;
        $this->confide->_obj_provider = $obj_provider;

        $this->assertTrue( $this->confide->resetPassword( 'mail@sample.com' ) );
    }

    public function testShouldLogout()
    {
        $auth = m::mock('Illuminate\Auth\Guard');
        $auth->shouldReceive('logout')
            ->once();

        $this->confide->_app['auth'] = $auth;
        $this->assertEquals( null, $this->confide->logout() );
    }

    public function testShouldMakeForms()
    {
        $view = m::mock('Illuminate\View\Environment\View');
        $view->shouldReceive('make')
            ->andReturn( 'Content' )
            ->times( 3 );

        $this->confide->_app['view'] = $view;

        $this->assertNotEquals( null, $this->confide->MakeLoginForm() );
        $this->assertNotEquals( null, $this->confide->makeSignupForm() );
        $this->assertNotEquals( null, $this->confide->makeForgetPasswordForm() );
    }
}
