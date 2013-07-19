<?php namespace Zizaco\Confide;

use Illuminate\Auth\UserInterface;
use LaravelBook\Ardent\Ardent;
use J20\Uuid\Uuid;

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
        'username' => 'required|alpha_dash|unique:users',
        'email' => 'required|email|unique:users',
        'password' => 'required|between:4,11|confirmed',
        'password_confirmation' => 'between:4,11',
    );

    /**
     * Rules for when updating a user.
     *
     * @var array
     */
    protected $updateRules = array(
        'username' => 'required|alpha_dash',
        'email' => 'required|email',
        'password' => 'between:4,11|confirmed',
        'password_confirmation' => 'between:4,11',
    );

    /**
     * Create a new ConfideUser instance.
     */
    public function __construct( array $attributes = array() )
    {
        parent::__construct( $attributes );

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

        // ConfideRepository will update the database
        static::$app['confide.repository']
            ->confirmUser( $this );

        return true;
    }

    /**
     * Send email with information about password reset
     *
     * @return string
     */
    public function forgotPassword()
    {
        // ConfideRepository will generate token (and save it into database)
        $token = static::$app['confide.repository']
            ->forgotPassword( $this );

        $view = static::$app['config']->get('confide::email_reset_password');

        $this->sendEmail( 'confide::confide.email.password_reset.subject', $view, array('user'=>$this, 'token'=>$token) );

        return true;
    }

    /**
     * Change user password
     *
     * @param  $params
     * @return string
     */
    public function resetPassword( $params )
    {
        $password = array_get($params, 'password', '');
        $passwordConfirmation = array_get($params, 'password_confirmation', '');

        if ( $password == $passwordConfirmation )
        {
            return static::$app['confide.repository']
                ->changePassword( $this, static::$app['hash']->make($password) );
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
     * @param \Closure $beforeSave
     * @param \Closure $afterSave
     * @param bool  $force Forces saving invalid data. Defaults to false; when true has the same effect as calling
     * @return bool
     */
    public function save( array $rules = array(), array $customMessages = array(), array $options = array(), \Closure $beforeSave = null, \Closure $afterSave = null, $force = false )
    {
        $duplicated = false;

        if(! $this->id)
        {
            $duplicated = static::$app['confide.repository']->userExists( $this );
        }

        if(! $duplicated)
        {
            return $this->real_save( $rules, $customMessages, $options, $beforeSave, $afterSave, $force );
        }
        else
        {
            $this->validationErrors->add(
                'duplicated',
                static::$app['translator']->get('confide::confide.alerts.duplicated_credentials')
            );

            return false;
        }
    }

    /**
     * Ardent method overloading:
     * Before save the user. Generate a confirmation
     * code if is a new user.
     *
     * @param User $user
     * @return bool
     */
    public static function beforeSave( $user )
    {
        if ( empty($user->id) )
        {
            $user->confirmation_code = md5( uniqid(mt_rand(), true) );
        }

        /*
         * Remove password_confirmation field before save to
         * database.
         */
        if ( isset($user->password_confirmation) )
        {
            unset( $user->password_confirmation );
        }

        return true;
    }

    /**
     * Ardent method overloading:
     * After save, delivers the confirmation link email.
     * code if is a new user.
     *
     * @param  User   $user
     * @param  string event status
     * @return bool
     */
    public static function afterSave( $user,  $status = '' )
    {
        if (! $user->confirmed && ! static::$app['cache']->get('confirmation_email_'.$user->id) )
        {
            $view = static::$app['config']->get('confide::email_account_confirmation');

            $user->sendEmail( 'confide::confide.email.account_confirmation.subject', $view, array('user' => $user) );

            // Save in cache that the email has been sent.
            $signup_cache = (int)static::$app['config']->get('confide::signup_cache');
            if ($signup_cache !== 0)
            {
                static::$app['cache']->put('confirmation_email_'.$user->id, true, $signup_cache);
            }
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
     * @param \Closure $beforeSave
     * @param \Closure $afterSave
     * @param bool  $force Forces saving invalid data. Defaults to false; when true has the same effect as calling
     * @return bool
     */
    protected function real_save( array $rules = array(), array $customMessages = array(), array $options = array(), \Closure $beforeSave = null, \Closure $afterSave = null, $force = false )
    {
        if ( defined('CONFIDE_TEST') )
        {
            self::beforeSave( $this );
            self::afterSave( $this );
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

            return parent::save( $rules, $customMessages, $options, $beforeSave, $afterSave, $force );
        }
    }

    /**
     * Alias of save but uses updateRules instead of rules.
     * @param array   $rules
     * @param array   $customMessages
     * @param array   $options
     * @param Closure $beforeSave
     * @param Closure $afterSave
     * @param bool    $force Forces saving invalid data. Defaults to false; when true has the same effect as calling
     * @return bool
     */
    public function amend( array $rules = array(), array $customMessages = array(), array $options = array(), \Closure $beforeSave = null, \Closure $afterSave = null, $force = false )
    {
        if (empty($rules)) {
            $rules = $this->getUpdateRules();
        }
        return $this->save( $rules, $customMessages, $options, $beforeSave, $afterSave, $force );
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

    /*
    |--------------------------------------------------------------------------
    | Deprecated methods
    |--------------------------------------------------------------------------
    |
    */

    /**
     * [Deprecated] Generates UUID and checks it for uniqueness against a table/column.
     *
     * @deprecated
     * @param  $table
     * @param  $field
     * @return string
     */
    protected function generateUuid($table, $field)
    {
        return md5( uniqid(mt_rand(), true) );
    }

    /**
     * [Deprecated]
     *
     * @deprecated
     */
    public function getUpdateRules()
    {
        return $this->updateRules;
    }

    /**
     * [Deprecated] Parses the two given users and compares the unique fields.
     *
     * @deprecated
     * @param $oldUser
     * @param $updatedUser
     * @param array $rules
     */
    public function prepareRules($oldUser, $updatedUser, $rules=array())
    {
        if(empty($rules)) {
            $rules = $this->getRules();
        }

        foreach($rules as $rule => $validation) {
            // get the rules with unique.
            if (strpos($validation, 'unique')) {
                // Compare old vs new
                if($oldUser->$rule != $updatedUser->$rule) {
                    // Set update rule to creation rule
                    $updateRules = $this->getUpdateRules();
                    $updateRules[$rule] = $validation;
                    $this->setUpdateRules($updateRules);
                }
            }
        }
    }

    /**
     * [Deprecated]
     *
     * @deprecated
     */
    public function getRules()
    {
        return self::$rules;
    }

    /**
     * [Deprecated]
     *
     * @deprecated
     */
    public function setUpdateRules($set)
    {
        $this->updateRules = $set;
    }

    /**
     * [Deprecated] Find an user by it's credentials. Perform a 'where' within
     * the fields contained in the $identityColumns.
     *
     * @deprecated Use ConfideRepository getUserByIdentity instead.
     * @param  array $credentials      An array containing the attributes to search for
     * @param  mixed $identityColumns  Array of attribute names or string (for one atribute)
     * @return ConfideUser             User object
     */
    public function getUserFromCredsIdentity($credentials, $identity_columns = array('username', 'email'))
    {
        return static::$app['confide.repository']->getUserByIdentity($credentials, $identity_columns);
    }

    /**
     * [Deprecated] Checks if an user exists by it's credentials. Perform a 'where' within
     * the fields contained in the $identityColumns.
     *
     * @deprecated Use ConfideRepository getUserByIdentity instead.
     * @param  array $credentials      An array containing the attributes to search for
     * @param  mixed $identityColumns  Array of attribute names or string (for one atribute)
     * @return boolean                 Exists?
     */
    public function checkUserExists($credentials, $identity_columns = array('username', 'email'))
    {
        $user = static::$app['confide.repository']->getUserByIdentity($credentials, $identity_columns);

        if ($user) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * [Deprecated] Checks if an user is confirmed by it's credentials. Perform a 'where' within
     * the fields contained in the $identityColumns.
     *
     * @deprecated Use ConfideRepository getUserByIdentity instead.
     * @param  array $credentials      An array containing the attributes to search for
     * @param  mixed $identityColumns  Array of attribute names or string (for one atribute)
     * @return boolean                 Is confirmed?
     */
    public function isConfirmed($credentials, $identity_columns = array('username', 'email'))
    {
        $user = static::$app['confide.repository']->getUserByIdentity($credentials, $identity_columns);

        if (! is_null($user) and $user->confirmed) {
            return true;
        } else {
            return false;
        }
    }
}
