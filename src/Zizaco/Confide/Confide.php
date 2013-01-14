<?php namespace Zizaco\Confide;

use Illuminate\View\Environment;
use Illuminate\Config\Repository;
use Zizaco\Confide\ConfideUser;
use View;
use DB;

class Confide
{
    /**
     * Confide Vesion
     */
    const VERSION = '0.2';

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
    public function Version()
    {
        return 'Confide v'.Confide::VERSION;
    }

    /**
     * Returns the model used for authent
     *
     * @return string
     */
    public function Model()
    {
        return $this->_database->table( $this->_config->get('auth.table') );
    }

    /**
     * Set the user confirmation to true.
     *
     * @param string  $code
     * @return bool
     */
    public function Confirm( $code )
    {
        $user = ConfideUser::where('confirmation_code', '=', $code)->get()->first();
        if( $user )
        {
            $user->Confirm();
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Reset the user password and send email to user
     *
     * @param string  $email
     * @return bool
     */
    public function ResetPassword( $email )
    {
        $user = ConfideUser::where('email', '=', $email)->get()->first();
        if( $user )
        {
            $user->ResetPassword();
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Display the default login view
     *
     * @return Illuminate\View\View
     */
    public function MakeLoginForm()
    {
        return $this->_view->make('confide::login');
    }

    /**
     * Display the default signup view
     *
     * @return Illuminate\View\View
     */
    public function MakeSignupForm()
    {
        return $this->_view->make('confide::signup');
    }

    /**
     * Display the forget password view
     *
     * @return Illuminate\View\View
     */
    public function MakeForgetPasswordForm()
    {
        return $this->_view->make('confide::forgot_password');
    }
}
