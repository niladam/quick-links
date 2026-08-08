<?php

namespace Niladam\QuickLinks\Tests\Fixtures;

/**
 * Stands in for a Filament resource. QuickLinks only ever reflects on the
 * class to find the file it lives in, so it needs no behaviour of its own.
 */
class TestResource
{
    public static function getModel(): string
    {
        return TestModel::class;
    }
}
