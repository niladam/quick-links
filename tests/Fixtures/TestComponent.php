<?php

namespace Niladam\QuickLinks\Tests\Fixtures;

use Filament\Support\Contracts\TranslatableContentDriver;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;

/**
 * A stand-in for a Filament resource page: a Livewire component with a table,
 * plus the getResource()/getModel() pair QuickLinks reads.
 */
class TestComponent extends Component implements HasTable
{
    use InteractsWithTable;

    public function getResource(): string
    {
        return TestResource::class;
    }

    public function getModel(): string
    {
        return TestModel::class;
    }

    public function table(Table $table): Table
    {
        return $table->columns([]);
    }

    /**
     * InteractsWithTable fills $this->table from a Livewire lifecycle hook that
     * never fires for a plain instance, so build it on demand instead.
     */
    public function getTable(): Table
    {
        if (! isset($this->table)) {
            $this->table = $this->table(Table::make($this));
        }

        return $this->table;
    }

    public function makeFilamentTranslatableContentDriver(): ?TranslatableContentDriver
    {
        return null;
    }

    public function render(): string
    {
        return '<div></div>';
    }
}
