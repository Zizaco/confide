<?php

return array(

    'username' => 'Username',
    'password' => 'Password',
    'password_confirmation' => 'Confirm Password',
    'e_mail' => 'Email',
    'username_e_mail' => 'Username or Email',

    'signup' => array(
        'title' => 'Signup',
        'desc' => 'Signup for new account',
        'confirmation_required' => 'Confirmation required',
        'submit' => 'Create new account',
    ),

    'login' => array(
        'title' => 'Login',
        'desc' => 'Enter your credentials',
        'forgot_password' => '(forgot password)',
        'remamber' => 'Remamber me',
        'submit' => 'Login',
    ),

    'forgot' => array(
        'title' => 'Forgot password',
        'submit' => 'Continue',
    ),

    'alerts' => array(
        'account_created' => 'Your account has been successfuly created. Please check your email for the instructions on how to confirm your account.',
        'wrong_credentials' => 'Incorrect username, email or password.',
        'confirmation' => 'Your account has been confirmed! You may now login.',
        'wrong_confirmation' => 'Wrong confirmation code.',
        'password_reset' => 'A new password has been sent to your email.',
        'wrong_password_reset' => 'User not found.',
    ),

    'email' => array(
        'account_confirmation' => array(
            'subject' => 'Account Confirmation',
            'greetings' => 'Hello :name',
            'body' => 'Please access the link bellow to confirm your account.',
            'farewell' => 'Regards',
        ),

        'password_reset' => array(
            'subject' => 'Password Reset',
            'greetings' => 'Hello :name',
            'body' => 'Your password has been changed to :password',
            'farewell' => 'Regards',
        ),
    ),

);
