<?php

return array(

    'username' => 'Utilisateur',
    'password' => 'Mot de passe',
    'password_confirmation' => 'Confirmez le mot de passe',
    'e_mail' => 'Email',
    'username_e_mail' => "Nom d'utilisateur ou Email",

    'signup' => array(
        'title' => "S'enregistrer",
        'desc' => "Créer un nouveau compte",
        'confirmation_required' => 'Confirmation requise',
        'submit' => 'Créer un nouveau compte',
    ),

    'login' => array(
        'title' => 'Connection',
        'desc' => 'Remplir vos identifiants',
        'forgot_password' => '(mot de passe oublié)',
        'remember' => 'Se souvenir de moi',
        'submit' => 'Se connecter',
    ),

    'forgot' => array(
        'title' => 'Mot de passe oublié',
        'submit' => 'Continuer',
    ),

    'alerts' => array(
        'account_created' => 'Votre compte a été crée avec succes. Veuillez vérifier votre boite email pour les instructions sur la confirmation de votre compte.',
        //'too_many_attempts' => 'Too many attempts. Try again in few minutes.',
        'too_many_attempts' => 'Trop de tentatives. Veuillez réessayer dans quelques minutes.',
        //'wrong_credentials' => 'Incorrect username, email or password.',
        'wrong_credentials' => 'Utilisateur, email ou mot de passe incorrect.',
        //'not_confirmed' => 'Your account may not be confirmed. Check your email for the confirmation link',
        'not_confirmed' => "Votre compte n'est pas confirmé. Veuillez vérifier vos emails pour le lien de confirmation.",
        //'confirmation' => "Your account has been confirmed! You may now login.",
        'confirmation' => "Votre compte a été confirmé avec succes. Vous pouvez désormais vous connecter.",
        //'wrong_confirmation' => 'Wrong confirmation code.',
        'wrong_confirmation' => 'Code de confirmation incorrect',
        //'password_forgot' => 'The information regarding password reset was sent to your email.',
        'password_forgot' => 'Les informations de réinitialisation vous ont été envoyé par email.',
        //'wrong_password_forgot' => 'User not found.',
        'wrong_password_forgot' => 'Utilisateur inconnu.',
        //'password_reset' => 'Your password has been changed successfully.',
        'password_reset' => 'Votre mot de passe a été modifié avec succes.',
        //'wrong_password_reset' => 'Invalid password. Try again',
        'wrong_password_reset' => 'Mot de passe incorrect. Veuillez réessayer',
        //'wrong_token' => 'The password reset token is not valid.',
        'wrong_token' => 'Le token de réinitialisation du mot de passe est incorrect.',
        //'duplicated_credentials' => 'The credentials provided have already been used. Try with different credentials.',
        'duplicated_credentials' => "Les identifiants donnés sont déja utilisés. Veuillez réessayer avec d'autres identifiants.",
    ),

    'email' => array(
        'account_confirmation' => array(
            //'subject' => 'Account Confirmation',
            'subject' => 'Confirmation du compte',
            'greetings' => 'Bonjour :name',
            //'body' => 'Please access the link below to confirm your account.',
            'body' => 'Veuillez cliquer sur le lien ci-dessous pour confirmer votre compte.',
            'farewell' => 'Bien à vous.',
        ),

        'password_reset' => array(
            'subject' => 'Réinitialisation du mot de passe',
            'greetings' => 'Bonjour :name',
            'body' => 'Veuillez cliquer sur le lien ci-dessous pour réinitialiser votre mot de passe.',
            'farewell' => 'Bien à vous.',
        ),
    ),

);
