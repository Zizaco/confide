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
            '[validatePassword,validateIsUnique,validateFields]'
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

        $validator->shouldReceive('validateIsUnique')
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

    public function testShouldValidateIsUnique()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $repo = m::mock('Zizaco\Confide\EloquentRepository');

        $validator = new UserValidator;
        $validator->repo = $repo;

        $userA = m::mock('Zizaco\Confide\ConfideUserInterface');
        $userA->id = 1;
        $userA->email = 'zizaco@gmail.com';
        $userA->username = 'zizaco';

        $userB = m::mock('Zizaco\Confide\ConfideUserInterface');
        $userB->id = '2';
        $userB->email = 'foo@bar.com';
        $userB->username = 'foo';

        $userC = m::mock('Zizaco\Confide\ConfideUserInterface');
        $userC->id = '3';
        $userC->email = 'something@somewhere.com';
        $userC->username = 'something';

        $userD = m::mock('Zizaco\Confide\ConfideUserInterface');
        $userD->id = ''; // No id
        $userD->email = 'something@somewhere.com'; // Duplicated email
        $userD->username = 'something';

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $userA->shouldReceive('getKey')
            ->andReturn($userA->id);

        $userB->shouldReceive('getKey')
            ->andReturn($userB->id);

        $userC->shouldReceive('getKey')
            ->andReturn($userC->id);

        $userD->shouldReceive('getKey')
            ->andReturn($userD->id);

        $repo->shouldReceive('getUserByIdentity')
            ->andReturnUsing(function($user) use ($userB, $userC) {
                if ($user['email'] == $userB->email) return $userB;
                if ($user['email'] == $userC->email) return $userC;
            });

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertTrue($validator->validateIsUnique($userA));
        $this->assertTrue($validator->validateIsUnique($userB));
        $this->assertTrue($validator->validateIsUnique($userC));
        $this->assertFalse($validator->validateIsUnique($userD));
    }
}
