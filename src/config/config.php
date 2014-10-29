<?php

return array(

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
    | Password reset expiration
    |--------------------------------------------------------------------------
    |
    | By default. A password reset request will expire after 7 hours. With the
    | line below you will be able to customize the duration of the reset
    | requests here.
    |
    */
    'password_reset_expiration' => 7, // hours

    /*
    |--------------------------------------------------------------------------
    | Signup E-mail and confirmation (true or false)
    |--------------------------------------------------------------------------
    |
    | By default a signup e-mail will be send by the system, however if you
    | do not want this to happen, change the line below in false and handle
    | the confirmation using another technique, for example by using the IPN
    | from a payment-processor. Very useful for websites offering products.
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
    'signup_email'   => true,
    'signup_confirm' => true,

    /*
    |--------------------------------------------------------------------------
    | E-Mail queue
    |--------------------------------------------------------------------------
    |
    | Modify the line below to change to which queue Confide will push the
    | email send job.
    | See: http://laravel.com/docs/queues#running-the-queue-listener
    | Confide will use the default queue driver (app/config/queue.php)
    |
    | Warning: This is not the driver that will be used, but to which queue/tube
    | inside the queue driver the job will be placed. For example:
    | By using 'email_queue' => 'emails', with the driver 'beanstalkd' in the
    | You will have to run `php artisan queue:listen --queue=emails`
    |
    */
    'email_queue' => 'default',

);
