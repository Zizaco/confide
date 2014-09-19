<?php namespace Zizaco\Confide;

use Mockery as m;
use PHPUnit_Framework_TestCase;

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
        $passService = m::mock('Zizaco\Confide\EloquentPasswordService[generateToken,sendEmail,getTable]', []);
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

        // Retrieve the connection name that is being used in the user model
        $user->shouldReceive('getConnectionName')
            ->andReturn('db_name');

        // The PasswordService generate token method is the responsible
        // for generating tokens
        $passService->shouldReceive('generateToken')
            ->andReturn($generatedToken);

        $passService->shouldReceive('getTable')
            ->andReturn('tbl_name');

        // The email containing the reset link should be sent
        $passService->shouldReceive('sendEmail')
            ->once()->with($user, $generatedToken);

        // Mocks DB in order to check for the following query:
        //     DB::connection('db_name')
        //         ->table('password_reminders')->insert(array(
        //             'email'=> $email,
        //             'token'=> $token,
        //             'created_at'=> new \DateTime
        //         ));
        $db->shouldReceive('connection')
            ->once()->with('db_name')
            ->andReturn($db);

        $db->shouldReceive('table')
            ->with('tbl_name')
            ->once()
            ->andReturn($db);

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
        $userEmail       = 'someone@somewhere.com';
        $token           = '123456789';
        $oldestValidDate = '2014-07-16 22:20:26';
        $passService = m::mock('Zizaco\Confide\EloquentPasswordService');
        $db          = m::mock('connection');
        $userModel   = m::mock('Zizaco\Confide\ConfideUserInterface');

        $passService->shouldAllowMockingProtectedMethods();
        $passService->app['db'] = $db;

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $passService->shouldReceive('getEmailByToken')
            ->passthru();

        $passService->shouldReceive('getConnection')
            ->once()->andReturn('db_name');

        $passService->shouldReceive('getTable')
            ->andReturn('tbl_name');

        $passService->shouldReceive('getOldestValidDate')
            ->once()->andReturn($oldestValidDate);

        $passService->shouldReceive('unwrapEmail')
            ->once()->with(['email'=>$userEmail])
            ->andReturn($userEmail);

        // Mocks DB in order to check for the following query:
        //     DB::connection('db_name')
        //         ->table('password_reminders')
        //         ->select('email')
        //         ->where('token','=',$token)
        //         ->first();
        $db->shouldReceive('connection')
            ->once()->with('db_name')
            ->andReturn($db);

        $db->shouldReceive('table')
            ->with('tbl_name')
            ->andReturn($db)
            ->once();

        $db->shouldReceive('select')
            ->with('email')
            ->andReturn($db)
            ->once();

        $db->shouldReceive('where')
            ->with('token', '=', $token)
            ->andReturn($db)
            ->once();

        $db->shouldReceive('where')
            ->with('created_at', '>=', $oldestValidDate)
            ->andReturn($db)
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

    public function testShouldDestroyToken()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $token       = '123456789';
        $passService = m::mock('Zizaco\Confide\EloquentPasswordService');
        $db          = m::mock('connection');

        $passService->shouldAllowMockingProtectedMethods();
        $passService->app['db'] = $db;

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $passService->shouldReceive('destroyToken')
            ->passthru();

        $passService->shouldReceive('getConnection')
            ->once()->andReturn('db_name');

        $passService->shouldReceive('getTable')
            ->andReturn('tbl_name');

        // Mocks DB in order to check for the following query:
        //     DB::connection('db_name')
        //         ->table('password_reminders')
        //         ->where('token','=',$token)
        //         ->delete();
        $db->shouldReceive('connection')
            ->once()->with('db_name')
            ->andReturn($db);

        $db->shouldReceive('table')
            ->with('tbl_name')
            ->andReturn($db)
            ->once();

        $db->shouldReceive('where')
            ->with('token', '=', $token)
            ->andReturn($db)
            ->once();

        $db->shouldReceive('delete')
            ->once()
            ->andReturn(1);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertTrue(
            $passService->destroyToken($token)
        );
    }

    public function testShouldGetConnection()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $repository    = m::mock('Zizaco\Confide\RepositoryInterface');
        $passService   = m::mock('Zizaco\Confide\EloquentPasswordService');
        $modelInstance = m::mock('Zizaco\Confide\ConfideUserInterface');

        $passService->shouldAllowMockingProtectedMethods();
        $passService->app['confide.repository'] = $repository;

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $passService->shouldReceive('getConnection')
            ->passthru();

        $repository->shouldReceive('model')
            ->once()->andReturn($modelInstance);

        $modelInstance->shouldReceive('getConnectionName')
            ->once()->andReturn('db_name');

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertEquals(
            'db_name',
            $passService->getConnection()
        );
    }

    public function testShouldGetTable()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $passService = m::mock('Zizaco\Confide\EloquentPasswordService');
        $config = m::mock('Config');

        $passService->shouldAllowMockingProtectedMethods();
        $passService->app['config'] = $config;

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $passService->shouldReceive('getTable')
            ->passthru();

        $config->shouldReceive('get')
            ->with('auth.reminder.table')
            ->times(3)->andReturnValues(['password_reminders', 'the_table', null]);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertEquals(
            'password_reminders',
            $passService->getTable()
        );
        $this->assertEquals(
            'the_table',
            $passService->getTable()
        );
        $this->assertNull(
            $passService->getTable()
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

        $mailer->shouldReceive('queueOn')
            ->once()->with('sync', 'view.name', ['user'=>$user, 'token'=>$token], m::any())
            ->andReturnUsing(function ($q, $a, $b, $closure) use ($test, $user) {
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

        $config->shouldReceive('get')
            ->once()->with('confide::email_queue')
            ->andReturn('sync');

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $passService->sendEmail($user, $token);
    }

    public function testShouldGetOldestValidDate()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $oldestValidDate = '2014-07-16 22:20:26';
        $carbon      = m::mock('Carbon\Carbon');
        $config      = m::mock('Config');
        $passService = m::mock('Zizaco\Confide\EloquentPasswordService');

        $passService->shouldAllowMockingProtectedMethods();
        $passService->app['Carbon\Carbon'] = $carbon;
        $passService->app['config']        = $config;

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $passService->shouldReceive('getOldestValidDate')
            ->passthru();

        $config->shouldReceive('get')
            ->once()->with('confide::confide.password_reset_expiration', 7)
            ->andReturn(14);

        $carbon->shouldReceive('now')
            ->once()->andReturn($carbon);

        $carbon->shouldReceive('subHours')
            ->once()->with(14)
            ->andReturn($carbon);

        $carbon->shouldReceive('toDateTimeString')
            ->once()->andReturn($oldestValidDate);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertEquals(
            $oldestValidDate,
            $passService->getOldestValidDate()
        );
    }
}
