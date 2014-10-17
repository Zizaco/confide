<?php namespace Zizaco\Confide;

use Zizaco\Confide\Facade as ConfideFacade;
use Illuminate\Support\Facades\App as App;

/**
 * This is a trait containing a initial implementation of the
 * methods declared in the ConfideUserInterface.
 *
 * @see \Zizaco\Confide\ConfideUserInterface
 * @license MIT
 * @package Zizaco\Confide
 */
trait ConfideUser
{
    /**
     * A MessageBag object that store any error regarding the confide User.
     *
     * @var \Illuminate\Support\MessageBag
     */
    public $errors;

    /**
     * Confirm the user (usually means that the user)
     * email is valid. Sets the confirmed attribute of
     * the user to true and also update the database.
     *
     * @return bool Success.
     */
    public function confirm()
    {
        $this->confirmed = true;

        return ConfideFacade::confirm($this->confirmation_code);
    }

    /**
     * Generates a token for password change and saves it in the
     * 'password_reminders' table with the email of the
     * user.
     *
     * @return string $token
     */
    public function forgotPassword()
    {
        return ConfideFacade::forgotPassword($this->email);
    }

    /**
     * Overwrites the original save method in order to perform
     * validation before actually saving the object.
     *
     * @param array $options
     *
     * @return bool
     */
    public function save(array $options = array())
    {
        return parent::save($options);
    }

    /**
     * Returns a MessageBag object that store any error
     * regarding the user validation.
     *
     * @return \Illuminate\Support\MessageBag
     */
    public function errors()
    {
        return $this->errors ?: $this->errors = App::make('Illuminate\Support\MessageBag');
    }

    /**
     * Get the unique identifier for the user.
     *
     * @see \Illuminate\Auth\UserInterface
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        // Get the value of the model's primary key.
        return $this->getKey();
    }

    /**
     * Get the password for the user.
     *
     * @see \Illuminate\Auth\UserInterface
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @see \Illuminate\Auth\UserInterface
     *
     * @return string
     */
    public function getRememberToken()
    {
        return $this->{$this->getRememberTokenName()};
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @see \Illuminate\Auth\UserInterface
     *
     * @param string $value
     */
    public function setRememberToken($value)
    {
        $this->{$this->getRememberTokenName()} = $value;
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @see \Illuminate\Auth\UserInterface
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    /**
     * Get the e-mail address where password reminders are sent.
     *
     * @see \Illuminate\Auth\Reminders\RemindableInterface
     *
     * @return string
     */
    public function getReminderEmail()
    {
        return $this->email;
    }
}
