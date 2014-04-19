<?php namespace Zizaco\Confide;

use Mockery as m;
use PHPUnit_Framework_TestCase;
use Illuminate\Support\Facades\App as App;

class UserValidatorTest extends PHPUnit_Framework_TestCase
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

    public function testShouldValidate()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
       $repo = m::mock('Zizaco\Confide\EloquentRepository');

        App::shouldReceive('make')
            ->with('confide.repository')
            ->andReturn($repo);

        $validator = m::mock(
            'Zizaco\Confide\UserValidator'.
            '[validatePassword,isUnique,validateFields]'
        );
        $validator->shouldAllowMockingProtectedMethods();

        $user = m::mock('Zizaco\Confide\ConfideUserInterface');

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $validator->shouldReceive('validatePassword')
            ->once()->andReturn(true);

        $validator->shouldReceive('isUnique')
            ->once()->andReturn(true);

        $validator->shouldReceive('validateFields')
            ->once()->andReturn(true);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertTrue($validator->validate($user));
    }

    public function testShouldValidatePassword()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $repo = m::mock('Zizaco\Confide\EloquentRepository');
        $hash = m::mock('Hash');

        App::shouldReceive('make')
            ->with('confide.repository')
            ->andReturn($repo);

        App::shouldReceive('make')
            ->with('hash')
            ->andReturn($hash);

        $validator = new UserValidator;

        $userA = m::mock('Zizaco\Confide\ConfideUserInterface');
        $userA->password              = 'foo123';
        $userA->password_confirmation = 'foo123';

        $userB = m::mock('Zizaco\Confide\ConfideUserInterface');
        $userB->password              = 'foo123';
        $userB->password_confirmation = 'foo456';

        $userC = m::mock('Zizaco\Confide\ConfideUserInterface');
        $userC->password = '$2y$10$8PqTle4VGODMbjFbpIe.vOISth8qAaXlO7CAi4HNneqe37Jy1gGRO';

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $userA->shouldReceive('getOriginal')
            ->once()->with('password')
            ->andReturn('');

        $userB->shouldReceive('getOriginal')
            ->once()->with('password')
            ->andReturn('old_pass');

        $userC->shouldReceive('getOriginal')
            ->once()->with('password')
            ->andReturn('$2y$10$8PqTle4VGODMbjFbpIe.vOISth8qAaXlO7CAi4HNneqe37Jy1gGRO');

        $hash->shouldReceive('make')
            ->once()->with('foo123')
            ->andReturn('hashedPassword');

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertTrue($validator->validatePassword($userA));
        $this->assertFalse($validator->validatePassword($userB));
        $this->assertTrue($validator->validatePassword($userC));
    }
}
