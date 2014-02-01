<?php namespace Zizaco\Confide;

/**
 * This class is the main entry point to use the confide
 * services. Usually this is the only service class that the
 * application will interact directly with.
 */
class Confide
{
    /**
     * Laravel application
     *
     * @var \Illuminate\Foundation\Application
     */
    public $app;

    /**
     * Confide repository instance
     *
     * @var \Zizaco\Confide\RepositoryInterface
     */
    public $repo;

    /**
     * Confide password service instance
     *
     * @var \Zizaco\Confide\PasswordServiceInterface
     */
    public $passService;

    /**
     * Create a new Confide class
     *
     * @param  \Zizaco\Confide\RepositoryInterface      $repo
     * @param  \Zizaco\Confide\PasswordServiceInterface $passService
     * @param  \Illuminate\Foundation\Application     $app Laravel application object
     * @return void
     */
    public function __construct(
        RepositoryInterface      $repo,
        PasswordServiceInterface $passService,
        $app = null
    )
    {
        $this->repo        = $repo;
        $this->passService = $passService;
        $this->app         = $app ?: app();
    }

    /**
     * Returns an object of the model set in auth config
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
     * Sets the confirmation code of the user with the
     * matching code to true.
     *
     * @param string $code
     * @return bool Success
     */
    public function confirm($code)
    {
        return $this->repo->confirmByCode($code);
    }

    /**
     * Attempt to log a user into the application with
     * password and identity field(s), usually email or username.
     *
     * @param  array $input Array containing at least 'username' or 'email' and 'password'. Optionally the 'remember' boolean.
     * @param  bool $mustBeConfirmed If true, the user must have confirmed his email account in order to log-in.
     * @return boolean Success
     */
    public function logAttempt($input, $mustBeConfirmed = true)
    {
        $remember = $this->extractRememberFromArray($input);
        $emailOrUsername = $this->extractIdentityFromArray($input);
        $user = $this->repo->getUserByEmailOrUsername($emailOrUsername);

        if ($user) {
            if (! $user->confirmed && $mustBeConfirmed )
                return false;

            $correctPassword = $this->app['hash']->check(
                isset($input['password']) ? $input['password'] : false,
                $user->password
            );

            if (! $correctPassword)
                return false;

            $this->app['auth']->login($user, $remember);
            return true;
        }

        return false;
    }

    protected function extractRememberFromArray($input)
    {
        if (isset($input['remember'])) {
            $remember = $input['remember'];
        } else {
            $remember = false;
        }

        return $remember;
    }

    protected function extractIdentityFromArray($input)
    {
        if (isset($input['email'])) {
            $emailOrUsername = $input['email'];
        } elseif (isset($input['username'])) {
            $emailOrUsername = $input['username'];
        } else {
            return false;
        }

        return $emailOrUsername;
    }
}
