<?php namespace Zizaco\Confide;

use Mockery as m;
use PHPUnit_Framework_TestCase;

class EloquentRepositoryTest extends PHPUnit_Framework_TestCase {

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

    public function testGetModel()
    {
        /**
         * Set
         */
        $modelClassName = '_mockedUser';
        $user = m::mock($modelClassName);
        $repo = new EloquentRepository([]);
        $repo->app['config'] = m::mock('Illuminate\Config\Repository');

        /**
         * Expectation
         */

        // Make sure to return the wanted value from config
        $repo->app['config']->shouldReceive('get')
            ->with('auth.model')
            ->once()
            ->andReturn($modelClassName);

        // When requesting the _mockedUser in the IoC, return
        // the correct object.
        $repo->app[$modelClassName] = $user;

        /**
         * Assertion
         */
        $this->assertEquals($user, $repo->model());
    }

}
