<?php namespace Zizaco\Confide;

use Illuminate\Auth\UserInterface;
use LaravelBook\Ardent\Ardent;

class ConfideUser extends Ardent implements UserInterface {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * Laravel application
     * 
     * @var Illuminate\Foundation\Application
     */
    public static $_app;

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array('password');

    /**
     * List of attribute names which should be hashed. (Ardent)
     *
     * @var array
     */
    public static $passwordAttributes = array('password');

    /**
     * This way the model will automatically replace the plain-text password
     * attribute (from $passwordAttributes) with the hash checksum on save
     *
     * @var bool
     */
    public $autoHashPasswordAttributes = true;

    /**
     * Ardent validation rules
     *
     * @var array
     */
    public static $rules = array(
      'username' => 'required|alpha_dash|between:4,16',
      'email' => 'required|email',
      'password' => 'required|between:4,11|confirmed',
      'password_confirmation' => 'required|between:4,11',
    );

    /**
     * Create a new ConfideUser instance.
     */
    public function __construct()
    {
        parent::__construct();

        if ( ! static::$_app )
            static::$_app = app();

        $this->table = static::$_app['config']->get('auth.table');
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
        $this->confirmed = 1;
        return $this->save();
    }

    /**
     * Reset user password and sends in user e-mail
     *
     * @return string
     */
    public function resetPassword()
    {
        $new_password = substr(md5(microtime().static::$_app['config']->get('app.key')),-9);
        $this->password = static::$_app['hash']->make($new_password);
        if ( $this->save() )
        {
            $this->fixViewHint();

            static::$_app['mailer']->send(
                'confide::emails.passwordreset',
                ['user' => $this, 'new_password' => $new_password],
                function($m){
                    $m->to( $this->email )
                    ->subject( static::$_app['translator']->get('confide::confide.email.password_reset.subject') );
                }
            );

            return true;
        }
        else{
            return false;
        }
    }

    public function save( $rules = array(), $customMessages = array(), Closure $beforeSave = null, Closure $afterSave = null )
    {
        return $this->real_save( $rules, $customMessages, $beforeSave, $afterSave );
    }

    /**
     * Ardent method overloading:
     * Before save the user. Generate a confirmation
     * code if is a new user.
     *
     * @param bool $forced Indicates whether the user is being saved forcefully
     * @return bool
     */
    public function beforeSave( $forced = false )
    {
        if ( empty($this->id) )
        {
            $this->confirmation_code = md5(microtime().static::$_app['config']->get('app.key'));
        }

        /*
         * Remove password_confirmation field before save to
         * database.
         */
        if ( isset($this->password_confirmation) )
        {
            unset( $this->password_confirmation );
        }

        return true;
    }

    /**
     * Ardent method overloading:
     * After save, delivers the confirmation link email.
     * code if is a new user.
     *
     * @param bool $forced Indicates whether the user is being saved forcefully
     * @return bool
     */
    public function afterSave( $success,  $forced = false )
    {
        if ( $success  and ! $this->confirmed )
        {
            $this->fixViewHint();

            static::$_app['mailer']->send('confide::emails.confirm', ['user' => $this], function($m)
            {
                $m->to( $this->email )
                ->subject( static::$_app['translator']->get('confide::confide.email.account_confirmation.subject') );
            });
        }

        return true;
    }

    /**
     * Runs the real eloquent save method or returns
     * true if it's under testing. Because Eloquent
     * and Ardent save methods are not Confide's
     * responsibility.
     *
     * @return bool
     */
    private function real_save( $rules, $customMessages, $beforeSave, $afterSave )
    {
        if ( defined('CONFIDE_TEST') )
        {
            $this->beforeSave();
            $this->afterSave( true );
            return true;
        }
        else{
            return parent::save( $rules, $customMessages, $beforeSave, $afterSave );
        }
    }

    /**
     * Add the namespace 'confide::' to view hints.
     * this makes possible to send emails using package views from
     * the command line.
     *
     * @return void
     */
    private function fixViewHint()
    {
        if (isset(static::$_app['view.finder']))
            static::$_app['view.finder']->addNamespace('confide', __DIR__.'/../../views');
    }
}
