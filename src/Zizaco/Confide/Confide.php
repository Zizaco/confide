<?php namespace Zizaco\Confide;

use Illuminate\View\Environment;
use Illuminate\Config\Repository;
use InvalidArgumentException;
use Zizaco\Confide\ObjectProvider;

class Confide
{
    /**
     * Laravel application
     * 
     * @var Illuminate\Foundation\Application
     */
    public $app;

    /**
     * Object repository
     * 
     * @var Zizaco\Confide\ObjectProvider
     */
    public $objectRepository;

    /**
     * Create a new confide instance.
     * 
     * @return void
     */
    public function __construct()
    {
        $this->app = app();
        $this->objectRepository = new ObjectProvider;
    }

    /**
     * Returns the Laravel application
     * 
     * @return Illuminate\Foundation\Application
     */
    public function app()
    {
        return $this->app;
    }

    /**
     * Returns the model set in auth config
     *
     * @return string
     */
    public function model()
    {
        $model = $this->app['config']->get('auth.model');

        return $this->objectRepository->getObject( $model );

    }

    /**
     * Get the currently authenticated user or null.
     *
     * @return Zizaco\Confide\ConfideUser|null
     */
    public function user()
    {
        return $this->app['auth']->user();
    }

    /**
     * Set the user confirmation to true.
     *
     * @param string $code
     * @return bool
     */
    public function confirm( $code )
    {
        $user = $this->model()->where('confirmation_code', '=', $code)->get()->first();
        if( $user )
        {
            return $user->confirm();
        }
        else
        {
            return false;
        }
    }

    /**
     * Attempt to log a user into the application with
     * password and identity field(s), usually email or username.
     *
     * @param  array $credentials
     * @param  bool $confirmed_only
     * @param  mixed $identity_columns
     * @return void
     */
    public function logAttempt( $credentials, $confirmed_only = false, $identity_columns = array('username', 'email') )
    {
        if(! $this->reachedThrottleLimit( $credentials ) )
        {
            $user = $this->model()
                ->getUserFromCredsIdentity($credentials, $identity_columns);

            $password_field = $this->app['config']->get('confide::password_field');
            
            if( ! is_null($user) and ($user->confirmed or !$confirmed_only ) and
                $this->app['hash']->check($credentials['password'], $user->$password_field) )
            {
                $remember = isset($credentials['remember']) ? $credentials['remember'] : false;

                $this->app['auth']->login( $user, $remember );
                return true;
            }
        }

        $this->throttleCount( $credentials );

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
        $attempts = $this->app['cache']->get($attempt_key, 0);

        if( $attempts >= $this->app['config']->get('confide::throttle_limit') )
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Send email with information about password reset
     *
     * @param string  $email
     * @return bool
     */
    public function forgotPassword( $email )
    {
        $user = Confide::model()->where('email', '=', $email)->get()->first();
        if( $user )
        {
            $user->forgotPassword();
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Checks to see if the user has a valid token.
     * 
     * @param $token
     * @return bool
     */
    public function isValidToken( $token )
    {
        $count = $this->app['db']->connection()->table('password_reminders')
            ->where('token','=',$token)
            ->count();

        return ($count != 0);
    }

    /**
     * Change user password
     *
     * @return string
     */
    public function resetPassword( $params )
    {
        $token = array_get($params, 'token', '');
        
        $email = $this->app['db']->connection()->table('password_reminders')
            ->select('email')->where('token','=',$token)
            ->first();

        if ($email)
            $email = $email->email;

        $user = Confide::model()->where('email', '=', $email)->get()->first();
        if( $user )
        {
            if($user->resetPassword( $params ))
            {
                // Password reset success, remove token from database
                $this->app['db']->connection()->table('password_reminders')
                    ->where('token', '=', $token)
                    ->delete();

                return true;
            }
            else
            {
                return false;
            }
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
        $this->app['auth']->logout();
    }

    /**
     * Display the default login view
     *
     * @deprecated
     * @return Illuminate\View\View
     */
    public function makeLoginForm()
    {
        return $this->app['view']->make($this->app['config']->get('confide::login_form'));
    }

    /**
     * Display the default signup view
     *
     * @deprecated
     * @return Illuminate\View\View
     */
    public function makeSignupForm()
    {
        return $this->app['view']->make( $this->app['config']->get('confide::signup_form') );
    }

    /**
     * Display the forget password view
     *
     * @deprecated
     * @return Illuminate\View\View
     */
    public function makeForgotPasswordForm()
    {
        return $this->app['view']->make( $this->app['config']->get('confide::forgot_password_form') );
    }

    /**
     * Display the forget password view
     *
     * @deprecated
     * @return Illuminate\View\View
     */
    public function makeResetPasswordForm( $token )
    {
        return $this->app['view']->make( $this->app['config']->get('confide::reset_password_form') , array('token'=>$token));
    }

    /**
     * Check whether the controller's action exists.
     * Returns the url if it does. Otherwise false.
     * @param $controllerAction
     * @return string
     */
    public function checkAction( $action, $parameters = array(), $absolute = true )
    {
        try {
            $url = $this->app['url']->action($action, $parameters, $absolute);
        } catch( InvalidArgumentException $e ) {
            return false;
        }

        return $url;
    }

    /**
     * Returns the name of the cache key that will be used
     * to store the failed attempts
     *
     * @param array $credentials.
     * @return string.
     */
    protected function attemptCacheKey( $credentials )
    {
        return 'confide_flogin_attempt_'
            .$this->app['request']->server('REMOTE_ADDR')
            .$this->app['request']->server('HTTP_X_FORWARDED_FOR')
            .$credentials['email'];
    }

    /**
     * Checks if the current IP / email has reached the throttle
     * limit
     * 
     * @param array $credentials
     * @return bool Value.
     */
    protected function reachedThrottleLimit( $credentials )
    {
        $attempt_key = $this->attemptCacheKey( $credentials );
        $attempts = $this->app['cache']->get($attempt_key, 0);

        return $attempts >= $this->app['config']->get('confide::throttle_limit');
    }

    /**
     * Increment IP / email throttle count
     * 
     * @param array $credentials
     * @return void
     */
    protected function throttleCount( $credentials )
    {
        $attempt_key = $this->attemptCacheKey( $credentials );
        $attempts = $this->app['cache']->get($attempt_key, 0);

        $this->app['cache']->put($attempt_key, $attempts+1, 2); // used throttling login attempts
    }
}
