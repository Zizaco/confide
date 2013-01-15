<?php

return array(

    'username' => 'Nome de Usuário',
    'password' => 'Senha',
    'password2' => 'Confirmar senha',
    'e_mail' => 'Email',
    'username_e_mail' => 'Email ou Usuário',

    'signup' => array(
        'title' => 'Cadastrar',
        'desc' => 'Cadastrar nova conta',
        'confirmation_required' => 'Confirmação necessária',
        'submit' => 'Criar nova conta',
    ),

    'login' => array(
        'title' => 'Entrar',
        'desc' => 'Entre suas credenciais',
        'forgot_password' => '(esqueci minha senha)',
        'remamber' => 'Continuar conectado',
        'submit' => 'Entrar',
    ),

    'forgot' => array(
        'title' => 'Esqueci minha senha',
        'submit' => 'Continuar',
    ),

    'alerts' => array(
        'wrong_credentials' => 'Nome de usuário ou senha incorretos.',
        'confirmation' => 'Sua conta foi confirmada! Você pode entrar agora.',
        'wrong_confirmation' => 'Código de confirmação incorreto.',
        'password_reset' => 'Um novo password foi enviado ao seu e-mail.',
        'wrong_password_reset' => 'Usuário não encontrado.',
    ),

    'email' => array(
        'account_confirmation' => array(
            'subject' => 'Confirmação de conta',
            'greetings' => 'Olá :name',
            'body' => 'Por favor, acesse o link abaixo para confirmar a sua conta',
            'farewell' => 'Att',
        ),

        'password_reset' => array(
            'subject' => 'Troca de senha',
            'greetings' => 'Olá :name',
            'body' => 'Sua senha foi alterada pada :password',
            'farewell' => 'Att',
        ),
    ),

);
