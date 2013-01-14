<?php namespace Zizaco\Confide;

use Illuminate\Auth\UserInterface;
use Config;
use Mail;
use Hash;

class ConfideUser extends \Eloquent implements UserInterface {

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
     * Create a new ConfideUser instance.
     */
    public function __construct()
    {
        parent::__construct();
        $this->table = Config::get('auth.table');
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
        $new_password = substr(md5(microtime().Config::get('app.key')),-9);
        $this->password = Hash::make($new_password);
        if ( $this->save() )
        {
            Mail::send(
                'confide::emails.passwordreset',
                ['user' => $this, 'new_password' => $new_password],
                function($m){
                    $m->to( $this->email )->subject( Lang::get('confide::confide.email.password_reset.subject') );
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
            $this->confirmation_code = md5(microtime().Config::get('app.key'));
        }

        if ( parent::save($this) )
        {
            Mail::send('confide::emails.confirm', ['user' => $this], function($m)
            {
                $m->to( $this->email )->subject( Lang::get('confide::confide.email.account_confirmation.subject') );
            });

            return true;
        }
    }

}
