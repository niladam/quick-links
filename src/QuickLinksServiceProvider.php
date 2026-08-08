<?php

namespace Niladam\QuickLinks;

use Filament\Support\Facades\FilamentView;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\View\TablesRenderHook;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\Livewire;
use Niladam\QuickLinks\Facades\QuickLinks;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class QuickLinksServiceProvider extends PackageServiceProvider
{
    public static string $name = 'quick-links';

    public static string $viewNamespace = 'quick-links';

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name(static::$name)
            ->hasViews(static::$viewNamespace)
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->askToStarRepoOnGitHub('niladam/quick-links');
            });

        $configFileName = $package->shortName();

        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }
    }

    public function packageRegistered(): void {}

    public function packageBooted(): void
    {
        /*
         * Rendered underneath the table header rather than through
         * ->description(), so a table that sets its own description keeps it
         * instead of having it overwritten.
         *
         * The hook runs on every render, which is also what lets
         * QuickLinks::disableIf() be evaluated per request.
         */
        FilamentView::registerRenderHook(
            TablesRenderHook::HEADER_AFTER,
            static function (): ?Htmlable {
                $livewire = Livewire::current();

                if (! $livewire instanceof HasTable) {
                    return null;
                }

                return QuickLinks::build($livewire->getTable());
            },
        );
    }
}
