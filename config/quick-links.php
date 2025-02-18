<?php

return [
    'enabled' => env('QUICK_LINKS_ENABLED', true),

    /**
     * Currently the supported links that are automatically added to your table are:
     *
     * resource - Opens the resource.
     * model    - Opens the model.
     * env      - Opens the env file.
     */
    'links' => [
        'resource' => env('QUICK_LINKS_SHOW_RESOURCE', true),
        'model' => env('QUICK_LINKS_SHOW_MODEL', true),
        'env' => env('QUICK_LINKS_SHOW_ENV', true),
    ],

    'prefix' => env('QUICK_LINKS_PREFIX', 'Open in PHPStorm:'),
    'separator' => env('QUICK_LINKS_SEPARATOR', ' &bull; '),

    'disabled' => [
        // Enter the full path to your resource file here if
        // you need/want to disable it for a specific resource.
        //
        // Eg:
        // \App\Filament\Resources\OrderResource::class,
    ],
];
