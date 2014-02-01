<?php namespace Zizaco\Confide;

use Mockery as m;
use PHPUnit_Framework_TestCase;

class ConfideTest extends PHPUnit_Framework_TestCase
{
    /**
     * Calls Mockery::close
     *
     * @return void
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
        $repo = m::mock('Zizaco\Confide\RepositoryInterface');
        $passService = m::mock('Zizaco\Confide\PasswordServiceInterface');
        $confide = new Confide($repo, $passService, $app);
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
        $repo = m::mock('Zizaco\Confide\RepositoryInterface');
        $passService = m::mock('Zizaco\Confide\PasswordServiceInterface');
        $confide = new Confide($repo, $passService, $app);
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
        $repo = m::mock('Zizaco\Confide\RepositoryInterface');
        $passService = m::mock('Zizaco\Confide\PasswordServiceInterface');
        $confide = new Confide($repo, $passService, $app);
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
        $repo = m::mock('Zizaco\Confide\RepositoryInterface');
        $passService = m::mock('Zizaco\Confide\PasswordServiceInterface');

        $confide = m::mock(
            'Zizaco\Confide\Confide'.
            '[extractRememberFromArray,extractIdentityFromArray]',
            [$repo, $passService, $app]
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
        $repo = m::mock('Zizaco\Confide\RepositoryInterface');
        $passService = m::mock('Zizaco\Confide\PasswordServiceInterface');

        $confide = m::mock(
            'Zizaco\Confide\Confide'.
            '[extractRememberFromArray,extractIdentityFromArray]',
            [$repo, $passService, $app]
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
        $repo = m::mock('Zizaco\Confide\RepositoryInterface');
        $passService = m::mock('Zizaco\Confide\PasswordServiceInterface');

        $confide = m::mock(
            'Zizaco\Confide\Confide'.
            '[extractRememberFromArray,extractIdentityFromArray]',
            [$repo, $passService, $app]
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
        $repo = m::mock('Zizaco\Confide\RepositoryInterface');
        $passService = m::mock('Zizaco\Confide\PasswordServiceInterface');

        $confide = m::mock(
            'Zizaco\Confide\Confide'.
            '[extractRememberFromArray,extractIdentityFromArray]',
            [$repo, $passService, $app]
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
        $repo = m::mock('Zizaco\Confide\RepositoryInterface');
        $passService = m::mock('Zizaco\Confide\PasswordServiceInterface');

        $confide = m::mock(
            'Zizaco\Confide\Confide'.
            '[extractRememberFromArray,extractIdentityFromArray]',
            [$repo, $passService, $app]
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
        $repo = m::mock('Zizaco\Confide\RepositoryInterface');
        $passService = m::mock('Zizaco\Confide\PasswordServiceInterface');

        $confide = m::mock(
            'Zizaco\Confide\Confide'.
            '[extractRememberFromArray]',
            [$repo, $passService, $app]
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
        $repo = m::mock('Zizaco\Confide\RepositoryInterface');
        $passService = m::mock('Zizaco\Confide\PasswordServiceInterface');

        $confide = m::mock(
            'Zizaco\Confide\Confide'.
            '[extractIdentityFromArray]',
            [$repo, $passService, $app]
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

    public function testShouldForgotPassword()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $app = [];
        $repo = m::mock('Zizaco\Confide\RepositoryInterface');
        $passService = m::mock('Zizaco\Confide\PasswordServiceInterface');
        $confide = new Confide($repo, $passService, $app);
        $user = m::mock('_mockedUser');
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
}
