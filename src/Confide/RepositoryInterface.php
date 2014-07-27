<?php namespace Zizaco\Confide;

/**
 * A service that abstracts all the interactions with persistent storage for confide.
 *
 * @license MIT
 * @package Zizaco\Confide
 */
interface RepositoryInterface
{
    /**
     * Returns the model set in auth config.
     *
     * @return mixed Instantiated object of the 'auth.model' class.
     */
    public function model();

    /**
     * Find a user by one of the fields given as $identity.
     * If one of the fields in the $identity array matches the user
     * will be retrieved.
     *
     * @param array $identity An array of attributes and values to search for.
     *
     * @return ConfideUser User object.
     */
    public function getUserByIdentity($identity);

    /**
     * Find a user by the given email.
     *
     * @param string $email The email to be used in the query.
     *
     * @return ConfideUser User object.
     */
    public function getUserByEmail($email);

    /**
     * Find a user by the given email or username.
     *
     * @param string $emailOrUsername Username of email to be used in the query.
     *
     * @return ConfideUser User object.
     */
    public function getUserByEmailOrUsername($emailOrUsername);

    /**
     * Update the confirmation status of a user to true if a user
     * is found with the given confirmation code.
     *
     * @param string $code
     *
     * @return bool Success.
     */
    public function confirmByCode($code);
}
