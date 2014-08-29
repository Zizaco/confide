<?php

/**
 * This is the Dutch translation for Confide. It has a formal and an informal spelling.
 **/

return array(

    'username' => 'Gebruikersnaam',
    'password' => 'Wachtwoord',
    'password_confirmation' => 'Bevestig wachtwoord',
    'e_mail' => 'E-mail',
    'username_e_mail' => 'Gebruikersnaam of e-mailadres',

    'signup' => array(
        'title' => 'Registreren',
        'desc' => 'Registreer een nieuw account',
        'confirmation_required' => 'Bevestiging vereist',
        'submit' => 'Maak nieuw account',
    ),

    'login' => array(
        'title' => 'Inloggen',
        'desc' => 'Vul je gegevens in',
        'forgot_password' => '(wachtwoord vergeten)',
        'remember' => 'Onthoud mij',
        'submit' => 'Inloggen',
    ),

    'forgot' => array(
        'title' => 'Wachtwoord vergeten',
        'submit' => 'Doorgaan',
    ),

    'alerts' => array(
        'account_created' => 'Je account is succesvol aangemaakt. Controleer je e-mailinbox voor instructies om je account the bevestigen.',
        'too_many_attempts' => 'Te veel pogingen. Probeer het over een enkele minuten nog eens.',
        'wrong_credentials' => 'Verkeerde gebruikersnaam, e-mailadres of wachtwoord.',
        'not_confirmed' => 'Je account is waarschijnlijk niet bevestigd. Controleer je e-mailinbox voor de bevestigingslink.',
        'confirmation' => 'Je account is bevestigd! Je kunt nu inloggen.',
        'wrong_confirmation' => 'Verkeerde bevestigingscode.',
        'password_forgot' => 'De informatie voor het opnieuw instellen van je wachtwoord is verstuurd naar je e-mailadres.',
        'wrong_password_forgot' => 'Gebruiker niet gevonden.',
        'password_reset' => 'Je wachtwoord is succesvol veranderd.',
        'wrong_password_reset' => 'Verkeerd wachtwoord. Probeer het nog eens.',
        'wrong_token' => 'Het token om je wachtwoord opnieuw in te stellen is niet geldig.',
        'duplicated_credentials' => 'De ingevulde gegevens zijn al in gebruik. Probeer het eens met andere gegevens.',
    ),

    'email' => array(
        'account_confirmation' => array(
            'subject' => 'Accountbevestiging',
            'greetings' => 'Hallo :name',
            'body' => 'Klik op de onderstaande link om je account te bevestigen.',
            'farewell' => 'Met vriendelijke groet',
        ),

        'password_reset' => array(
            'subject' => 'Wachtwoord opnieuw instellen',
            'greetings' => 'Hallo :name',
            'body' => 'Klik op de onderstaande link om je wachtwoord opnieuw in te stellen.',
            'farewell' => 'Met vriendelijke groet',
        ),
    ),

);
