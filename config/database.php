<?php

return [
    
    'default' => 'mysql',
    
    'connections' => [
        'mysql' => [
            'read' => array(
                ['host' => '127.0.0.1:3306'],
            ),
            'write' => array(
                'host' => '127.0.0.1:3306'
            ),
            //'port'      => '3306',
            'driver'    => 'mysql',
            'database'  => 'phpkit',
            'username'  => 'root',
            'password'  => '',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => 'kit_'
        ]
    ]
];
