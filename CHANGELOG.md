# Changelog

All notable changes to `quick-links` will be documented in this file.

## v2.0.0 - 2026-08-08

### What's Changed

Adds Filament v4 and v5 support, and moves rendering off the table description onto a render hook so tables that set their own description keep it. See [UPGRADING.md](https://github.com/niladam/quick-links/blob/main/UPGRADING.md).

#### Breaking

* Minimum PHP is now 8.2, up from 8.1
* `filament/tables` `^3.0|^4.0|^5.0` is now a declared dependency — previously imported but never required
* `disableIf()` evaluates its closure per render instead of once at registration, and a closure returning `true` now disables. It previously disabled the package globally
* `buildResourceLinks()` and `buildFileLinks()` return `['url' => ..., 'label' => ...]` arrays instead of HTML strings
* `build()` returns a `View` (`?Htmlable`) instead of `?HtmlString`

#### Added

* Filament v4 and v5 support, alongside v3
* Publishable Blade view for the links — `php artisan vendor:publish --tag=quick-links-views`
* Pest test suite and a CI matrix covering PHP 8.2, 8.3 and 8.4 against Filament v3, v4 and v5

#### Changed

* Links render through the `tables::header.after` render hook rather than the table's `description()`, so an existing description is no longer overwritten
* Markup moved out of PHP into the Blade view, which receives structured links rather than pre-rendered anchors

#### Fixed

* `disableIf()` no longer runs before there is a request to decide against, and no longer applies its result inverted

#### Known limitation

* The render hook sits inside the table header container, which Filament hides when a table has no heading, description, header actions, search, filters or column manager. Links will not appear on such a table

**Full Changelog**: https://github.com/niladam/quick-links/compare/v1.1.2...v2.0.0

## v1.1.2 - 2025-03-24

### What's Changed

* Ensure we do not attempt to build it unless it's a resource by @niladam in https://github.com/niladam/quick-links/pull/5

**Full Changelog**: https://github.com/niladam/quick-links/compare/v1.1.1...v1.1.2

## v1.1.1 - 2025-02-19

### What's Changed

* Remove the issue templates. Update composer.json by @niladam in https://github.com/niladam/quick-links/pull/4

**Full Changelog**: https://github.com/niladam/quick-links/compare/v1.1.0...v1.1.1

## v1.1.0 - 2025-02-19

### What's Changed

* Allow files to be added to the quicklinks by @niladam in https://github.com/niladam/quick-links/pull/3

**Full Changelog**: https://github.com/niladam/quick-links/compare/v1.0.0...v1.1.0

## v1.0.0 - 2025-02-18

Initial release
