<?php namespace Zizaco\Confide;

/**
 * This class is the main entry point to use the confide
 * services. Usually this is the only service class that the
 * application will interact directly with.
 *
 * @license MIT
 * @package Zizaco\Confide
 */
class Confide
{
    /**
     * Laravel application.
     *
     * @var \Illuminate\Foundation\Application
     */
    public $app;

    /**
     * Confide repository instance.
     *
     * @var \Zizaco\Confide\RepositoryInterface
     */
    public $repo;

    /**
     * Confide password service instance.
     *
     * @var \Zizaco\Confide\PasswordServiceInterface
     */
    public $passService;

    /**
     * Confide login throttling service instance.
     *
     * @var \Zizaco\Confide\LoginThrottleServiceInterface
     */
    public $loginThrottler;

    /**
     * Create a new Confide class.
     *
     * @param \Zizaco\Confide\RepositoryInterface           $repo
     * @param \Zizaco\Confide\PasswordServiceInterface      $passService
     * @param \Zizaco\Confide\LoginThrottleServiceInterface $loginThrottler
     * @param \Illuminate\Foundation\Application            $app            Laravel application object
     *
     * @return void
     */
    public function __construct(
        RepositoryInterface       $repo,
        PasswordServiceInterface  $passService,
        LoginThrottleServiceInterface $loginThrottler,
        $app = null
    ) {
        $this->repo           = $repo;
        $this->passService    = $passService;
        $this->loginThrottler = $loginThrottler;
        $this->app            = $app ?: app();
    }

    /**
     * Returns an object of the model set in auth config.
     *
     * @return mixed
     */
    public function model()
    {
        return $this->repo->model();
    }

    /**
     * Get the currently authenticated user or null.
     *
     * @return \Zizaco\Confide\ConfideUserInterface|null
     */
    public function user()
    {
        return $this->app['auth']->user();
    }

    /**
     * Sets the 'confirmed' field of the user with the matching code to true.
     *
     * @param string $code
     *
     * @return bool Success
     */
    public function confirm($code)
    {
        return $this->repo->confirmByCode($code);
    }

    /**
     * Checks if a user with the given identity (email or username) already
     * exists and retrieve it.
     *
     * @param array $identity Array containing at least 'username' or 'email'.
     *
     * @return \Zizaco\Confide\ConfideUserInterface|null
     */
    public function getUserByEmailOrUsername($identity)
    {
        if (is_array($identity)) {
            $identity = $this->extractIdentityFromArray($identity);
        }

        return $this->repo->getUserByEmailOrUsername($identity);
    }

    /**
     * Attempt to log a user into the application with password and
     * identity field(s), usually email or username.
     *
     * @param array $input           Array containing at least 'username' or 'email' and 'password'.
     *                               Optionally the 'remember' boolean.
     * @param bool  $mustBeConfirmed If true, the user must have confirmed his email account in order to log-in.
     *
     * @return bool Success.
     */
    public function logAttempt(array $input, $mustBeConfirmed = true)
    {
        $remember = $this->extractRememberFromArray($input);
        $emailOrUsername = $this->extractIdentityFromArray($input);

        if (!$this->loginThrottling($emailOrUsername)) {
            return false;
        }

        $user = $this->repo->getUserByEmailOrUsername($emailOrUsername);

        if ($user) {
            if (! $user->confirmed && $mustBeConfirmed) {
                return false;
            }

            $correctPassword = $this->app['hash']->check(
                isset($input['password']) ? $input['password'] : false,
                $user->password
            );

            if (! $correctPassword) {
                return false;
            }

            $this->app['auth']->login($user, $remember);
            return true;
        }

        return false;
    }

    /**
     * Extracts the value of the remember key of the given array.
     *
     * @param array $input An array containing the key 'remember'.
     *
     * @return bool
     */
    protected function extractRememberFromArray(array $input)
    {
        if (isset($input['remember'])) {
            return $input['remember'];
        }

        return false;
    }

    /**
     * Extracts the email or the username key of the given array.
     *
     * @param array $input An array containing the key 'email' or 'username'.
     *
     * @return mixed
     */
    protected function extractIdentityFromArray(array $input)
    {
        if (isset($input['email'])) {
            return $input['email'];
        } elseif (isset($input['username'])) {
            return $input['username'];
        }

        return false;
    }

    /**
     * Calls throttleIdentity of the loginThrottler and returns false
     * if the throttleCount is grater then the 'throttle_limit' config.
     * Also sleeps a little in order to avoid dicionary attacks.
     *
     * @param mixed $identity.
     *
     * @return boolean False if the identity has reached the 'throttle_limit'.
     */
    protected function loginThrottling($identity)
    {
        $count = $this->loginThrottler
            ->throttleIdentity($identity);

        if ($count >= $this->app['config']->get('confide::throttle_limit')) {
            return false;
        }

        // Throttling delay!
        // See: http://www.codinghorror.com/blog/2009/01/dictionary-attacks-101.html
        if ($count > 2) {
            usleep(($count-1) * 400000);
        }

        return true;
    }

    /**
     * Asks the loginThrottler service if the given identity has reached the throttle_limit.
     *
     * @param mixed $identity The login identity.
     *
     * @return boolean True if the identity has reached the throttle_limit.
     */
    public function isThrottled($identity)
    {
        return $this->loginThrottler->isThrottled($identity);
    }

    /**
     * If an user with the given email exists then generate a token for password
     * change and saves it in the 'password_reminders' table with the email
     * of the user.
     *
     * @param string $email
     *
     * @return string $token
     */
    public function forgotPassword($email)
    {
        $user = $this->repo->getUserByEmail($email);

        if ($user) {
            return $this->passService->requestChangePassword($user);
        }

        return false;
    }

    /**
     * Delete the record of the given token from 'password_reminders' table.
     *
     * @param string $token Token retrieved from a forgotPassword.
     *
     * @return boolean Success.
     */
    public function destroyForgotPasswordToken($token)
    {
        return $this->passService->destroyToken($token);
    }

    /**
     * Returns a user that corresponds to the given reset password token or
     * false if there is no user with the given token.
     *
     * @param string $token
     *
     * @return ConfideUser
     */
    public function userByResetPasswordToken($token)
    {
        $email = $this->passService->getEmailByToken($token);

        if ($email) {
            return $this->repo->getUserByEmail($email);
        }

        return false;
    }

    /**
     * Log the user out of the application.
     */
    public function logout()
    {
        return $this->app['auth']->logout();
    }

    /**
     * Display the default login view.
     *
     * @return \Illuminate\View\View
     */
    public function makeLoginForm()
    {
        return $this->app['view']->make($this->app['config']->get('confide::login_form'));
    }

    /**
     * Display the default signup view
     *
     * @return \Illuminate\View\View
     */
    public function makeSignupForm()
    {
        return $this->app['view']->make($this->app['config']->get('confide::signup_form'));
    }

    /**
     * Display the forget password view.
     *
     * @return \Illuminate\View\View
     */
    public function makeForgotPasswordForm()
    {
        return $this->app['view']->make($this->app['config']->get('confide::forgot_password_form'));
    }

    /**
     * Display the forget password view
     *
     * @return \Illuminate\View\View
     */
    public function makeResetPasswordForm($token)
    {
        return $this->app['view']->make(
            $this->app['config']->get('confide::reset_password_form'),
            array('token' => $token)
        );
    }
}
