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

    /**
     * Add your resources here that you want to disable the quick links for.
     *
     * Please make sure to add the FQCN of your resource here.
     *
     * Eg: \App\Filament\Resources\OrderResource::class
     */
    'disabled' => [
        //
    ],

    /**
     * Add your files here that you want to enable quick links for.
     *
     * These will be added at the after the resource links.
     *
     * Please make sure to add the full path to your file here.
     *
     * Missing files will be ignored.
     *
     * Eg:
     *      base_path('config/quick-links.php') => 'quick config'
     *      will generate a link with the name 'quick config'
     *      and open the file at base_path('config/quick-links.php')
     */
    'files' => [
        // base_path('config/quick-links.php') => 'quick config',
    ],
];
