<?php namespace Zizaco\Confide;

use Mockery as m;
use PHPUnit_Framework_TestCase;
use Zizaco\Confide\Facade as ConfideFacade;
use Illuminate\Support\Facades\App as App;
use Illuminate\Database\Eloquent\Model as Eloquent;

class ConfideUserTest extends PHPUnit_Framework_TestCase
{
    /**
     * Calls Mockery::close
     */
    public function tearDown()
    {
        m::close();
    }

    public function testShouldConfirm()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $user = new _ConfideUserStub;
        $user->confirmation_code = '12345';

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        ConfideFacade::shouldReceive('confirm')
            ->once()->with($user->confirmation_code)
            ->andReturn(true);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $user->confirm();
    }

    public function testShouldForgotPassword()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $user = new _ConfideUserStub;
        $user->email = 'someone@somewhere.com';

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        ConfideFacade::shouldReceive('forgotPassword')
            ->once()->with($user->email)
            ->andReturn(true);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $user->forgotPassword();
    }

    public function testIsValidOnNew()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $user = new _ConfideUserStub;
        $validator = m::mock('Zizaco\Confide\Validator');

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        App::shouldReceive('make')
            ->once()->with('confide.user_validator')
            ->andReturn($validator);

        $validator->shouldReceive('validate')
            ->once()->with($user)
            ->andReturn(true);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertTrue($user->isValid());
    }

    public function testIsValidOnExisting()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $user = new _ConfideUserStub;
        $user->exists = true;
        $validator = m::mock('Zizaco\Confide\Validator');

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        App::shouldReceive('make')
            ->once()->with('confide.user_validator')
            ->andReturn($validator);

        $validator->shouldReceive('validate')
            ->once()->with($user, 'update')
            ->andReturn(true);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertTrue($user->isValid());
    }

    public function testShouldValidateAndSave()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $user = m::mock('Zizaco\Confide\_ConfideUserStub[isValid,save,newQueryWithoutScopes]');

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $user->shouldReceive('save')
            ->once()
            ->passthru();

        $user->shouldReceive('isValid')
            ->once()
            ->andReturn(true);

        // Throw an exception instead of actually saving the object
        $user->shouldReceive('newQueryWithoutScopes')
            ->once()
            ->andReturnUsing(function () {
                throw new \Exception('Saved in database');
            });

        // Set the exception as expected ;)
        $this->setExpectedException(
            'Exception',
            'Saved in database'
        );

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $user->save();
    }

    public function testShouldNotSaveInvalid()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $user = m::mock('Zizaco\Confide\_ConfideUserStub[isValid,save,newQueryWithoutScopes]');

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $user->shouldReceive('save')
            ->once()
            ->passthru();

        $user->shouldReceive('isValid')
            ->once()
            ->andReturn(false); // If validation returns false

        // Throw an exception instead of actually saving the object
        $user->shouldReceive('newQueryWithoutScopes')
            ->never()
            ->andReturnUsing(function () {
                throw new \Exception('Saved in database');
            });

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertFalse($user->save());
    }

    public function testShouldGetErrors()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $user = new _ConfideUserStub;
        $newMessageBag = m::mock('Illuminate\Support\MessageBag');
        $existentMessageBag = m::mock('Illuminate\Support\MessageBag');
        $user->errors = $existentMessageBag;

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        App::shouldReceive('make')
            ->once()->with('Illuminate\Support\MessageBag')
            ->andReturn($newMessageBag);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertEquals($existentMessageBag, $user->errors());
        $user->errors = null;
        $this->assertEquals($newMessageBag, $user->errors());
    }

    public function testShouldGetAuthIdentifier()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $user = m::mock('Zizaco\Confide\_ConfideUserStub[getKey]');

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $user->shouldReceive('getKey')
            ->once()
            ->andReturn(1);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertEquals(1, $user->getAuthIdentifier());
    }

    public function testShouldGetAuthPassword()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $user = new _ConfideUserStub;
        $user->password = '1234';

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertEquals('1234', $user->getAuthPassword());
    }

    public function testShouldGetRememberToken()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $user = new _ConfideUserStub;
        $user->remember_token = '1234';

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertEquals('1234', $user->getRememberToken());
    }

    public function testShouldSetRememberToken()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $user = new _ConfideUserStub;
        $user->remember_token = null;

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $user->setRememberToken('123');
        $this->assertEquals('123', $user->remember_token);
    }

    public function testShouldGetRememberTokenName()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $user = new _ConfideUserStub;

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertEquals('remember_token', $user->getRememberTokenName());
    }

    public function testShouldGetReminderEmail()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $user = new _ConfideUserStub;
        $user->email = 'someone@somewhere.com';

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertEquals('someone@somewhere.com', $user->getReminderEmail());
    }
}

/**
 * A stub class that implements ConfideUserInterface and uses
 * the ConfideUser trait.
 *
 * @see \Zizaco\Confide\ConfideUser
 */
class _ConfideUserStub extends Eloquent implements ConfideUserInterface
{
    use ConfideUser;
}
