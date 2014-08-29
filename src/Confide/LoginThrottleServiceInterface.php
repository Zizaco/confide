<?php namespace Zizaco\Confide;

/**
 * The LoginThrottle is a service that Throttles login after
 * too many failed attempts. This is a secure measure in
 * order to avoid brute force attacks.
 *
 * @license MIT
 * @package Zizaco\Confide
 */
interface LoginThrottleServiceInterface
{
    /**
     * Increments the count for the given identity by one and
     * also returns the current value for that identity.
     *
     * @param mixed $identity The login identity.
     *
     * @return int How many times that same identity was used.
     */
    public function throttleIdentity($identity);

    /**
     * Tells if the given identity has reached the throttle_limit.
     *
     * @param mixed $identity The login identity.
     *
     * @return bool True if the identity has reached the throttle_limit.
     */
    public function isThrottled($identity);
}
