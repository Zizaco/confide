<?php namespace Zizaco\Confide;

use Illuminate\View\Environment;
use Illuminate\Config\Repository;
use Zizaco\Confide\ConfideUser;
use View;
use DB;
use Auth;
use Hash;

class Confide
{
    /**
     * Confide Vesion
     */
    const VERSION = '0.3';

    /**
     * Illuminate view environment.
     * 
     * @var Illuminate\View\Environment
     */
    public $_view;

    /**
     * Illuminate config repository.
     * 
     * @var Illuminate\Config\Repository
     */
    public $_config;

    /**
     * Illuminate database manager.
     * 
     * @var Illuminate\Database\DatabaseManager 
     */
    public $_database;

    /**
     * Create a new confide instance.
     * 
     * @param  Illuminate\View\Environment  $view
     * @param  Illuminate\Config\Repository  $config
     * @param Illuminate\Database\DatabaseManager  $database
     * @return void
     */
    public function __construct($view, $config, $database)
    {
        $this->_view = $view;
        $this->_config = $config;
        $this->_database = $database;
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
     * Returns the model used for authent
     *
     * @return string
     */
    public function model()
    {
        $model = $this->_config->get('auth.model');

        return new $model;
    }

    /**
     * Get the currently authenticated user or null.
     *
     * @return Zizaco\Confide\ConfideUser|null
     */
    public function user()
    {
        return Auth::user();
    }

    /**
     * Set the user confirmation to true.
     *
     * @param string  $code
     * @return bool
     */
    public function confirm( $code )
    {
        $user = ConfideUser::where('confirmation_code', '=', $code)->get()->first();
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

        if ( ! is_null($user) and Hash::check($credentials['password'], $user->password) )
        {
            Auth::login( 
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
        $user = ConfideUser::where('email', '=', $email)->get()->first();
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
        Auth::logout();
    }

    /**
     * Display the default login view
     *
     * @return Illuminate\View\View
     */
    public function makeLoginForm()
    {
        return $this->_view->make('confide::login');
    }

    /**
     * Display the default signup view
     *
     * @return Illuminate\View\View
     */
    public function makeSignupForm()
    {
        return $this->_view->make('confide::signup');
    }

    /**
     * Display the forget password view
     *
     * @return Illuminate\View\View
     */
    public function makeForgetPasswordForm()
    {
        return $this->_view->make('confide::forgot_password');
    }
}
