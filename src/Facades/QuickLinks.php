<?php

namespace Niladam\QuickLinks\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Niladam\QuickLinks\QuickLinks
 */
class QuickLinks extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Niladam\QuickLinks\QuickLinks::class;
    }
}
