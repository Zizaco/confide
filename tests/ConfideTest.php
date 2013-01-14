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
        $view = m::mock('Illuminate\View\Environment\View');
        $config = m::mock('Illuminate\Config\Repository');
        $database = m::mock('Illuminate\Database\DatabaseManager');

        $this->confide = new Confide($view, $config, $database);
    }

    public function testGetVersion()
    {
        $is = $this->confide->Version();
        $should_be = 'Confide v'.Confide::VERSION;

        $this->assertEquals( $should_be, $is );
    }

    public function testGetModel()
    {
        $config = m::mock('Illuminate\Config\Repository', ['get'=>'user']);
        $this->confide->_config = $config;
        
        $database = m::mock('Illuminate\Database\DatabaseManager');
        $database->shouldReceive('table')
            ->with( $this->confide->_config->get('auth.table') )
            ->andReturn('true')->once();
        $this->confide->_database = $database;

        $queryBuilder = $this->confide->Model();
        $this->assertNotNull( $queryBuilder );
    }

    public function testShouldConfirm()
    {
        $user = m::mock('ConfideUser');
        $user->shouldReceive('Confirm')->andReturn(true)->once();

        $this->assertTrue( $this->confide->Confirm( $user ) );
    }

    public function testShouldResetPassword()
    {
        $user = m::mock('ConfideUser');
        $user->shouldReceive('ResetPassword')->andReturn(true)->once();

        $this->assertTrue( $this->confide->ResetPassword( $user ) );
    }

    public function testShouldMakeLoginForm()
    {
        $view = m::mock('Illuminate\View\Environment\View');
        $view->shouldReceive('make')->with('confide::login')->andReturn('something')->once();

        $this->confide->_view = $view;
        $this->assertNotEquals( null, $this->confide->MakeLoginForm() );
    }

    public function testShouldMakeSignupForm()
    {
        $view = m::mock('Illuminate\View\Environment\View');
        $view->shouldReceive('make')->with('confide::signup')->andReturn('something')->once();

        $this->confide->_view = $view;
        $this->assertNotEquals( null, $this->confide->MakeSignupForm() );
    }
}
