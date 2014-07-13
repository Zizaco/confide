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
        $passService = m::mock('Zizaco\Confide\EloquentPasswordService[generateToken,sendEmail]',[]);
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

        // The email containing the reset link should be sent
        $passService->shouldReceive('sendEmail')
            ->once()
            ->andReturn($user, $generatedToken);

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
        $passService = m::mock('Zizaco\Confide\EloquentPasswordService');
        $passService->shouldAllowMockingProtectedMethods();
        $db = m::mock('connection');

        $passService->app['db'] = $db;

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $passService->shouldReceive('getEmailByToken')
            ->passthru();

        $passService->shouldReceive('unwrapEmail')
            ->once()->with(['email'=>$userEmail])
            ->andReturn($userEmail);

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

    public function testShouldGenerateToken()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $passService = m::mock('Zizaco\Confide\EloquentPasswordService');
        $passService->shouldAllowMockingProtectedMethods();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $passService->shouldReceive('generateToken')
            ->passthru();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertTrue(is_string($passService->generateToken()));
    }

    public function testShouldUnwrapEmail()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $passService = m::mock('Zizaco\Confide\EloquentPasswordService');
        $passService->shouldAllowMockingProtectedMethods();
        $email = 'someone@somewhere.com';
        $emailArray = ['email'=>$email];
        $emailObject = m::mock('UserWithEmail');

        $emailObject->email = $email;

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $passService->shouldReceive('unwrapEmail')
            ->passthru();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertEquals($email, $passService->unwrapEmail($email));
        $this->assertEquals($email, $passService->unwrapEmail($emailArray));
        $this->assertEquals($email, $passService->unwrapEmail($emailObject));
    }

    public function testShouldSendEmail()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $passService = m::mock('Zizaco\Confide\EloquentPasswordService[sendEmail]');
        $passService->shouldAllowMockingProtectedMethods();

        $mailer                         = m::mock('Mailer');
        $config                         = m::mock('Config');
        $translator                     = m::mock('Translator');
        $passService->app['mailer']     = $mailer;
        $passService->app['config']     = $config;
        $passService->app['translator'] = $translator;

        $token = '123';
        $user  = m::mock('UserWithEmail');
        $user->email = 'someone@somewhere.com';
        $user->username = 'Someone';

        $test = $this;

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $passService->shouldReceive('sendEmail')
            ->passthru();

        $mailer->shouldReceive('send')
            ->once()->with('view.name', ['user'=>$user, 'token'=>$token], m::any())
            ->andReturnUsing(function($a,$b,$closure) use ($test, $user) {
                $message = m::mock('Message');

                $message->shouldReceive('to')
                    ->once()->with($user->email, $user->username)
                    ->andReturn($message);

                $message->shouldReceive('subject')
                    ->once()->with('the-email-subject')
                    ->andReturn($message);

                $closure($message);
            });

        $translator->shouldReceive('get')
            ->once()->with('confide::confide.email.password_reset.subject')
            ->andReturn('the-email-subject');

        $config->shouldReceive('get')
            ->once()->with('confide::email_reset_password')
            ->andReturn('view.name');

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $passService->sendEmail($user, $token);
    }
}
