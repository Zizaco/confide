<?php namespace Zizaco\Confide;

use Illuminate\Auth\UserInterface;
use LaravelBook\Ardent\Ardent;
use J20\Uuid;

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
    public static $app;

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
      'email' => 'required|email',
      'password' => 'required|between:4,11|confirmed',
    );

    /**
     * Create a new ConfideUser instance.
     */
    public function __construct()
    {
        parent::__construct();

        if ( ! static::$app )
            static::$app = app();

        $this->table = static::$app['config']->get('auth.table');
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

        // Executes directly using query builder 
        return static::$app['db']->table($this->table)
            ->where($this->getKeyName(), $this->getKey())
            ->update(array('confirmed'=>1));
    }

    /**
     * Send email with information about password reset
     *
     * @return string
     */
    public function forgotPassword()
    {
        $token = $this->generateUuid('password_reminders', 'token');

        static::$app['db']->connection()->table('password_reminders')->insert(array(
            'email'=> $this->email,
            'token'=> $token,
            'created_at'=> new \DateTime
        ));

        $view = static::$app['config']->get('confide::email_reset_password');

        $this->sendEmail( 'confide::confide.email.password_reset.subject', $view, array('user'=>$this, 'token'=>$token) );

        return true;
    }

    /**
     * Change user password
     *
     * @param $params
     * @return string
     */
    public function resetPassword( $params )
    {
        $this->password = array_get($params, 'password', '');
        $this->password_confirmation = array_get($params, 'password_confirmation', '');

        if ( $this->save() )
        {
            return true;
        }
        else{
            return false;
        }
    }

    /**
     * Overwrite the Ardent save method. Saves model into
     * database
     *
     * @param array $rules:array
     * @param array $customMessages
     * @param array $options
     * @param closure $beforeSave
     * @param callable|\Zizaco\Confide\Closure $afterSave
     * @return bool
     */
    public function save( array $rules = array(), array $customMessages = array(), array $options = array(), Closure $beforeSave = null, Closure $afterSave = null )
    {
        return $this->real_save( $rules, $customMessages, $options, $beforeSave, $afterSave );
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
            $this->confirmation_code = $this->generateUuid($this->table, 'confirmation_code');
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
     * @param bool $success
     * @param bool $forced Indicates whether the user is being saved forcefully
     * @return bool
     */
    public function afterSave( $success,  $forced = false )
    {
        if ( $success  and ! $this->confirmed )
        {
            $view = static::$app['config']->get('confide::email_account_confirmation');
            
            $this->sendEmail( 'confide::confide.email.account_confirmation.subject', $view, array('user' => $this) );
        }

        return true;
    }

    /**
     * Runs the real eloquent save method or returns
     * true if it's under testing. Because Eloquent
     * and Ardent save methods are not Confide's
     * responsibility.
     *
     * @param array $rules
     * @param array $customMessages
     * @param array $options
     * @param Closure $beforeSave
     * @param Closure $afterSave
     * @return bool
     */
    protected function real_save( array $rules = array(), array $customMessages = array(), array $options = array(), Closure $beforeSave = null, Closure $afterSave = null )
    {
        if ( defined('CONFIDE_TEST') )
        {
            $this->beforeSave();
            $this->afterSave( true );
            return true;
        }
        else{

            /*
             * This will make sure that a non modified password
             * will not trigger validation error.
             */
            if( empty($rules) && $this->password == $this->getOriginal('password') )
            {
                $rules = static::$rules;
                $rules['password'] = 'required';
            }

            return parent::save( $rules, $customMessages, $options, $beforeSave, $afterSave );
        }
    }

    /**
     * Add the namespace 'confide::' to view hints.
     * this makes possible to send emails using package views from
     * the command line.
     *
     * @return void
     */
    protected static function fixViewHint()
    {
        if (isset(static::$app['view.finder']))
            static::$app['view.finder']->addNamespace('confide', __DIR__.'/../../views');
    }

    /**
     * Send email using the lang sentence as subject and the viewname
     *
     * @param mixed $subject_translation
     * @param mixed $view_name
     * @param array $params
     * @return voi.
     */
    protected function sendEmail( $subject_translation, $view_name, $params = array() )
    {
        if ( static::$app['config']->getEnvironment() == 'testing' )
            return;

        static::fixViewHint();

        $user = $this;

        static::$app['mailer']->send($view_name, $params, function($m) use ($subject_translation, $user)
        {
            $m->to( $user->email )
            ->subject( ConfideUser::$app['translator']->get($subject_translation) );
        });
    }

    /**
     * Generates UUID and checks it for uniqueness against a table/column.
     *
     * @param  $table
     * @param  $field
     * @return string
     */
    protected function generateUuid($table, $field)
    {
        // Generate Uuid
        $uuid = Uuid\Uuid::v4(false);
        // Check that it is unique
        $currentConfirmationCode = static::$app['db']->table($table)->where($field, $uuid)->first();
        // If it isn't unique, try again. Make sure we're not Mocking.
        if($currentConfirmationCode != NULL && !is_a($currentConfirmationCode, 'Mockery\Mock')) {
           $uuid =  $this->generateUuid($table, $field);
        }

        return $uuid;
    }
}
