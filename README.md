# Quickly open resource, and other files from within filament panel in your PHPstorm editor.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/niladam/quick-links.svg?style=flat-square)](https://packagist.org/packages/niladam/quick-links)
[![Total Downloads](https://img.shields.io/packagist/dt/niladam/quick-links.svg?style=flat-square)](https://packagist.org/packages/niladam/quick-links)

Quickly open resource, models, and other files from within your FilamentPHP table in your PHPstorm editor.

> [!NOTE]
> The links are rendered through Filament's `tables::header.after` render hook, underneath the table header, so a table that already sets its own `description` keeps it.
>
> That spot lives inside the table's header container, which Filament hides when a table has nothing to put in it. A table with no heading, no description, no header actions, no search, no filters and no column manager therefore won't show the links.

## Installation

You can install the package via composer:

```bash
composer require niladam/quick-links
```

Run the install command:

```bash
php artisan quick-links:install
```

## Configuration

These are the contents of the published config file:

```php

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
```

## Styling

The links are rendered from a Blade view. Publish it if you want to change the markup:

```bash
php artisan vendor:publish --tag=quick-links-views
```

It lands in `resources/views/vendor/quick-links/quick-links.blade.php` and receives `$links` (each with a `url` and a `label`), `$prefix` and `$separator`.

## Conditional disabling

While you can disablee the package entirely by setting the `QUICK_LINKS_ENABLED` environment variable to `false` you can also use a closure to conditionally disable it.

#### Somewhere in a Service Provider..

```php
use Niladam\QuickLinks\Facades\QuickLinks;

// Disable for the user with ID 1
QuickLinks::disableIf(fn() => auth()->id() === 1);

// Disable for a specific role:
QuickLinks::disableIf(fn() => auth()->user()->hasRole('moderator'));
```

#### Disabling for a specific resource using code:

```php
use Niladam\QuickLinks\Facades\QuickLinks;

QuickLinks::disableOn(App\Filament\Resources\OrderResource::class);
```

#### Disabling quicklinks on a specific resource(s) using config

Simply add the FQCN(s) (fully qualified class name) to your resource in the `quick-links.disabled` config option.

## Testing

```bash
composer test
```

The suite runs against Filament v3, v4 and v5.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

Upgrading from v1? See [UPGRADING](UPGRADING.md).

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Madalin Tache](https://github.com/niladam)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
