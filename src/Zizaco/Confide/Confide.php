<?php namespace Zizaco\Confide;

use Illuminate\View\Environment;
use Illuminate\Config\Repository;
use ObjectProvier;

class Confide
{
    /**
     * Confide Vesion
     */
    const VERSION = '0.4';

    /**
     * Laravel application
     * 
     * @var Illuminate\Foundation\Application
     */
    public $_app;

    /**
     * Object provider
     * 
     * @var Zizaco\Confide\ObjectProvider
     */
    public $_obj_provider;

    /**
     * Create a new confide instance.
     * 
     * @param  Illuminate\Foundation\Application  $app
     * @return void
     */
    public function __construct($app)
    {
        $this->_app = $app;
        $this->_obj_provider = new ObjectProvider;
    }

    /**
     * Returns the current version
     *
     * @return string
     */
    public function version()
    {
        return 'Confide v'.Confide::VERSION;
    }

    /**
     * Returns the Laravel application
     * 
     * @return Illuminate\Foundation\Application
     */
    public function app()
    {
        return $this->_app;
    }

    /**
     * Returns the model set in auth config
     *
     * @return string
     */
    public function model()
    {
        $model = $this->_app['config']->get('auth.model');

        return $this->_obj_provider->getObject( $model );

    }

    /**
     * Get the currently authenticated user or null.
     *
     * @return Zizaco\Confide\ConfideUser|null
     */
    public function user()
    {
        return $this->_app['auth']->user();
    }

    /**
     * Set the user confirmation to true.
     *
     * @param string $code
     * @return bool
     */
    public function confirm( $code )
    {
        $user = Confide::model()->where('confirmation_code', '=', $code)->get()->first();
        if( $user )
        {
            $user->confirm();
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Attempt to log a user into the application with
     * password and username or email.
     *
     * @param  array $arguments
     * @return void
     */
    public function logAttempt( $credentials )
    {
        $user = $this->model()
            ->where('email','=',$credentials['email'])
            ->orWhere('username','=',$credentials['email'])
            ->first();

        if ( ! is_null($user) and $this->_app['hash']->check($credentials['password'], $user->password) )
        {
            $this->_app['auth']->login( 
                $user,
                isset($credentials['remember']) ? $credentials['remember'] : false 
            );

            return true;
        }
    }

    /**
     * Reset the user password and send email to user
     *
     * @param string  $email
     * @return bool
     */
    public function resetPassword( $email )
    {
        $user = Confide::model()->where('email', '=', $email)->get()->first();
        if( $user )
        {
            $user->resetPassword();
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Log the user out of the application.
     *
     * @return void
     */
    public function logout()
    {
        $this->_app['auth']->logout();
    }

    /**
     * Display the default login view
     *
     * @return Illuminate\View\View
     */
    public function makeLoginForm()
    {
        return $this->_app['view']->make('confide::login');
    }

    /**
     * Display the default signup view
     *
     * @return Illuminate\View\View
     */
    public function makeSignupForm()
    {
        return $this->_app['view']->make('confide::signup');
    }

    /**
     * Display the forget password view
     *
     * @return Illuminate\View\View
     */
    public function makeForgetPasswordForm()
    {
        return $this->_app['view']->make('confide::forgot_password');
    }
}
