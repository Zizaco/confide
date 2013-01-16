<?php namespace Zizaco\Confide;

use Illuminate\Auth\UserInterface;
use Config;
use Mail;
use Hash;
use Lang;

class ConfideUser extends \Illuminate\Database\Eloquent\Model implements UserInterface {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array('password');

    /**
     * Laravel application
     * 
     * @var Illuminate\Foundation\Application
     */
    public static $_app;

    /**
     * Create a new ConfideUser instance.
     */
    public function __construct()
    {
        parent::__construct();

        if ( ! ConfideUser::$_app )
        {
            ConfideUser::$_app = Confide::app();
        }

        $this->table = ConfideUser::$_app['config']->get('auth.table');
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Confirm the user (usually means that the user)
     * email is valid.
     *
     * @return bool
     */
    public function confirm()
    {
        $this->confirmed = true;
        return $this->save();
    }

    /**
     * Reset user password and sends in user e-mail
     *
     * @return string
     */
    public function resetPassword()
    {
        $new_password = substr(md5(microtime().ConfideUser::$_app['config']->get('app.key')),-9);
        $this->password = ConfideUser::$_app['hash']->make($new_password);
        if ( $this->save() )
        {
            ConfideUser::$_app['mail']->send(
                'confide::emails.passwordreset',
                ['user' => $this, 'new_password' => $new_password],
                function($m){
                    $m->to( $this->email )
                    ->subject( Lang::get('confide::confide.email.password_reset.subject') );
                }
            );

            return true;
        }
        else{
            return false;
        }
    }

    /**
     * Saves the user. Generate a confirmation code if
     * is a new user.
     *
     * @return bool
     */
    public function save()
    {
        if ( empty($this->id) )
        {
            $this->confirmation_code = md5(microtime().ConfideUser::$_app['config']->get('app.key'));
        }

        if ( $this->real_save() )
        {
            ConfideUser::$_app['mail']->send('confide::emails.confirm', ['user' => $this], function($m)
            {
                $m->to( $this->email )
                ->subject( Lang::get('confide::confide.email.account_confirmation.subject') );
            });

            return true;
        }
    }

    /**
     * Runs the real eloquent save method or returns
     * true if it's under testing. Because eloquent
     * save method is not Confide's responsibility.
     *
     * @return bool
     */
    private function real_save()
    {
        if ( isset($GLOBALS['_phpunit_confide_test']) )
        {
            return true;
        }
        else{
            return parent::save($this);
        }
    }

}
