<?php namespace Zizaco\Confide;

use Illuminate\Auth\UserInterface;
use Config;

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
        $this->table = Config::get('confide::users_table');
        parent::__construct();
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

}
