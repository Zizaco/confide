<?php namespace Zizaco\Confide;

use Illuminate\Auth\Reminders\RemindableInterface;

/**
 * A service that abstracts all user password management related methods.
 *
 * @license MIT
 * @package Zizaco\Confide
 */
interface PasswordServiceInterface
{
    /**
     * Generate a token for password change and saves it in the
     * 'password_reminders' table with the email of the user.
     *
     * @param RemindableInterface $user An existent user.
     *
     * @return string Password reset token.
     */
    public function requestChangePassword(RemindableInterface $user);

    /**
     * Returns the email associated with the given reset password token.
     *
     * @param string $token
     *
     * @return string Email.
     */
    public function getEmailByToken($token);

    /**
     * Delete the record of the given token from database.
     *
     * @param string $token
     *
     * @return boolean Success.
     */
    public function destroyToken($token);
}
