<?php namespace Zizaco\Confide;

use Mockery as m;
use PHPUnit_Framework_TestCase;
use Illuminate\Auth\Reminders\RemindableInterface;

class EloquentPasswordServiceTest extends PHPUnit_Framework_TestCase
{

    /**
     * ConfideRepository instance
     *
     * @var Zizaco\Confide\ConfideRepository
     */
    protected $repo;

    /**
     * Calls Mockery::close
     *
     * @return void
     */
    public function tearDown()
    {
        m::close();
    }

    public function testSouldRequestChangePassword()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        date_default_timezone_set('America/New_York');
        $userEmail = 'someone@somewhere.com';
        $generatedToken = '123456789';

        $user = m::mock('Illuminate\Auth\Reminders\RemindableInterface');
        $passService = m::mock('Zizaco\Confide\EloquentPasswordService[generateToken]',[]);
        $db = m::mock('connection');

        $passService->shouldAllowMockingProtectedMethods();

        $passService->app['db'] = $db;

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        // Since the user implements the RemindableInterface.
        // See: http://laravel.com/docs/security#password-reminders-and-reset
        $user->shouldReceive('getReminderEmail')
            ->andReturn($userEmail);

        // The PasswordService generate token method is the responsible
        // for generating tokens
        $passService->shouldReceive('generateToken')
            ->andReturn($generatedToken);

        // Mocks DB in order to check for the following query:
        //     DB::table('password_reminders')->insert(array(
        //    'email'=> $email,
        //    'token'=> $token,
        //    'created_at'=> new \DateTime
        //));
        $db->shouldReceive('connection')
            ->once()
            ->andReturn( $db );

        $db->shouldReceive('table')
            ->with( 'password_reminders' )
            ->once()
            ->andReturn( $db );

        $db->shouldReceive('insert')
            ->with([
                'email' => $userEmail,
                'token' => $generatedToken,
                'created_at' => new \DateTime
            ])
            ->once()
            ->andReturn(true);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertEquals(
            $generatedToken,
            $passService->requestChangePassword($user)
        );
    }

    public function testShouldGetEmailByToken()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $userEmail = 'someone@somewhere.com';
        $token = '123456789';
        $passService = new EloquentPasswordService;
        $db = m::mock('connection');

        $passService->app['db'] = $db;

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        // Mocks DB in order to check for the following query:
        //     DB::table('password_reminders')
        //         ->select('email')
        //         ->where('token','=',$token)
        //         ->first();
        $db->shouldReceive('connection')
            ->once()
            ->andReturn( $db );

        $db->shouldReceive('table')
            ->with('password_reminders')
            ->andReturn( $db )
            ->once();

        $db->shouldReceive('select')
            ->with('email')
            ->andReturn( $db )
            ->once();

        $db->shouldReceive('where')
            ->with('token', '=', $token)
            ->andReturn( $db )
            ->once();

        $db->shouldReceive('first')
            ->once()
            ->andReturn(['email' => $userEmail]);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertEquals(
            $userEmail,
            $passService->getEmailByToken($token)
        );
    }
}
