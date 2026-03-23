<?php

declare(strict_types=1);

return [
    'db' => [
        'server' => 'tcp:mysqlserver-senati.database.windows.net,1433',
        'database' => 'senati_database',
        'username' => 'Seminario',
        'password' => '{your_password_here}',
        'encrypt' => true,
        'trust_server_certificate' => false,
        'login_timeout' => 30,
    ],
    'app' => [
        'name' => 'Inventario SENATI',
    ],
];
