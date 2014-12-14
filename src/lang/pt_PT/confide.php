<?php

return array(

    'username' => 'Nome de utilizador',
    'password' => 'Senha',
    'password_confirmation' => 'Confirmar senha',
    'e_mail' => 'Email',
    'username_e_mail' => 'Nome de utilizador ou email',

    'signup' => array(
        'title' => 'Registar',
        'desc' => 'Registar nova conta',
        'confirmation_required' => 'Confirmação necessária',
        'submit' => 'Criar nova conta',
    ),

    'login' => array(
        'title' => 'Iniciar sessão',
        'desc' => 'Insira os seus dados',
        'forgot_password' => '(recuperar senha)',
        'remember' => 'Continuar ligado',
        'submit' => 'Iniciar sessão',
    ),

    'forgot' => array(
        'title' => 'Recuperar senha',
        'submit' => 'Continuar',
    ),

    'alerts' => array(
        'account_created' => 'A sua conta foi criada com sucesso.',
        'instructions_sent' => 'Aceda ao seu email para saber como confirmar a sua conta.',
        'too_many_attempts' => 'Demasiadas tentativas. Tente novamente daqui a alguns minutos.',
        'wrong_credentials' => 'Nome de utilizador, email ou senha incorretos.',
        'not_confirmed' => 'A sua conta pode não estar confirmada. Aceda aos seu email para obter o link de confirmação',
        'confirmation' => 'A sua conta foi confirmada! Pode agora iniciar sessão.',
        'password_confirmation' => 'As senhas não coincidem.', 
        'wrong_confirmation' => 'Código de confirmação errado.',
        'password_forgot' => 'A informação para recuperação de senha foi enviada para o seu email.',
        'wrong_password_forgot' => 'Utilizador não encontrado.',
        'password_reset' => 'A senha foi alterada com sucesso.',
        'wrong_password_reset' => 'Senha inválida. Tente novamente',
        'wrong_token' => 'O "token" para recuperação de senha não é válido..',
        'duplicated_credentials' => 'Os dados fornecidos já estão em utilização. Tente com outras credenciais.',
    ),

    'email' => array(
        'account_confirmation' => array(
            'subject' => 'Confirmação de conta',
            'greetings' => 'Olá :name',
            'body' => 'Aceda ao link abaixo para confirmar a sua conta',
            'farewell' => 'Cumprimentos',
        ),

        'password_reset' => array(
            'subject' => 'Reposição de senha',
            'greetings' => 'Olá :name',
            'body' => 'Aceda ao seguinte link para alterar a sua senha',
            'farewell' => 'Cumprimentos',
        ),
    ),

);