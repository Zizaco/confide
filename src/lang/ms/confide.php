<?php

return array(

    'username' => 'Nama Pengguna',
    'password' => 'Kata Laluan',
    'password_confirmation' => 'Sah Kata Laluan',
    'e_mail' => 'Emel',
    'username_e_mail' => 'Nama Pengguna atau Emel',

    'signup' => array(
        'title' => 'Daftar',
        'desc' => 'Daftar akaun baharu',
        'confirmation_required' => 'Pengesahan diperlukan',
        'submit' => 'Cipta akaun baharu',
    ),

    'login' => array(
        'title' => 'Log Masuk',
        'desc' => 'Masukkan identiti anda',
        'forgot_password' => '(lupa kata laluan)',
        'remember' => 'Ingati Saya',
        'submit' => 'Log Masuk',
    ),

    'forgot' => array(
        'title' => 'Lupa kata laluan',
        'submit' => 'Teruskan',
    ),

    'alerts' => array(
        'account_created' => 'Akaun anda telah berjaya dicipta.',
        'instructions_sent' => 'Sila periksa emel untuk melihat keadah pengesahkan akaun anda.',
        'too_many_attempts' => 'Terlalu banyak percubaan. Sila cuba sebentar lagi',
        'wrong_credentials' => 'Nama pengguna, email atau kata laluan tidak tepat.',
        'not_confirmed' => 'Akaun anda mungkin tidak disahkan. Sila periksa emel untuk kaedah pengesahan akaun.',
        'confirmation' => 'Akaun anda telah disahkan!. Sila log masuk untuk meneruskan aktiviti.',
        'password_confirmation' => 'Kata laluan tidak serasi.', 
        'wrong_confirmation' => 'Kod pengesahan tidak tepat.',
        'password_forgot' => 'Maklumat tentang set semula kata laluan telah dihantar melalui emel anda.',
        'wrong_password_forgot' => 'Pengguna tidak dijumpai.',
        'password_reset' => 'Kata laluan anda telah berjaya ditukar.',
        'wrong_password_reset' => 'Kata laluan tidak tepat. Sila cuba lagi.',
        'wrong_token' => 'Token set semula kata laluan tidak tepat.',
        'duplicated_credentials' => 'Maklumat identiti telah digunakan. Sila gunakan identiti lain.',
    ),

    'email' => array(
        'account_confirmation' => array(
            'subject' => 'Pengesahan Akaun',
            'greetings' => 'Hai :name',
            'body' => 'Sila akses pautan dibawah untuk mengesahkan akaun anda.',
            'farewell' => 'Terima kasih',
        ),

        'password_reset' => array(
            'subject' => 'Set Semula Kata Laluan',
            'greetings' => 'Hai :name',
            'body' => 'Sila akses pautan dibawah untuk menukar kata laluan anda.',
            'farewell' => 'Terima kasih',
        ),
    ),

);
