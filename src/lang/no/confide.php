<?php

return array(

    'username' => 'Brukernavn',
    'password' => 'Passord',
    'password_confirmation' => 'Bekreft Passord',
    'e_mail' => 'Epost',
    'username_e_mail' => 'Brukernavn eller Epost',

    'signup' => array(
        'title' => 'Registrer deg',
        'desc' => 'Registrer deg for en ny brukerkonto',
        'confirmation_required' => 'Bekreftelse Påkrevd',
        'submit' => 'Lag ny konto',
    ),

    'login' => array(
        'title' => 'Logg Inn',
        'desc' => 'Skriv inn dine brukerdetaljer',
        'forgot_password' => '(glemt passord)',
        'remember' => 'Husk meg',
        'submit' => 'Logg inn',
    ),

    'forgot' => array(
        'title' => 'Glemt Passord',
        'submit' => 'Fortsett',
    ),

    'alerts' => array(
        'account_created' => 'Din konto ble opprettet.',
        'instructions_sent' => 'Vennligst sjekk din epostkonto for instruksjoner om hvordan du bekrefter din konto.',
        'too_many_attempts' => 'For mange forsøk. Prøv igjen om noen minutter.',
        'wrong_credentials' => 'Feil brukernavn, epost eller passord',
        'not_confirmed' => 'Din brukerkonto er kanskje ikke bekreftet. Sjekk din epostkonto for bekreftelseslenke',
        'confirmation' => 'Din konto er bekreftet! Du kan nå logge inn.',
        'wrong_confirmation' => 'Feil bekreftelseskode.',
        'password_forgot' => 'Informajson om nullstilling av passord ble sendt til din epostadresse.',
        'wrong_password_forgot' => 'Bruker ikke funnet.',
        'password_reset' => 'Passordet ditt ble endret.',
        'wrong_password_reset' => 'Ugyldig passord. Prøv igjen.',
        'wrong_token' => 'Passord nullstillingskode er ugyldig.',
        'duplicated_credentials' => 'Brukerdetaljene du oppga er i bruk. Prøv med andre brukerdetaljer.',
    ),

    'email' => array(
        'account_confirmation' => array(
            'subject' => 'Kontobekreftelse',
            'greetings' => 'Hei :name',
            'body' => 'Vennligst besøk lenken under for å bekrefte din konto.',
            'farewell' => 'Hilsen',
        ),

        'password_reset' => array(
            'subject' => 'Nullstilling av Passord',
            'greetings' => 'Hei :name',
            'body' => 'Besøk følgende lenke for å endre ditt passord.',
            'farewell' => 'Hilsen',
        ),
    ),

);
