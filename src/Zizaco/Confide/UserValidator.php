<?php namespace Zizaco\Confide;

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
     * Validates the given user. Should check if all the fields are correctly
     * @param  ConfideUserInterface $user Instance to be tested
     * @return boolean                    True if the $user is valid
     */
    public function validate(ConfideUserInterface $user)
    {
        unset($user->password_confirmation);
        $user->password = \Hash::make($user->password);

        return true;
    }

}
