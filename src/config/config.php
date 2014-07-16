<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | Database Connection
    |--------------------------------------------------------------------------
    |
    | Defines which database connection to use when 
    | looking for the users table. The default null value uses the default connection.
    | To use a connection besides the default define it like:
    | 'connection' => 'myConnection'
    |
    */
    'connection' => null,

    /*
    |--------------------------------------------------------------------------
    | Login Throttle
    |--------------------------------------------------------------------------
    |
    | Defines how many login failed tries may be done within
    | the 'throttle_time_period', which is in minutes.
    |
    */

    'throttle_limit' => 9,
    'throttle_time_period' => 2,

    /*
    |--------------------------------------------------------------------------
    | Login Throttle Field
    |--------------------------------------------------------------------------
    |
    | Login throttle is done using the remote ip address
    | and a provided credential. Email and username are likely values.
    |
    | Default: email
    |
    */
    'login_cache_field' => 'email',

    /*
    |--------------------------------------------------------------------------
    | Form Views
    |--------------------------------------------------------------------------
    |
    | The VIEWS used to render forms with Confide methods:
    | makeLoginForm, makeSignupForm, makeForgotPasswordForm
    | and makeResetPasswordForm.
    |
    | By default, the out of the box confide views are used
    | but you can create your own forms and replace the view
    | names here. For example
    |
    |  // To use app/views/user/signup.blade.php:
    |
    | 'signup_form' => 'user.signup'
    |
    |
    */
    'login_form' =>             'confide::login',
    'signup_form' =>            'confide::signup',
    'forgot_password_form' =>   'confide::forgot_password',
    'reset_password_form' =>    'confide::reset_password',

    /*
    |--------------------------------------------------------------------------
    | Email Views
    |--------------------------------------------------------------------------
    |
    | The VIEWS used to email messages for some Confide events:
    |
    | By default, the out of the box confide views are used
    | but you can create your own forms and replace the view
    | names here. For example
    |
    |  // To use app/views/email/confirmation.blade.php:
    |
    | 'email_account_confirmation' => 'email.confirmation'
    |
    |
    */

    'email_reset_password' =>       'confide::emails.passwordreset', // with $user and $token.
    'email_account_confirmation' => 'confide::emails.confirm', // with $user

    /*
    |--------------------------------------------------------------------------
    | Signup (create) Cache
    |--------------------------------------------------------------------------
    |
    | By default you will only can only register once every 2 hours
    | (120 minutes) because you are not able to receive a registration
    | email more often then that.
    |
    | You can adjust that limitation here, set to 0 for no caching.
    | Time is in minutes.
    |
    |
    */
    'signup_cache' => 120,
    
    /*
    |--------------------------------------------------------------------------
    | Signup E-mail and confirmation (true or false)
    |--------------------------------------------------------------------------
    |
    | By default a signup e-mail will be send by the system, however if you
    | do not want this to happen, change the line below in false and handle
    | the confirmation using another technique, for example by using the IPN
    | from a payment-processor. Very usefull for websites offering products.
    |
    | signup_email:
    | is for the transport of the email, true or false
    | If you want to use an IPN to trigger the email, then set it to false
    |
    | signup_confirm:
    | is to decide of a member needs to be confirmed before he is able to login
    | so when you set this to true, then a member has to be confirmed before
    | he is able to login, so if you want to use an IPN for confirmation, be 
    | sure that the ipn process also changes the confirmed flag in the member
    | table, otherwise they will not be able to login after the payment.
    |
    */
    'signup_email'      => true,
    'signup_confirm'    => true,

);
