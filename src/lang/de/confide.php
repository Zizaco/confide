<?php

return array(

    'username' => 'Benutzername',
    'password' => 'Passwort',
    'password_confirmation' => 'Passwort bestätigen',
    'e_mail' => 'E-Mail',
    'username_e_mail' => 'Benutzername oder E-Mail',

    'signup' => array(
        'title' => 'Registrieren',
        'desc' => 'Einen neuen Nutzer registrieren',
        'confirmation_required' => 'Bestätigung notwendig',
        'submit' => 'Nutzer erstellen',
    ),

    'login' => array(
        'title' => 'Login',
        'desc' => 'Logindaten eingeben',
        'forgot_password' => '(Passwort vergessen)',
        'remember' => 'Login merken',
        'submit' => 'Login',
    ),

    'forgot' => array(
        'title' => 'Passwort vergessen',
        'submit' => 'Weiter',
    ),

    'alerts' => array(
        'account_created' => 'Your account has been successfully created. Please check your email for the instructions on how to confirm your account.',
        'too_many_attempts' => 'Too many attempts. Try again in few minutes.',
        'wrong_credentials' => 'Incorrect username, email or password.',
        'not_confirmed' => 'Your account may not be confirmed. Check your email for the confirmation link',
        'confirmation' => 'Your account has been confirmed! You may now login.',
        'wrong_confirmation' => 'Wrong confirmation code.',
        'password_forgot' => 'The information regarding password reset was sent to your email.',
        'wrong_password_forgot' => 'User not found.',
        'password_reset' => 'Your password has been changed successfully.',
        'wrong_password_reset' => 'Invalid password. Try again',
        'wrong_token' => 'The password reset token is not valid.',
        'duplicated_credentials' => 'The credentials provided have already been used. Try with different credentials.',
    ),

    'email' => array(
        'account_confirmation' => array(
            'subject' => 'Anmeldebestätigung',
            'greetings' => 'Hallo :name',
            'body' => 'Bitte folgen Sie dem unten stehenden Link um Ihren Benutzeraccount zu bestätigen.',
            'farewell' => 'Vielen Dank',
        ),

        'password_reset' => array(
            'subject' => 'Passwort zurücksetzen',
            'greetings' => 'Hallo :name',
            'body' => 'Bitte folgen Sie dem unten stehenden Link um Ihr Passwort zu ändern',
            'farewell' => 'Vielen Dank',
        ),
    ),

);
