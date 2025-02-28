<?php
return [
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
        'development' => [
            'adapter' => 'mysql',
            'host' => 'db',
            'name' => 'currency',
            'user' => 'root',
            'pass' => 'secret',
            'port' => 3306,
            'charset' => 'utf8mb4',
        ]
    ]
];