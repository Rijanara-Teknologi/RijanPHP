<?php

use Teguh02\Rijanphp\Core\Rijan;

return [

    'default' => env('LOG_CHANNEL', 'single'),

    'channels' => [
        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/rijanphp.log'),
            'level' => 'debug',
        ],
    ],

];
