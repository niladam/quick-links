<?php

namespace Niladam\QuickLinks;

use Filament\Tables\Table;
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
        if (QuickLinks::isDisabled()) {
            return;
        }

        Table::configureUsing(static fn (Table $table) => $table->description(fn () => QuickLinks::build($table)));
    }
}
