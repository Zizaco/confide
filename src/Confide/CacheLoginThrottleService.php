<?php namespace Zizaco\Confide;

/**
 * The LoginThrottle is a service that Throttles login after
 * too many failed attempts. This is a secure measure in
 * order to avoid brute force attacks.
 *
 * @license MIT
 * @package Zizaco\Confide
 */
class CacheLoginThrottleService implements LoginThrottleServiceInterface
{
    /**
     * Laravel application.
     *
     * @var \Illuminate\Foundation\Application
     */
    public $app;

    /**
     * Create a new PasswordService.
     *
     * @param \Illuminate\Foundation\Application $app Laravel application object.
     */
    public function __construct($app = null)
    {
        $this->app = $app ?: app();
    }

    /**
     * Increments the count for the given identity by one and
     * also returns the current value for that identity.
     *
     * @param mixed $identity The login identity.
     *
     * @return int How many times that same identity was used.
     */
    public function throttleIdentity($identity)
    {
        $identity = $this->parseIdentity($identity);

        // Increments and also retuns the current count
        return $this->countThrottle($identity);
    }

    /**
     * Tells if the given identity has reached the throttle_limit.
     *
     * @param mixed $identity The login identity.
     *
     * @return bool True if the identity has reached the throttle_limit.
     */
    public function isThrottled($identity)
    {
        $identity = $this->parseIdentity($identity);

        // Retuns the current count
        $count = $this->countThrottle($identity, 0);

        return $count >= $this->app['config']->get('confide::throttle_limit');
    }

    /**
     * Parse the given identity in order to return a string with
     * the relevant fields. I.E: if the attacker tries to use a
     * bunch of different passwords, the identity will still be the
     * same.
     *
     * @param mixed $identity
     *
     * @return string $identityString.
     */
    protected function parseIdentity($identity)
    {
        if (is_array($identity)) {
            if (isset($identity['email'])) {
                return $identity['email'];
            } elseif (isset($identity['username'])) {
                return $identity['username'];
            } else {
                return serialize($identity);
            }
        }

        return $identity;
    }

    /**
     * Increments the count for the given string by one stores
     * it into cache and returns the current value for that
     * identity.
     *
     * @param string $identityString
     * @param int    $increments     Amount that is going to be added to the throttling attemps for the given identity.
     *
     * @return int How many times that same string was used.
     */
    protected function countThrottle($identityString, $increments = 1)
    {
        $count = $this->app['cache']
            ->get('login_throttling:'.md5($identityString), 0);

        $count = $count + $increments;

        $ttl = $this->app['config']->get('confide::throttle_time_period');

        $this->app['cache']
            ->put('login_throttling:'.md5($identityString), $count, $ttl);

        return $count;
    }
}
