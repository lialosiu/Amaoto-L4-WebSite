<?php

return array(

    'default' => 'dev',

    'connections' => array(

        'dev' => array(
            'driver'   => 'sqlite',
            'database' => app_path('database/dev.sqlite'),
            'prefix'   => '',
        ),

    ),

);
