<?php namespace Zizaco\Confide;

use Illuminate\Auth\UserInterface;

interface ConfideUserInterface extends UserInterface
{
    /**
     * Confirm the user (usually means that the user)
     * email is valid.
     *
     * @return bool
     */
    public function confirm();

    /**
     * Send email with information about password reset
     *
     * @return string
     */
    public function forgotPassword();

    /**
     * Checks if the current user is valid
     *
     * @return boolean
     */
    public function isValid();
}
