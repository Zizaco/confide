<?php namespace Zizaco\Confide;

use Illuminate\Auth\Reminders\RemindableInterface;

/**
 * A service that abstracts all user password management related methods.
 *
 * @license MIT
 * @package Zizaco\Confide
 */
class EloquentPasswordService implements PasswordServiceInterface
{
    /**
     * Laravel application.
     *
     * @var \Illuminate\Foundation\Application
     */
    public $app;

    /**
     * Create a new PasswordService.
     *
     * @param \Illuminate\Foundation\Application $app Laravel application object
     */
    public function __construct($app = null)
    {
        $this->app = $app ?: app();
    }

    /**
     * Generate a token for password change and saves it in
     * the 'password_reminders' table with the email of the
     * user.
     *
     * @param RemindableInterface $user An existent user.
     *
     * @return string Password reset token.
     */
    public function requestChangePassword(RemindableInterface $user)
    {
        $email = $user->getReminderEmail();
        $token = $this->generateToken();

        $values = array(
            'email'=> $email,
            'token'=> $token,
            'created_at'=> new \DateTime
        );

        $table = $this->getTable();

        $this->app['db']
            ->connection($user->getConnectionName())
            ->table($table)
            ->insert($values);

        $this->sendEmail($user, $token);

        return $token;
    }

    /**
     * Returns the email associated with the given reset
     * password token.
     *
     * @param string $token
     *
     * @return string Email.
     */
    public function getEmailByToken($token)
    {
        $connection = $this->getConnection();
        $table = $this->getTable();

        $email = $this->app['db']
            ->connection($connection)
            ->table($table)
            ->select('email')
            ->where('token', '=', $token)
            ->where('created_at', '>=', $this->getOldestValidDate())
            ->first();

        $email = $this->unwrapEmail($email);

        return $email;
    }

    /**
     * Delete the record of the given token from database.
     *
     * @param string $token
     *
     * @return boolean Success.
     */
    public function destroyToken($token)
    {
        $connection = $this->getConnection();
        $table = $this->getTable();

        $affected = $this->app['db']
            ->connection($connection)
            ->table($table)
            ->where('token', '=', $token)
            ->delete();

        return $affected > 0;
    }

    /**
     * Returns a possible custom connection that may has being used
     * for the user model. If null is returned by this method than
     * the default connection is going to be used.
     *
     * @return string Original $connection value of the user model.
     */
    protected function getConnection()
    {
        return $this->app['confide.repository']
            ->model()->getConnectionName();
    }

    /**
     * Returns the configured password reminders table.
     *
     * @return string Table name.
     */
    protected function getTable()
    {
        return $this->app['config']->get('auth.reminder.table');
    }

    /**
     * Generates a random password change token.
     *
     * @return string
     */
    protected function generateToken()
    {
        return md5(uniqid(mt_rand(), true));
    }

    /**
     * Extracts the email of the given object or array.
     *
     * @param mixed $email An object, array or email string.
     *
     * @return string The email address.
     */
    protected function unwrapEmail($email)
    {
        if ($email && is_object($email)) {
            $email = $email->email;
        } elseif ($email && is_array($email)) {
            $email = $email['email'];
        }

        return $email;
    }

    /**
     * Sends an email containing the reset password link with the
     * given token to the user.
     *
     * @param RemindableInterface $user  An existent user.
     * @param string              $token Password reset token.
     *
     * @return void
     */
    protected function sendEmail($user, $token)
    {
        $config = $this->app['config'];
        $lang   = $this->app['translator'];

        $this->app['mailer']->queueOn(
            $config->get('confide::email_queue'),
            $config->get('confide::email_reset_password'),
            compact('user', 'token'),
            function ($message) use ($user, $token, $lang) {
                $message
                    ->to($user->email, $user->username)
                    ->subject($lang->get('confide::confide.email.password_reset.subject'));
            }
        );
    }

    /**
     * Returns a date to limit the acceptable password reset requests.
     *
     * @return string 'Y-m-d H:i:s' formated string.
     */
    protected function getOldestValidDate()
    {
        // Instantiate a carbon object (that is a dependency of
        // 'illuminate/database')
        $carbon = $this->app['Carbon\Carbon'];
        $config = $this->app['config'];

        return $carbon->now()
            ->subHours($config->get('confide::confide.password_reset_expiration', 7))
            ->toDateTimeString();
    }
}
