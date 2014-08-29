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
        // If is an array, try to pick up the login throttle field. Othewise, transforms it into a string.
        if (is_array($identity)) {

            $fields = $this->app['config']->get('confide::login_throttle_field');

            if( !$fields )
                return $this->serializeIdentity($identity);

            $fields = array_flip((array)$fields);

            $matches = array_intersect_key($fields, $identity);

            if ( $matches ) {
                $identity = $identity[key($matches)];
            } else {
                return $this->serializeIdentity($identity);
            }

        }

        return $identity;
    }


    /**
     * remove password, remember, _token and then transform identity into a string.
     *
     *
     * @param array $identity
     *
     * @return string $identityString.
     */
    protected function serializeIdentity(array $identity)
    {
        unset($identity['password'],$identity['remember'],$identity['_token']);
        $identity = serialize($identity);
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
