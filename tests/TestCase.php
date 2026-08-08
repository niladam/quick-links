<?php

namespace Niladam\QuickLinks\Tests;

use Filament\Actions\ActionsServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Infolists\InfolistsServiceProvider;
use Filament\Notifications\NotificationsServiceProvider;
use Filament\QueryBuilder\QueryBuilderServiceProvider;
use Filament\Schemas\SchemasServiceProvider;
use Filament\Support\SupportServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Livewire\LivewireServiceProvider;
use Niladam\QuickLinks\QuickLinks;
use Niladam\QuickLinks\QuickLinksServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        // Both are static, so they would otherwise leak between tests.
        QuickLinks::$disabled = [];
        QuickLinks::$enabled = true;
    }

    protected function getPackageProviders($app): array
    {
        // Filament splits itself differently per major - schemas and the query
        // builder only exist from v4 - so only register what is actually there.
        return array_values(array_filter([
            LivewireServiceProvider::class,
            SupportServiceProvider::class,
            ActionsServiceProvider::class,
            SchemasServiceProvider::class,
            FormsServiceProvider::class,
            InfolistsServiceProvider::class,
            NotificationsServiceProvider::class,
            QueryBuilderServiceProvider::class,
            TablesServiceProvider::class,
            QuickLinksServiceProvider::class,
        ], 'class_exists'));
    }
}
