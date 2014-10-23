<?php namespace Zizaco\Confide;

/**
 * A service that abstracts all database interactions that happens
 * in Confide using Eloquent.
 *
 * @license MIT
 * @package Zizaco\Confide
 */
class EloquentRepository implements RepositoryInterface
{
    /**
     * Laravel application.
     *
     * @var \Illuminate\Foundation\Application
     */
    public $app;

    /**
     * Name of the model that should be used to retrieve your users.
     * You may specify an specific object. Then that object will be
     * returned when calling `model()` method.
     *
     * @var string
     */
    public $model;

    /**
     * Create a new ConfideRepository
     *
     * @param \Illuminate\Foundation\Application $app Laravel application object
     */
    public function __construct($app = null)
    {
        $this->app = $app ?: app();
    }

    /**
     * Returns the model set in auth config
     *
     * @return mixed Instantiated object of the 'auth.model' class
     */
    public function model()
    {
        if (! $this->model) {
            $this->model = $this->app['config']->get('auth.model');
        }

        if ($this->model) {
            return $this->app[$this->model];
        }

        throw new \Exception("Wrong model specified in config/auth.php", 639);
    }

    /**
     * Find a user by one of the fields given as $identity.
     * If one of the fields in the $identity array matches the user
     * will be retrieved.
     *
     * @param array $identity An array of attributes and values to search for
     *
     * @return ConfideUser User object
     */
    public function getUserByIdentity(array $identity)
    {

        $identity = $this->shouldIncludeUsername($identity);

        $user = $this->getConstraintModelWithIdentity($identity);

        return $user->first();

    }

    protected function getConstraintModelWithIdentity(array $identity)
    {
        $user = $this->model();

        $firstWhere = true;

        $user = $user->whereNested(function($query) use ($identity, $firstWhere) {

            foreach ($identity as $attribute => $value) {

                if ($firstWhere) {
                    $query = $query->where($attribute, '=', $value);
                } else {
                    $query = $query->orWhere($attribute, '=', $value);
                }

                $firstWhere = false;
            }

        });

        return $user;
    }

    /**
     * Unset username field if optional username is on
     * @param  array $identity
     * @return mixed
     */
    protected function shouldIncludeUsername(array $identity)
    {
        if (true === $this->app['config']->get('confide::optional_username'))
            unset($identity['username']);

        return $identity;
    }

    /**
     * Find a user by the given email
     *
     * @param string $email The email to be used in the query
     *
     * @return ConfideUser User object
     */
    public function getUserByEmail($email)
    {
        return $this->getUserByIdentity(['email'=>$email]);
    }

    /**
     * Find a user by the given email or username
     *
     * @param string $emailOrUsername Username of email to be used in the query
     *
     * @return ConfideUser User object
     */
    public function getUserByEmailOrUsername($emailOrUsername)
    {
        $identity = [
            'email' => $emailOrUsername,
            'username' => $emailOrUsername
        ];

        return $this->getUserByIdentity($identity);
    }

    /**
     * Update the confirmation status of a user to true if a user
     * is found with the given confirmation code.
     *
     * @param string $code
     *
     * @return bool Success
     */
    public function confirmByCode($code)
    {
        $identity = ['confirmation_code' => $code];

        $user = $this->getUserByIdentity($identity);

        if ($user) {
            return $this->confirmUser($user);
        } else {
            return false;
        }
    }

    /**
     * Updated the given user in the database. Set the 'confirmed' attribute to
     * true.
     *
     * @param  ConfideUser User object
     *
     * @return bool Success
     */
    protected function confirmUser($user)
    {
        $user->confirmed = true;

        return $user->save();
    }
}
