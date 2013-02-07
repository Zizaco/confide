<?php namespace Zizaco\Confide;

use Illuminate\View\Environment;
use Illuminate\Config\Repository;
use ObjectProvier;

class Confide
{
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
     * Defines how many login failed tries may be done within
     * two minutes.
     * 
     * @var integer
     */
    protected $throttle_limit = 9;

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
        return 'Confide v'.CONFIDE_VERSION;
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
     * @param  bool $confirmed_only
     * @return void
     */
    public function logAttempt( $credentials, $confirmed_only = false )
    {
        // Throttle login attempts
        $attempt_key = $this->attemptCacheKey( $credentials );
        $attempts = $this->_app['cache']->get($attempt_key, 0);

        if( $attempts < $this->throttle_limit )
        {
            // Try to login normally
            $user = $this->model()
                ->where('email','=',$credentials['email'])
                ->orWhere('username','=',$credentials['email'])
                ->first();

            if( ! is_null($user) and ($user->confirmed or !$confirmed_only ) and $this->_app['hash']->check($credentials['password'], $user->password) )
            {
                $this->_app['auth']->login( 
                    $user,
                    isset($credentials['remember']) ? $credentials['remember'] : false 
                );

                return true;
            }
        }

        $this->_app['cache']->put($attempt_key, $attempts+1, 2); // used throttling login attempts

        return false;
    }

    /**
     * Checks if the credentials has been throttled by too
     * much failed login attempts
     * 
     * @param array $credentials
     * @return mixed Value.
     */
    public function isThrottled( $credentials )
    {
        // Check how many failed tries have been done
        $attempt_key = $this->attemptCacheKey( $credentials );
        $attempts = $this->_app['cache']->get($attempt_key, 0);

        if( $attempts >= $this->throttle_limit )
        {
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

    /**
     * Returns the name of the cache key that will be used
     * to store the failed attempts
     *
     * @param array $credentials.
     * @return string.
     */
    private function attemptCacheKey( $credentials )
    {
        return 'confide_flogin_attempt_'
            .$this->_app['request']->server('REMOTE_ADDR')
            .$this->_app['request']->server('HTTP_X_FORWARDED_FOR')
            .$credentials['email'];
    }
}
