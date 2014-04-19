{{ '<?php' }}

/**
 * Class UserRepository
 *
 * This service abstracts some interactions that occurs within Confide and
 * the Database.
 */
class UserRepository
{
    /**
     * Signup a new account with the given parameters
     * @param  array $input Array containing 'username', 'email' and 'password'.
     * @return {{ $model }} {{ $model }} object that may or may not be saved successfully. Check the id to make sure.
     */
    public function signup($input)
    {
        $user = new {{ $model }};

        $user->username = array_get($input, 'username');
        $user->email    = array_get($input, 'email');
        $user->password = array_get($input, 'password');

        // The password confirmation will be removed from model
        // before saving. This field will be used in Ardent's
        // auto validation.
        $user->password_confirmation = array_get($input, 'password_confirmation' );

        // Save if valid. Password field will be hashed before save
        $this->save($user);

        return $user;
    }

    /**
     * Attempts to login with the given credentials.
     * @param  array $input Array containing the credentials (email/username and password)
     * @return boolean Success?
     */
    public function login($input)
    {
        if(! isset($input['password']))
            $input['password'] = null;

        $identityColumns = ['email', 'username'];

        return Confide::logAttempt($input, Config::get('confide::signup_confirm'), $identityColumns);
    }

    /**
     * Checks if the credentials has been throttled by too
     * much failed login attempts
     *
     * @param array $credentials Array containing the credentials (email/username and password)
     * @return boolean Is throttled
     */
    public function isThrottled($input)
    {
        return Confide::isThrottled($input);
    }

    /**
     * Checks if the given credentials correponds to a user that exists but
     * is not confirmed
     * @param array $credentials Array containing the credentials (email/username and password)
     * @return boolean Exists and is not confirmed?
     */
    public function existsButNotConfirmed($input)
    {
        $user = App::make('{{ $model }}');

        return $user->checkUserExists($input) and !$user->isConfirmed($input);
    }

    /**
     * Simply saves the given instance
     * @param  {{ $model }} $instance
     * @return boolean           Success
     */
    public function save({{ $model }} $instance)
    {
        return $instance->save();
    }
}
