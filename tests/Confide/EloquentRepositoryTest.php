<?php namespace Zizaco\Confide;

use Mockery as m;
use PHPUnit_Framework_TestCase;

class EloquentRepositoryTest extends PHPUnit_Framework_TestCase
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
        $modelClassName = '_mockedUser';
        $user = m::mock($modelClassName);
        $repo = new EloquentRepository([]);
        $repo->app['config'] = m::mock('Illuminate\Config\Repository');

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        // Make sure to return the wanted value from config
        $repo->app['config']->shouldReceive('get')
            ->with('auth.model')
            ->once()
            ->andReturn($modelClassName);

        // When requesting the _mockedUser in the IoC, return
        // the correct object.
        $repo->app[$modelClassName] = $user;

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertEquals($user, $repo->model());
    }

    public function testShouldThrowExceptionIfCannotGetModel()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $repo = new EloquentRepository([]);
        $repo->app['config'] = m::mock('Illuminate\Config\Repository');

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        // Make sure to return the wanted value from config
        $repo->app['config']->shouldReceive('get')
            ->with('auth.model')
            ->once()
            ->andReturn(null);

        // Set the exception as expected ;)
        $this->setExpectedException(
            'Exception',
            'Wrong model specified in config/auth.php'
        );

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $repo->model();
    }

    public function testShouldGetUserByIdentity()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $identity = [
            'email' => 'someone@somewhere.com',
            'something' => 'somevalue'
        ];
        $model = m::mock('_mockedUser');
        $user = m::mock('_mockedUser');
        $nestedModel = m::mock('_mockedUser');
        $repo = m::mock('Zizaco\Confide\EloquentRepository[model]', []);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        // Repo model method should return the model instance
        $repo->shouldReceive('model')
            ->andReturn($model);

        // Should query for the user using each credential inside closure
        foreach($identity as $attribute => $value) {
            $nestedModel->shouldReceive('orWhere')
                ->with($attribute, '=', $value)
                ->once()
                ->andReturn($nestedModel);
        }

        // Should call with nested closure containing the above orWhere calls.
        $model->shouldReceive('where')
            ->with(m::on(function($arg) use ($nestedModel) {
                if (!is_callable($arg)) return false;
                $arg($nestedModel);
                return true;
            }))
            ->once()
            ->andReturn($model);

        $model->shouldReceive('get')
            ->once()
            ->andReturn($model);

        $model->shouldReceive('first')
            ->once()
            ->andReturn($user);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        // Should return the user
        $this->assertEquals($user, $repo->getUserByIdentity($identity));
    }

    public function testShouldGetUserByEmail()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $email = 'someone@somewhere.com';
        $user = m::mock('_mockedUser');
        $repo = m::mock('Zizaco\Confide\EloquentRepository[getUserByIdentity]', []);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        // Repo model method should return the model instance
        $repo->shouldReceive('getUserByIdentity')
            ->with(['email'=>$email])
            ->andReturn($user);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        // Should return the user
        $this->assertEquals($user, $repo->getUserByEmail($email));
    }

    public function testShouldGetUserByEmailOrUsername()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $username = 'Someone';
        $user = m::mock('_mockedUser');
        $repo = m::mock('Zizaco\Confide\EloquentRepository[getUserByIdentity]', []);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        // Repo model method should return the model instance
        $repo->shouldReceive('getUserByIdentity')
            ->with(['email'=>$username, 'username'=>$username])
            ->andReturn($user);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        // Should return the user
        $this->assertEquals($user, $repo->getUserByEmailOrUsername($username));
    }

    public function testShouldConfirmByCode()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $confirmCode = 123123;
        $wrongConfirmCode = 'IdontExist';
        $user = m::mock('_mockedUser');
        $repo = m::mock('Zizaco\Confide\EloquentRepository[getUserByIdentity,confirmUser]', []);
        $repo->shouldAllowMockingProtectedMethods();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        // Should query for the user
        $repo->shouldReceive('getUserByIdentity')
            ->with(['confirmation_code' => $confirmCode])
            ->andReturn($user);

        // Return null if no user can be found
        $repo->shouldReceive('getUserByIdentity')
            ->with(['confirmation_code' => $wrongConfirmCode])
            ->andReturn(null);

        // Should call the confirmUser method with the user
        // instance
        $repo->shouldReceive('confirmUser')
            ->with($user)
            ->once()
            ->andReturn(true);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertTrue($repo->confirmByCode($confirmCode));
        $this->assertFalse($repo->confirmByCode($wrongConfirmCode));
    }

    public function testShouldConfirmUser()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $user = m::mock('_mockedUser');
        $repo = m::mock('Zizaco\Confide\EloquentRepository[confirmUser]', []);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $user->shouldReceive('save')
            ->once()
            ->andReturn(true);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertTrue($repo->confirmUser($user));
    }
}
