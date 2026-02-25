<?php
return [
    'host'       => getenv('MAIL_HOST') ?: '',
    'username'   => getenv('MAIL_USERNAME') ?: '',
    'password'   => getenv('MAIL_PASSWORD') ?: '',
    'port'       => (int) (getenv('MAIL_PORT') ?: 587),
    'encryption' => getenv('MAIL_ENCRYPTION') ?: 'tls', // tls or ssl

    'from' => [
        'email' => getenv('MAIL_FROM_EMAIL') ?: '',
        'name'  => getenv('MAIL_FROM_NAME') ?: ''
    ]
];
?>