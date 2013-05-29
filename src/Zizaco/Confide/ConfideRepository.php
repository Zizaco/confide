<?php namespace Zizaco\Confide;

class ConfideRepository
{
    /**
     * Laravel application
     * 
     * @var Illuminate\Foundation\Application
     */
    public $app;

    /**
     * Name of the model that should be used to retrieve your users.
     * You may specify an specific object. Then that object will be
     * returned when calling `model()` method.
     * 
     * @var string
     */
    public $model;

    /**
     * Create a new ConfideRepository
     *
     * @return void
     */
    public function __construct()
    {
        $this->app = app();
    }

    /**
     * Returns the model set in auth config
     *
     * @return mixed Instantiated object of the 'auth.model' class
     */
    public function model()
    {
        if (! $this->model)
        {
            $this->model = $this->app['config']->get('auth.model');
        }

        if(is_object($this->model))
        {
            return $this->model;
        }
        elseif(is_string($this->model))
        {
            return new $this->model;
        }

        throw new \Exception("Model not specified in config/auth.php", 639);
    }

    /**
     * Set the user confirmation to true.
     *
     * @param string $code
     * @return bool
     */
    public function confirm( $code )
    {
        $user = $this->model()->where('confirmation_code', '=', $code)->get()->first();
        if( $user )
        {
            return $user->confirm();
        }
        else
        {
            return false;
        }
    }

    /**
     * Find a user by the given email
     * 
     * @param  string $email The email to be used in the query
     * @return object        User object
     */
    public function getUserByMail( $email )
    {
        $user = $this->model()->where('email', '=', $email)->get()->first();

        return $user;
    }

    /**
     * Get password reminders count by the given token
     * 
     * @param  string $token
     * @return int    Password reminders count
     */
    public function getPasswordRemindersCount( $token )
    {
        $count = $this->app['db']->connection()->table('password_reminders')
            ->where('token','=',$token)->count();

        return $count;
    }

    /**
     * Get email of password reminder by the given token
     * 
     * @param  string $token
     * @return string Email
     */
    public function getEmailByReminderToken( $token )
    {
        $email = $this->app['db']->connection()->table('password_reminders')
            ->select('email')->where('token','=',$token)
            ->first();

        if ($email && is_object($email))
            $email = $email->email;

        return $email;
    }

    /**
     * Remove password reminder from database by the given token
     * 
     * @param  string $token
     * @return void
     */
    public function deleteEmailByReminderToken( $token )
    {
        $email = $this->app['db']->connection()->table('password_reminders')
            ->select('email')->where('token','=',$token)
            ->delete();
    }
}
