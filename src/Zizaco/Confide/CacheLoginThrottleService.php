<?php namespace Zizaco\Confide;

/**
 * The LoginThrottle is a service that Throttles login attempts
 * after too many failed attempts. This is a secure measure in
 * order to avoid brute force attacks.
 *
 * @package  Zizaco\Confide
 */
class CacheLoginThrottleService implements LoginThrottleServiceInterface
{
    /**
     * Laravel application
     *
     * @var \Illuminate\Foundation\Application
     */
    public $app;

    /**
     * Create a new PasswordService
     *
     * @param  \Illuminate\Foundation\Application $app Laravel application object
     * @return void
     */
    public function __construct($app = null)
    {
        $this->app = $app ?: app();
    }

    /**
     * Increments the count for the given identity by one and
     * also returns the current value for that identity.
     *
     * @param  mixed $identity The login identity
     * @return integer How many times that same identity was used
     */
    public function throttleIdentity($identity)
    {
        // If is an array, remove password, remember and then
        // transforms it into a string.
        if (is_array($identity))
        {
            unset($identity['password']);
            unset($identity['remember']);
            $identity = serialize($identity);
        }

        // Increments and also retuns the current count
        return $this->countThrottle($identity);
    }

    /**
     * Increments the count for the given string by one stores
     * it into cache and returns the current value for that
     * identity.
     *
     * @param  string $identityString
     * @return integer How many times that same string was used
     */
    protected function countThrottle($identityString)
    {
        $count = $this->app['cache']
            ->get('login_throttling:'.md5($identityString), 0);

        $count++;

        $ttl = $this->app['config']->get('confide::throttle_time_period');

        $this->app['cache']
            ->put('login_throttling:'.md5($identityString), $count, $ttl);

        return $count;
    }
}
