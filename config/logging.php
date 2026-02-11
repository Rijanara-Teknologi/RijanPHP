<?php

use Teguh02\Rijanphp\Core\Rijan;

return [

    'default' => env('LOG_CHANNEL', 'single'),

    'channels' => [
        'single' => [
            'driver' => 'single',
            'path' => Rijan::$instance->base_path('storage/logs/rijanphp.log'),
            'level' => 'debug',
        ],
    ],

];
