<?php

namespace Niladam\QuickLinks;

use Filament\Support\Facades\FilamentView;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\View\TablesRenderHook;
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
        // Rendered below the table header instead of via ->description(), so
        // tables that define their own description keep it — the quick links
        // are appended rather than silently dropped. The enabled check runs
        // per render, so runtime config changes are respected.
        FilamentView::registerRenderHook(
            TablesRenderHook::HEADER_AFTER,
            static function (): ?string {
                if (QuickLinks::isDisabled()) {
                    return null;
                }

                $component = Livewire::current();

                if (! $component instanceof HasTable) {
                    return null;
                }

                $links = QuickLinks::build($component->getTable());

                return $links
                    ? '<div class="quick-links" style="padding: 0.5rem 1rem; font-size: 0.75rem; color: #71717a;">'.$links.'</div>'
                    : null;
            },
        );
    }
}
