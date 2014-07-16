<?php namespace Zizaco\Confide;

use Illuminate\Support\Facades\App as App;
use Illuminate\Support\Facades\Lang as Lang;
use Illuminate\Support\MessageBag as MessageBag;
use Illuminate\Support\Contracts\MessageProviderInterface as MessageProviderInterface;


/**
 * This is the default validator used by ConfideUser. You may overwrite this
 * class and implement your own validator by creating a class that implements
 * the `UserValidatorInterface` and by registering that class in IoC container
 * as 'confide.user_validator'.
 *
 * This validator will look for the basic fields (username, email,
 * password), and if the user is unique.
 *
 * In order to use a custom validator:
 *     // MyOwnValidator.php
 *     class MyOwnValidator implements UserValidatorInterface {
 *         ...
 *     }
 *
 *     // routes.php
 *     ...
 *     App::bind('confide.user_validator', 'MyOwnValidator');
 *
 * @see \Zizaco\Confide\UserValidator
 * @license MIT
 * @package  Zizaco\Confide
 */
class UserValidator implements UserValidatorInterface {

  /**
   * Confide repository instance
   *
   * @var \Zizaco\Confide\RepositoryInterface
   */
  public $repo;

  /**
   * Validation rules for this Validator.
   *
   * @var array
   */
  public $rules = [
    'create' => [
    'username' => 'required|alpha_dash',
    'email'    => 'required|email',
    'password' => 'required|min:4',
    ],
    'update' => [
    'username' => 'required|alpha_dash',
    'email'    => 'required|email',
    'password' => 'required|min:4',
    ]
    ];

  /**
   * Validates the given user. Should check if all the fields are correctly
   * @param  ConfideUserInterface $user Instance to be tested
   * @return boolean                    True if the $user is valid
   */
  public function validate(ConfideUserInterface $user, $ruleset = 'create')
  {
    // Set the $repo as a ConfideRepository object
    $this->repo = App::make('confide.repository');

    // Set $user->errors as a MessageBag object
    $user->errors = App::make('Illuminate\Support\MessageBag');

    // Validate object
    $result = $this->validatePassword($user) &&
      $this->validateIsUnique($user) &&
      $this->validateAttributes($user, $ruleset);

    return $result;
  }

  /**
   * Validates the password and password_confirmation of the given
   * user
   * @param  ConfideUserInterface $user
   * @return boolean  True if password is valid
   */
  public function validatePassword(ConfideUserInterface $user)
  {
    $hash = App::make('hash');

    if($user->getOriginal('password') != $user->password) {
      if ($user->password == $user->password_confirmation) {

        // Hashes password and unset password_confirmation field
        $user->password = $hash->make($user->password);
        unset($user->password_confirmation);

        return true;
      } else {
        $this->attachErrorMsg($user->errors, 'confide::confide.alerts.wrong_confirmation', 'password_confirmation');
        return false;
      }
    }

    return true;
  }

  /**
   * Validates if the given user is unique. If there is another
   * user with the same credentials but a different id, this
   * method will return false.
   * @param  ConfideUserInterface $user
   * @return boolean  True if user is unique
   */
  public function validateIsUnique(ConfideUserInterface $user)
  {
    $identity = [
      'username' => $user->username,
      'email'    => $user->email,
      ];

    foreach($identity as $attribute => $value) {

      $similar = $this->repo->getUserByIdentity(array($attribute => $value));

      if (!$similar || $similar->getKey() == $user->getKey()) {
        return true;
      }


      $this->attachErrorMsg($user->errors, 'confide::confide.alerts.duplicated_credentials', $attribute);
      return false;
    }
  }

  /**
   * Uses Laravel Validator in order to check if the attributes of the
   * $user object are valid for the given $ruleset
   * @param  ConfideUserInterface $user
   * @param  string               $ruleset The name of the key in the UserValidator->$rules array
   * @return boolean  True if the attributes are valid
   */
  public function validateAttributes(ConfideUserInterface $user, $ruleset = 'create')
  {
    $attributes = $user->toArray();
    $rules = $this->rules[$ruleset];

    $validator = App::make('validator')
      ->make( $attributes, $rules );

    // Validate and attach errors
    if ($validator->fails()) {
      $this->attachErrorMsg($user->errors, $validator->errors());
      return false;
    } else {
      return true;
    }
  }

  /**
   * Attach errorMsg to a bag accordingly to type and provides key option
   * to allow checking of this locally and get correct Lang msg. 
   * Otherwise works as defaults MessageBag merge method. 
   * @param  \Illuminate\Support\MessageBag $messageBag
   * @param  mixed 			                    $errorMsg The error key and message
   * @param  string     
   * @return void
   */
  public function attachErrorMsg(MessageBag $messageBag, $errorMsg, $key = 'confide')
  {
    if( is_array($errorMsg) || $errorMsg instanceof MessageProviderInterface) {
      $messageBag->merge($errorMsg);
    } else {
      $messageBag->add($key, Lang::get($errorMsg));
    }
  }
}
