<?php
return [
    'defaults' => [
        'routes' => [
            'api' => [
                'middleware' => [
                    'api',
                ],
                'prefix' => 'api',
            ],
        ],
    ],
];
