<?php namespace Zizaco\Confide;

use Illuminate\Auth\Reminders\RemindableInterface;

/**
 * A service that abstracts all user password management related methods
 *
 * @package  Zizaco\Confide
 */
interface PasswordServiceInterface
{
    /**
     * Generate a token for password change and saves it in
     * the 'password_reminders' table with the email of the
     * user.
     *
     * @param  RemindableInterface $user     An existent user
     * @return string Password reset token
     */
    public function requestChangePassword(RemindableInterface $user);

    /**
     * Returns the email associated with the given reset
     * password token
     * @param  string $token
     * @return string Email
     */
    public function getEmailByToken($token);
}
