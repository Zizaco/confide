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
        $GLOBALS['_phpunit_confide_test'] = true;
    }

    public function setUp()
    {
        ConfideUser::$_app = $this->mockApp();

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

        $this->assertTrue( $this->confide_user->confirm() );
    }

    public function testShouldResetPassword()
    {
        // Should send an email once
        ConfideUser::$_app['mail'] = m::mock( 'Mail' );
        ConfideUser::$_app['mail']->shouldReceive('send')
            ->andReturn( null )
            ->atLeast(1);

        $this->populateUser();

        $old_password = $this->confide_user->password;

        $this->assertTrue( $this->confide_user->resetPassword() );

        $new_password = $this->confide_user->password;

        // Should have generated a new password code
        $this->assertNotEquals( $old_password, $new_password );
    }

    public function testShouldGenerateConfirmationCodeOnSave()
    {
        // Should send an email once
        ConfideUser::$_app['mail'] = m::mock( 'Mail' );
        ConfideUser::$_app['mail']->shouldReceive('send')
            ->andReturn( null )
            ->once();

        $this->populateUser();

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

        $app['config']->shouldReceive( 'get' )
            ->with( 'app.key' )
            ->andReturn( '123' );

        $app['mail'] = m::mock( 'Mail' );
        $app['mail']->shouldReceive('send')
            ->andReturn( null );

        $app['hash'] = m::mock( 'Hash' );
        $app['hash']->shouldReceive('make')
            ->andReturn( 'aRandomHash' );

        return $app;
    }

}
