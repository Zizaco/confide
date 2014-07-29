<?php namespace Zizaco\Confide;

use Mockery as m;
use PHPUnit_Framework_TestCase;

class ConfideTest extends PHPUnit_Framework_TestCase
{
    /**
     * Calls Mockery::close
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
        $repo           = m::mock('Zizaco\Confide\RepositoryInterface');
        $passService    = m::mock('Zizaco\Confide\PasswordServiceInterface');
        $loginThrottler = m::mock('Zizaco\Confide\LoginThrottleServiceInterface');
        $confide = new Confide($repo, $passService, $loginThrottler, $app);
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
        $repo           = m::mock('Zizaco\Confide\RepositoryInterface');
        $passService    = m::mock('Zizaco\Confide\PasswordServiceInterface');
        $loginThrottler = m::mock('Zizaco\Confide\LoginThrottleServiceInterface');
        $confide = new Confide($repo, $passService, $loginThrottler, $app);
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
        $repo           = m::mock('Zizaco\Confide\RepositoryInterface');
        $passService    = m::mock('Zizaco\Confide\PasswordServiceInterface');
        $loginThrottler = m::mock('Zizaco\Confide\LoginThrottleServiceInterface');
        $confide = new Confide($repo, $passService, $loginThrottler, $app);
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

    public function testShouldGetUserByEmailOrUsername()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $app = [];
        $repo           = m::mock('Zizaco\Confide\RepositoryInterface');
        $passService    = m::mock('Zizaco\Confide\PasswordServiceInterface');
        $loginThrottler = m::mock('Zizaco\Confide\LoginThrottleServiceInterface');
        $confide = m::mock(
            'Zizaco\Confide\Confide'.
            '[extractIdentityFromArray]',
            [$repo, $passService, $loginThrottler, $app]
        );
        $confide->shouldAllowMockingProtectedMethods();

        $identity = ['email'=>'johndoe@example.com'];
        $user     = m::mock('_mockedUser');

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $confide->shouldReceive('extractIdentityFromArray')
            ->once()->with($identity)
            ->andReturn($identity['email']);

        $repo->shouldReceive('getUserByEmailOrUsername')
            ->once()->with('johndoe@example.com')
            ->andReturn($user);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertEquals(
            $user,
            $confide->getUserByEmailOrUsername($identity)
        );
    }

    public function testShouldDoLogAttempt()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $app = [];
        $app['auth'] = m::mock('Auth');
        $app['hash'] = m::mock('Hash');
        $repo           = m::mock('Zizaco\Confide\RepositoryInterface');
        $passService    = m::mock('Zizaco\Confide\PasswordServiceInterface');
        $loginThrottler = m::mock('Zizaco\Confide\LoginThrottleServiceInterface');

        $confide = m::mock(
            'Zizaco\Confide\Confide'.
            '[extractRememberFromArray,extractIdentityFromArray,loginThrottling]',
            [$repo, $passService, $loginThrottler, $app]
        );
        $confide->shouldAllowMockingProtectedMethods();

        $user = m::mock('_mockedUser');
        $user->confirmed = true;
        $user->email = 'someone@somewhere.com';
        $user->password = 'secret';

        $remember = true;
        $input = [
            'email'=>$user->email,
            'password'=>$user->password,
            'remember'=>$remember
        ];

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $confide->shouldReceive('extractRememberFromArray')
            ->with($input)->andReturn(true);

        $confide->shouldReceive('extractIdentityFromArray')
            ->with($input)->andReturn($user->email);

        $confide->shouldReceive('loginThrottling')
            ->once()->with($user->email)
            ->andReturn(true);

        $repo->shouldReceive('getUserByEmailOrUsername')
            ->once()->with('someone@somewhere.com')
            ->andReturn($user);

        $app['hash']->shouldReceive('check')
            ->once()->with($user->password, $user->password)
            ->andReturn(true);

        $app['auth']->shouldReceive('login')
            ->once()->with($user, $remember)
            ->andReturn(true);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertTrue($confide->logAttempt($input));
    }

    public function testShouldFailLogAttemptIfThrottled()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $app = [];
        $app['auth'] = m::mock('Auth');
        $app['hash'] = m::mock('Hash');
        $repo           = m::mock('Zizaco\Confide\RepositoryInterface');
        $passService    = m::mock('Zizaco\Confide\PasswordServiceInterface');
        $loginThrottler = m::mock('Zizaco\Confide\LoginThrottleServiceInterface');

        $confide = m::mock(
            'Zizaco\Confide\Confide'.
            '[extractRememberFromArray,extractIdentityFromArray,loginThrottling]',
            [$repo, $passService, $loginThrottler, $app]
        );
        $confide->shouldAllowMockingProtectedMethods();

        $user = m::mock('_mockedUser');
        $user->confirmed = true;
        $user->email = 'someone@somewhere.com';
        $user->password = 'secret';

        $remember = true;
        $input = [
            'email'=>$user->email,
            'password'=>$user->password,
            'remember'=>$remember
        ];

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $confide->shouldReceive('extractRememberFromArray')
            ->with($input)->andReturn(true);

        $confide->shouldReceive('extractIdentityFromArray')
            ->with($input)->andReturn($user->email);

        $confide->shouldReceive('loginThrottling')
            ->once()->with($user->email)
            ->andReturn(false);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertFalse($confide->logAttempt($input));
    }

    public function testShouldFailLogAttemptIfUserNotFound()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $app = [];
        $app['auth'] = m::mock('Auth');
        $app['hash'] = m::mock('Hash');
        $repo           = m::mock('Zizaco\Confide\RepositoryInterface');
        $passService    = m::mock('Zizaco\Confide\PasswordServiceInterface');
        $loginThrottler = m::mock('Zizaco\Confide\LoginThrottleServiceInterface');

        $confide = m::mock(
            'Zizaco\Confide\Confide'.
            '[extractRememberFromArray,extractIdentityFromArray,loginThrottling]',
            [$repo, $passService, $loginThrottler, $app]
        );
        $confide->shouldAllowMockingProtectedMethods();

        $user = m::mock('_mockedUser');
        $user->confirmed = true;
        $user->email = 'someone@somewhere.com';
        $user->password = 'secret';

        $remember = true;
        $input = [
            'email'=>$user->email,
            'password'=>$user->password,
            'remember'=>$remember
        ];

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $confide->shouldReceive('extractRememberFromArray')
            ->with($input)->andReturn(true);

        $confide->shouldReceive('extractIdentityFromArray')
            ->with($input)->andReturn($user->email);

        $confide->shouldReceive('loginThrottling')
            ->once()->with($user->email)
            ->andReturn(true);

        $repo->shouldReceive('getUserByEmailOrUsername')
            ->once()->with('someone@somewhere.com')
            ->andReturn(false);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertFalse($confide->logAttempt($input));
    }

    public function testShouldFailLogAttemptIfUserNotConfirmed()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $app = [];
        $app['auth'] = m::mock('Auth');
        $app['hash'] = m::mock('Hash');
        $repo           = m::mock('Zizaco\Confide\RepositoryInterface');
        $passService    = m::mock('Zizaco\Confide\PasswordServiceInterface');
        $loginThrottler = m::mock('Zizaco\Confide\LoginThrottleServiceInterface');

        $confide = m::mock(
            'Zizaco\Confide\Confide'.
            '[extractRememberFromArray,extractIdentityFromArray,loginThrottling]',
            [$repo, $passService, $loginThrottler, $app]
        );
        $confide->shouldAllowMockingProtectedMethods();

        $user = m::mock('_mockedUser');
        $user->confirmed = false;
        $user->email = 'someone@somewhere.com';
        $user->password = 'secret';

        $remember = true;
        $input = [
            'email'=>$user->email,
            'password'=>$user->password,
            'remember'=>$remember
        ];

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $confide->shouldReceive('extractRememberFromArray')
            ->with($input)->andReturn(true);

        $confide->shouldReceive('extractIdentityFromArray')
            ->with($input)->andReturn($user->email);

        $confide->shouldReceive('loginThrottling')
            ->once()->with($user->email)
            ->andReturn(true);

        $repo->shouldReceive('getUserByEmailOrUsername')
            ->once()->with('someone@somewhere.com')
            ->andReturn($user);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertFalse($confide->logAttempt($input));
    }

    public function testShouldFailLogAttemptIfWrongPass()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $app = [];
        $app['auth'] = m::mock('Auth');
        $app['hash'] = m::mock('Hash');
        $repo           = m::mock('Zizaco\Confide\RepositoryInterface');
        $passService    = m::mock('Zizaco\Confide\PasswordServiceInterface');
        $loginThrottler = m::mock('Zizaco\Confide\LoginThrottleServiceInterface');

        $confide = m::mock(
            'Zizaco\Confide\Confide'.
            '[extractRememberFromArray,extractIdentityFromArray,loginThrottling]',
            [$repo, $passService, $loginThrottler, $app]
        );
        $confide->shouldAllowMockingProtectedMethods();

        $user = m::mock('_mockedUser');
        $user->confirmed = true;
        $user->email = 'someone@somewhere.com';
        $user->password = 'secret';

        $remember = true;
        $input = [
            'email'=>$user->email,
            'password'=>$user->password,
            'remember'=>$remember
        ];

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $confide->shouldReceive('extractRememberFromArray')
            ->with($input)->andReturn(true);

        $confide->shouldReceive('extractIdentityFromArray')
            ->with($input)->andReturn($user->email);

        $confide->shouldReceive('loginThrottling')
            ->once()->with($user->email)
            ->andReturn(true);

        $repo->shouldReceive('getUserByEmailOrUsername')
            ->once()->with('someone@somewhere.com')
            ->andReturn($user);

        $app['hash']->shouldReceive('check')
            ->once()->with($user->password, $user->password)
            ->andReturn(false);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertFalse($confide->logAttempt($input));
    }

    public function testShouldDoLogAttemptIfNotConfirmed()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $app = [];
        $app['auth'] = m::mock('Auth');
        $app['hash'] = m::mock('Hash');
        $repo           = m::mock('Zizaco\Confide\RepositoryInterface');
        $passService    = m::mock('Zizaco\Confide\PasswordServiceInterface');
        $loginThrottler = m::mock('Zizaco\Confide\LoginThrottleServiceInterface');

        $confide = m::mock(
            'Zizaco\Confide\Confide'.
            '[extractRememberFromArray,extractIdentityFromArray,loginThrottling]',
            [$repo, $passService, $loginThrottler, $app]
        );
        $confide->shouldAllowMockingProtectedMethods();

        $user = m::mock('_mockedUser');
        $user->confirmed = false;
        $user->email = 'someone@somewhere.com';
        $user->password = 'secret';

        $remember = true;
        $input = [
            'email'=>$user->email,
            'password'=>$user->password,
            'remember'=>$remember
        ];

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $confide->shouldReceive('extractRememberFromArray')
            ->with($input)->andReturn(true);

        $confide->shouldReceive('extractIdentityFromArray')
            ->with($input)->andReturn($user->email);

        $confide->shouldReceive('loginThrottling')
            ->once()->with($user->email)
            ->andReturn(true);

        $repo->shouldReceive('getUserByEmailOrUsername')
            ->once()->with('someone@somewhere.com')
            ->andReturn($user);

        $app['hash']->shouldReceive('check')
            ->once()->with($user->password, $user->password)
            ->andReturn(true);

        $app['auth']->shouldReceive('login')
            ->once()->with($user, $remember)
            ->andReturn(true);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertTrue($confide->logAttempt($input, false));
    }

    public function testShouldExtractRememberFromArray()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $app = [];
        $repo           = m::mock('Zizaco\Confide\RepositoryInterface');
        $passService    = m::mock('Zizaco\Confide\PasswordServiceInterface');
        $loginThrottler = m::mock('Zizaco\Confide\LoginThrottleServiceInterface');

        $confide = m::mock(
            'Zizaco\Confide\Confide'.
            '[extractRememberFromArray]',
            [$repo, $passService, $loginThrottler, $app]
        );
        $confide->shouldAllowMockingProtectedMethods();

        $inputWithoutRemember = [];
        $inputWithRemember = [
            'remember' => true
        ];

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $confide->shouldReceive('extractRememberFromArray')
            ->passthru();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertTrue(
            $confide->extractRememberFromArray($inputWithRemember)
        );
        $this->assertFalse(
            $confide->extractRememberFromArray($inputWithoutRemember)
        );
    }

    public function testShouldExtractIdentityFromArray()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $app = [];
        $repo           = m::mock('Zizaco\Confide\RepositoryInterface');
        $passService    = m::mock('Zizaco\Confide\PasswordServiceInterface');
        $loginThrottler = m::mock('Zizaco\Confide\LoginThrottleServiceInterface');

        $confide = m::mock(
            'Zizaco\Confide\Confide'.
            '[extractIdentityFromArray]',
            [$repo, $passService, $loginThrottler, $app]
        );
        $confide->shouldAllowMockingProtectedMethods();

        $emptyId = ['garbage'=>'dontNeed'];
        $userId  = ['username' => 'someone', 'garbage'=>'dontNeed'];
        $emailId = ['email' => 'someone@somewhere.com', 'garbage'=>'dontNeed'];
        $bothId  = ['email' => 'someone@somewhere.com', 'username' => 'someone', 'garbage'=>'dontNeed'];

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $confide->shouldReceive('extractIdentityFromArray')
            ->passthru();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertEquals(
            false,
            $confide->extractIdentityFromArray($emptyId)
        );
        $this->assertEquals(
            'someone',
            $confide->extractIdentityFromArray($userId)
        );
        $this->assertEquals(
            'someone@somewhere.com',
            $confide->extractIdentityFromArray($emailId)
        );
        $this->assertEquals(
            'someone@somewhere.com',
            $confide->extractIdentityFromArray($bothId)
        );
    }

    public function testShouldDoLoginThrottling()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $config = m::mock('Config');
        $app = ['config'=>$config];
        $repo           = m::mock('Zizaco\Confide\RepositoryInterface');
        $passService    = m::mock('Zizaco\Confide\PasswordServiceInterface');
        $loginThrottler = m::mock('Zizaco\Confide\LoginThrottleServiceInterface');

        $confide = m::mock(
            'Zizaco\Confide\Confide'.
            '[loginThrottling]',
            [$repo, $passService, $loginThrottler, $app]
        );
        $confide->shouldAllowMockingProtectedMethods();

        $userEmail = 'someone@somewhere.com';
        $throttledUserEmail  = 'hack@me.com';

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $confide->shouldReceive('loginThrottling')
            ->passthru();

        $config->shouldReceive('get')
            ->twice()->with('confide::throttle_limit')
            ->andReturn(19);

        $loginThrottler->shouldReceive('throttleIdentity')
            ->once()->with($userEmail)
            ->andReturn(0);

        $loginThrottler->shouldReceive('throttleIdentity')
            ->once()->with($throttledUserEmail)
            ->andReturn(20);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertTrue($confide->loginThrottling($userEmail));
        $this->assertFalse($confide->loginThrottling($throttledUserEmail));
    }

    public function testIsThrottled()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $app = [];
        $repo           = m::mock('Zizaco\Confide\RepositoryInterface');
        $passService    = m::mock('Zizaco\Confide\PasswordServiceInterface');
        $loginThrottler = m::mock('Zizaco\Confide\LoginThrottleServiceInterface');

        $confide = m::mock(
            'Zizaco\Confide\Confide[isThrottled]',
            [$repo, $passService, $loginThrottler, $app]
        );

        $userEmail = 'someone@somewhere.com';

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $confide->shouldReceive('isThrottled')
            ->passthru();

        $loginThrottler->shouldReceive('isThrottled')
            ->once()->with($userEmail)
            ->andReturn(true);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertTrue($confide->isThrottled($userEmail));
    }

    public function testShouldForgotPassword()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $app = [];
        $repo           = m::mock('Zizaco\Confide\RepositoryInterface');
        $passService    = m::mock('Zizaco\Confide\PasswordServiceInterface');
        $loginThrottler = m::mock('Zizaco\Confide\LoginThrottleServiceInterface');
        $confide = new Confide($repo, $passService, $loginThrottler, $app);

        $user = m::mock('Illuminate\Auth\Reminders\RemindableInterface');
        $user->email = 'someone@somewhere.com';
        $generatedToken = '12345';

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $repo->shouldReceive('getUserByEmail')
            ->once()->with($user->email)
            ->andReturn($user);

        $repo->shouldReceive('getUserByEmail')
            ->andReturn(false);

        $passService->shouldReceive('requestChangePassword')
            ->once()->with($user)
            ->andReturn($generatedToken);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertEquals(
            $generatedToken,
            $confide->forgotPassword($user->email)
        );

        $this->assertFalse($confide->forgotPassword('wrong@somewhere.com'));
    }

    public function testShouldDestroyForgotPasswordToken()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $app = [];
        $repo           = m::mock('Zizaco\Confide\RepositoryInterface');
        $passService    = m::mock('Zizaco\Confide\PasswordServiceInterface');
        $loginThrottler = m::mock('Zizaco\Confide\LoginThrottleServiceInterface');

        $confide = m::mock(
            'Zizaco\Confide\Confide[destroyForgotPasswordToken]',
            [$repo, $passService, $loginThrottler, $app]
        );

        $token = '123456789';

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $confide->shouldReceive('destroyForgotPasswordToken')
            ->passthru();

        $passService->shouldReceive('destroyToken')
            ->once()->with($token)
            ->andReturn(true);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertTrue($confide->destroyForgotPasswordToken($token));
    }

    public function testShouldGetUserByPasswordResetToken()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $app = [];
        $repo           = m::mock('Zizaco\Confide\RepositoryInterface');
        $passService    = m::mock('Zizaco\Confide\PasswordServiceInterface');
        $loginThrottler = m::mock('Zizaco\Confide\LoginThrottleServiceInterface');
        $confide = new Confide($repo, $passService, $loginThrottler, $app);

        $user = m::mock('_mockedUser');
        $user->email = 'someone@somewhere.com';
        $token = '12345';

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $passService->shouldReceive('getEmailByToken')
            ->once()->with($token)
            ->andReturn($user->email);

        $passService->shouldReceive('getEmailByToken')
            ->andReturn(false);

        $repo->shouldReceive('getUserByEmail')
            ->once()->with($user->email)
            ->andReturn($user);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertEquals(
            $user,
            $confide->userByResetPasswordToken($token)
        );

        $this->assertFalse(
            $confide->userByResetPasswordToken('wrong')
        );
    }

    public function testShouldDoLogout()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $app = [];
        $app['auth'] = m::mock('Auth');
        $repo           = m::mock('Zizaco\Confide\RepositoryInterface');
        $passService    = m::mock('Zizaco\Confide\PasswordServiceInterface');
        $loginThrottler = m::mock('Zizaco\Confide\LoginThrottleServiceInterface');
        $confide = new Confide($repo, $passService, $loginThrottler, $app);
        $user = m::mock('_mockedUser');

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $app['auth']->shouldReceive('logout')
            ->once()->andReturn(true);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertTrue($confide->logout());
    }

    public function testShouldMakeViews()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $app = [];
        $app['config'] = m::mock('Config');
        $app['view'] = m::mock('ViewEnv');
        $repo           = m::mock('Zizaco\Confide\RepositoryInterface');
        $passService    = m::mock('Zizaco\Confide\PasswordServiceInterface');
        $loginThrottler = m::mock('Zizaco\Confide\LoginThrottleServiceInterface');
        $confide = new Confide($repo, $passService, $loginThrottler, $app);
        $token = '12345';
        $view = m::mock('View');

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $app['view']->shouldReceive('make')
            ->once()->with('view.confide::login_form')
            ->andReturn($view);
        $app['view']->shouldReceive('make')
            ->once()->with('view.confide::signup_form')
            ->andReturn($view);
        $app['view']->shouldReceive('make')
            ->once()->with('view.confide::forgot_password_form')
            ->andReturn($view);
        $app['view']->shouldReceive('make')
            ->once()->with(
                'view.confide::reset_password_form',
                ['token'=>$token]
            )
            ->andReturn($view);

        $app['config']->shouldReceive('get')
            ->times(4)->andReturnUsing(function ($name) {
                return 'view.'.$name;
            });

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertEquals($view, $confide->makeLoginForm());
        $this->assertEquals($view, $confide->makeSignupForm());
        $this->assertEquals($view, $confide->makeForgotPasswordForm());
        $this->assertEquals($view, $confide->makeResetPasswordForm($token));
    }
}
