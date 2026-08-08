# Upgrading

## From v1.x to v2.0

### Requirements

- PHP 8.2 or higher (was 8.1).
- `filament/tables` `^3.0|^4.0|^5.0` is now a declared dependency. The package
  always needed it, but never asked for it, so Composer may now pull it in or
  refuse to resolve where it previously stayed quiet.

### `disableIf()` behaves as documented

It used to call the closure immediately and store the result, then apply that
result the wrong way round. Following the README:

```php
QuickLinks::disableIf(fn () => auth()->id() === 1);
```

ran while the service provider booted, when `auth()->id()` is still `null`, and
disabled the package for everybody. The closure is now stored and evaluated on
every render, and a closure returning `true` disables the links.

If you inverted your closure to work around the old behaviour, remove the
inversion.

### Links no longer use the table description

They render through Filament's `tables::header.after` render hook, so a table
that sets its own `description()` keeps it. Contributed by
[@grafst](https://github.com/grafst) in
[#18](https://github.com/niladam/quick-links/pull/18).

One consequence: that hook sits inside the table's header container, which
Filament hides when a table has nothing else to show there. A table with no
heading, no description, no header actions, no search, no filters and no column
manager will not display the links.

### Return types

`build()` returns a `View` (typed `?Htmlable`) rather than `?HtmlString`, and
`buildResourceLinks()` and `buildFileLinks()` return arrays shaped
`['url' => ..., 'label' => ...]` instead of HTML strings.

If you called these directly, render the view or read the array keys instead of
concatenating the old strings.

### Customising the markup

The markup now lives in a Blade view rather than being built in PHP:

```bash
php artisan vendor:publish --tag=quick-links-views
```
