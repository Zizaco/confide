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
        $user = $model = m::mock('_mockedUser');

        $identity = [
            'email' => 'someone@somewhere.com',
            'something' => 'somevalue'
        ];

        $config = m::mock('Illuminate\Config\Repository');

        $app = compact('config');
        $repo = m::mock('Zizaco\Confide\EloquentRepository[getConstraintModelWithIdentity]', [$app])->shouldAllowMockingProtectedMethods();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $repo->shouldReceive('getConstraintModelWithIdentity')
            ->once()
            ->andReturn($model);

        $model->shouldReceive('first')
            ->once()
            ->andReturn($user);

        $config->shouldReceive('get')
                ->with('confide::optional_username')
                ->once()
                ->andReturn(true);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        // Should return the user
        $this->assertEquals($user, $repo->getUserByIdentity($identity));
    }

    public function testShouldGetConstraintModelWithIdentity()
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
        $repo = m::mock('Zizaco\Confide\EloquentRepository[model,getConstraintModelWithIdentity]', []);
        $repo->shouldAllowMockingProtectedMethods();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        // Repo model method should return the model instance
        $repo->shouldReceive('model')
            ->andReturn($model);

        // Should query for the user using each credential
        $model->shouldReceive('whereNested')->with(m::on(function($callback) use ($model)
        {
            $model->shouldReceive('where')->with('email', '=', 'someone@somewhere.com')->once()->andReturn($model);

            $model->shouldReceive('orWhere')->with('something', '=', 'somevalue')->once()->andReturn($model);

            $callback($model);

            return true;
        }))->once()->andReturn($model);

        $repo->shouldReceive('getConstraintModelWithIdentity')->passthru();
        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $rtn = $repo->getConstraintModelWithIdentity($identity);

        $this->assertEquals($model, $rtn);
    }

    public function testNotIncludeUsername()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $identity = [
            'email' => 'someone@somewhere.com',
            'username' => 'blahblah'
        ];

        $config = m::mock('Illuminate\Config\Repository');

        $app = compact('config');

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $repo = m::mock('Zizaco\Confide\EloquentRepository[shouldIncludeUsername]', [$app]);
        $repo->shouldAllowMockingProtectedMethods();

        $config->shouldReceive('get')
                ->with('confide::optional_username')
                ->once()
                ->andReturn(true);

        $repo->shouldReceive('shouldIncludeUsername')->passthru();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $identity = $repo->shouldIncludeUsername($identity);
        // Should return the user
        $this->assertTrue(!isset($identity['username']));
    }

    public function testIncludeUsername()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $identity = [
            'email' => 'someone@somewhere.com',
            'username' => 'blahblah'
        ];

        $config = m::mock('Illuminate\Config\Repository');

        $app = compact('config');

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $repo = m::mock('Zizaco\Confide\EloquentRepository[shouldIncludeUsername]', [$app]);
        $repo->shouldAllowMockingProtectedMethods();

        $config->shouldReceive('get')
                ->with('confide::optional_username')
                ->once()
                ->andReturn(false);

        $repo->shouldReceive('shouldIncludeUsername')->passthru();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $identity = $repo->shouldIncludeUsername($identity);
        // Should return the user
        $this->assertTrue(isset($identity['username']));
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
