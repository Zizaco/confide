<?php

use Zizaco\Confide\ConfideUser;
use Mockery as m;

class ConfideUserTest extends PHPUnit_Framework_TestCase {

    /**
     * ConfideUser instance
     *
     * @var Zizaco/Confide/Confideuser
     */
    protected $confide_user;

    public function tearDown()
    {
        m::close();
    }

    public static function setUpBeforeClass()
    {
        /**
         * For ConfideUser real_save() method:
         * Runs the real eloquent save method or returns
         * true if it's under testing. Because eloquent
         * save method is not Confide's responsibility.
         *
         */
        define('CONFIDE_TEST', true);
    }

    public function setUp()
    {
        ConfideUser::$app = $this->mockApp();

        $this->confide_user = new ConfideUser;
    }

    private function populateUser()
    {
        $this->confide_user->username = 'uname';
        $this->confide_user->password = '123123';
        $this->confide_user->email = 'mail@sample.com';
        $this->confide_user->confirmation_code = '456456';
        $this->confide_user->confirmed = true;
    }

    public function testShouldGetAuthPassword()
    {
        $this->populateUser();

        $this->assertEquals( $this->confide_user->password, $this->confide_user->getAuthPassword() );
    }

    public function testShouldConfirm()
    {
        $this->populateUser();

        $this->assertNotEquals( 0, $this->confide_user->confirm() );

        $this->assertEquals( 1, $this->confide_user->confirmed );
    }

    public function testShouldSendForgotPassword()
    {
        // Should send an email once
        ConfideUser::$app['mailer'] = m::mock( 'Mail' );
        ConfideUser::$app['mailer']->shouldReceive('send')
            ->andReturn( null )
            ->atLeast(1);

        $this->populateUser();

        $old_password = $this->confide_user->password;

        $this->assertTrue( $this->confide_user->forgotPassword() );
    }

    public function testShouldChangePassword()
    {
        $credentials = array( 'email'=>'mail@sample.com', 'password'=>'987987' );

        $this->populateUser();

        $old_password = $this->confide_user->password;

        $this->assertTrue( $this->confide_user->resetPassword( $credentials ) );

        $new_password = $this->confide_user->password;

        // Should have generated a new password code
        $this->assertNotEquals( $old_password, $new_password );
    }

    public function testShouldGenerateConfirmationCodeOnSave()
    {
        // Should send an email once
        ConfideUser::$app['mailer'] = m::mock( 'Mail' );
        ConfideUser::$app['mailer']->shouldReceive('send')
            ->andReturn( null )
            ->once();

        $this->populateUser();
        $this->confide_user->confirmation_code = '';
        $this->confide_user->confirmed = false;

        $old_cc = $this->confide_user->confirmation_code;

        $this->assertTrue( $this->confide_user->save() );

        $new_cc = $this->confide_user->confirmation_code;

        // Should have generated a new confirmation code
        $this->assertNotEquals( $old_cc, $new_cc );
    }

    public function testShouldNotGenerateConfirmationCodeIfExists()
    {
        $this->populateUser();

        // Considering the model was already saved
        $this->confide_user->id = 1;

        $old_cc = $this->confide_user->confirmation_code;

        $this->assertTrue( $this->confide_user->save() );

        $new_cc = $this->confide_user->confirmation_code;

        // Should not change confirmation code
        $this->assertEquals( $old_cc, $new_cc );
    }

    private function mockApp()
    {
        // Mocks the application components
        $app = array();

        $app['config'] = m::mock( 'Config' );
        $app['config']->shouldReceive( 'get' )
            ->with( 'auth.table' )
            ->andReturn( 'users' );

        $app['config']->shouldReceive( 'getEnvironment' )
            ->andReturn( 'production' );

        $app['config']->shouldReceive( 'get' )
            ->with( 'app.key' )
            ->andReturn( '123' );

        $app['config']->shouldReceive( 'get' )
            ->with( 'confide::throttle_limit' )
            ->andReturn( 9 );

        $app['config']->shouldReceive( 'get' )
            ->andReturn( 'confide::login' );

        $app['mailer'] = m::mock( 'Mail' );
        $app['mailer']->shouldReceive('send')
            ->andReturn( null );

        $app['hash'] = m::mock( 'Hash' );
        $app['hash']->shouldReceive('make')
            ->andReturn( 'aRandomHash' );

        $app['db'] = m::mock( 'DatabaseManager' );
        $app['db']->shouldReceive('connection')
            ->andReturn( $app['db'] );
            
        $app['db']->shouldReceive('table')
            ->andReturn( $app['db'] );
            
        $app['db']->shouldReceive('insert')
            ->andReturn( $app['db'] );
            
        $app['db']->shouldReceive('where')
            ->andReturn( $app['db'] );

        $app['db']->shouldReceive('first')
            ->andReturn( $app['db'] );

        $app['db']->shouldReceive('update')
            ->andReturn( true );

        return $app;
    }

}
