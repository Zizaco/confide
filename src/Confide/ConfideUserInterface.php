<?php namespace Zizaco\Confide;

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

/**
 * Interface that declares the methods that must be
 * present in the User method that is going to be used
 * with Confide.
 * If you are looking for a implementation for this
 * methods see ConfideUser trait.
 *
 * @see \Zizaco\Confide\ConfideUser
 * @license MIT
 * @package Zizaco\Confide
 */
interface ConfideUserInterface extends UserInterface, RemindableInterface
{
    /**
     * Confirm the user (usually means that the user) email is valid.
     *
     * @return bool
     */
    public function confirm();

    /**
     * Send email with information about password reset.
     *
     * @return string
     */
    public function forgotPassword();

    /**
     * Checks if the current user is valid.
     *
     * @return bool
     */
    public function isValid();
}
