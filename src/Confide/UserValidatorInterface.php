<?php namespace Zizaco\Confide;

/**
 * Interface that declares the methods that must be
 * present in the UserValidator that is going to be used
 * by ConfideUser.
 *
 * @see \Zizaco\Confide\UserValidator
 * @license MIT
 * @package Zizaco\Confide
 */
interface UserValidatorInterface
{
    /**
     * Validates the given user. Should check if all the fields are correctly
     * and may check other stuff too, like if the user is unique.
     *
     * @param ConfideUserInterface $user Instance to be tested.
     *
     * @return bool True if the $user is valid.
     */
    public function validate(ConfideUserInterface $user);
}
