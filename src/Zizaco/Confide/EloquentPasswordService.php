<?php namespace Zizaco\Confide;

use Illuminate\Auth\Reminders\RemindableInterface;

/**
 * A service that abstracts all user password management related methods
 *
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
            ->connection()
            ->table('password_reminders')
            ->insert($values);

        return $token;
    }

    public function getEmailByToken($token)
    {
        $email = $this->app['db']
            ->connection()
            ->table('password_reminders')
            ->select('email')->where('token','=',$token)
            ->first();

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
     * Generates a random password change token
     *
     * @return  string
     */
    protected function generateToken()
    {
        return md5(uniqid(mt_rand(), true));
    }
}
