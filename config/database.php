<?php

use Illuminate\Support\Str;

return [

    'default' => 'mysql',

    'connections' => [

        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => 'gateway01.ap-southeast-1.prod.aws.tidbcloud.com',
            'port' => '4000',
            'database' => 'staycation-db',
            'username' => '4DYyn4ujWLMYNpK.root',
            'password' => 'QQhLZbWir5XT9sPv', // <--- PASSWORD JUARA (HIJAU)
            'unix_socket' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => '/etc/ssl/certs/ca-certificates.crt',
                PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
            ]) : [],
        ],

    ],

    'migrations' => 'migrations',

];