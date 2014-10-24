<?php

return array(

    'default' => 'dev_mysql',

    'connections' => array(

        'dev' => array(
            'driver' => 'sqlite',
            'database' => app_path('database/dev.sqlite'),
            'prefix' => '',
        ),

        'dev_mysql' => array(
            'driver' => 'mysql',
            'host' => 'localhost',
            'database' => 'amaoto_new',
            'username' => 'amaoto_new',
            'password' => 'amaoto_new',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
        ),

    ),

);
