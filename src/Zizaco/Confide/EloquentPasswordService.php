<?php namespace Zizaco\Confide;

use Illuminate\Auth\Reminders\RemindableInterface;

/**
 * A service that abstracts all user password management related methods
 *
 * @license MIT
 * @package  Zizaco\Confide
 */
class EloquentPasswordService implements PasswordServiceInterface
{
    /**
     * Laravel application
     *
     * @var \Illuminate\Foundation\Application
     */
    public $app;

    /**
     * Create a new PasswordService
     *
     * @param  \Illuminate\Foundation\Application $app Laravel application object
     * @return void
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
     * @param  RemindableInterface $user     An existent user
     * @return string Password reset token
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

        $this->app['db']
            ->connection($user->connection)
            ->table('password_reminders')
            ->insert($values);

        $this->sendEmail($user, $token);

        return $token;
    }

    /**
     * Returns the email associated with the given reset
     * password token
     * @param  string $token
     * @return string Email
     */
    public function getEmailByToken($token)
    {
        $connection = $this->app['confide.repository']
            ->model()->connection;

        $email = $this->app['db']
            ->connection($connection)
            ->table('password_reminders')
            ->select('email')->where('token','=',$token)
            ->first();

        $email = $this->unwrapEmail($email);

        return $email;
    }

    /**
     * Generates a random password change token
     *
     * @return  string
     */
    protected function generateToken()
    {
        return md5(uniqid(mt_rand(), true));
    }

    /**
     * Extracts the email of the given object or array
     * @param  mixed $email An object, array or email string
     * @return string       The email address
     */
    protected function unwrapEmail($email)
    {
        if ($email && is_object($email))
        {
            $email = $email->email;
        }
        elseif ($email && is_array($email))
        {
            $email = $email['email'];
        }

        return $email;
    }

    /**
     * Sends an email containing the reset password link with the given token to
     * the user
     * @param  RemindableInterface $user  An existent user
     * @param  string $token  Password reset token
     * @return void
     */
    protected function sendEmail($user, $token)
    {
        $config = $this->app['config'];
        $lang   = $this->app['translator'];

        $this->app['mailer']->send($config->get('confide::email_reset_password'), compact('user', 'token'), function($message) use ($user, $token, $lang) {
            $message
                ->to($user->email, $user->username)
                ->subject($lang->get('confide::confide.email.password_reset.subject'));
        });
    }
}
