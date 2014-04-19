<?php namespace Zizaco\Confide;

use Illuminate\Support\Facades\App as App;

/**
 * This is the default validator used by ConfideUser. You may overwrite this
 * class and implement your own validator by creating a class that implements
 * the `UserValidatorInterface` and by registering that class in IoC container
 * as 'confide.user_validator'.
 *
 * This validator will look for the basic fields (username, email,
 * password), and if the user is unique.
 *
 * In order to use a custom validator:
 *     // MyOwnValidator.php
 *     class MyOwnValidator implements UserValidatorInterface {
 *         ...
 *     }
 *
 *     // routes.php
 *     ...
 *     App::bind('confide.user_validator', 'MyOwnValidator');
 *
 * @see \Zizaco\Confide\UserValidator
 * @license MIT
 * @package  Zizaco\Confide
 */
class UserValidator implements UserValidatorInterface {

    /**
     * Confide repository instance
     *
     * @var \Zizaco\Confide\RepositoryInterface
     */
    public $repo;

    /**
     * Validation rules for this Validator.
     *
     * @var array
     */
    protected $rules = [
        'create' => [
            'username' => 'required|alpha_dash',
            'email'    => 'required|email',
            'password' => 'required|min:4',
        ],
        'update' => [
            'username' => 'required|alpha_dash',
            'email'    => 'required|email',
            'password' => 'required|min:4',
        ]
    ];

    /**
     * Validates the given user. Should check if all the fields are correctly
     * @param  ConfideUserInterface $user Instance to be tested
     * @return boolean                    True if the $user is valid
     */
    public function validate(ConfideUserInterface $user, $ruleset = 'create')
    {
        // Set the $repo as a ConfideRepository object
        $this->repo = App::make('confide.repository');

        // Validate object
        $result = $this->validatePassword($user) &&
            $this->validateIsUnique($user) &&
            $this->validateFields($user);

        return $result;
    }

    /**
     * Validates the password and password_confirmation of the given
     * user
     * @param  ConfideUserInterface $user
     * @return boolean  True if password is valid
     */
    public function validatePassword(ConfideUserInterface $user)
    {
        $hash = App::make('hash');

        if($user->getOriginal('password') != $user->password) {
            if ($user->password == $user->password_confirmation) {

                // Hashes password and unset password_confirmation field
                $user->password = $hash->make($user->password);
                unset($user->password_confirmation);

                return true;
            } else {
                return false;
            }
        }

        return true;
    }

    public function validateIsUnique(ConfideUserInterface $user)
    {
        $identity = [
            'username' => $user->username,
            'email'    => $user->email,
        ];

        $similar = $this->repo->getUserByIdentity($identity);

        if (!$similar || $similar->getKey() == $user->getKey()) {
            return true;
        }

        return false;
    }
}
