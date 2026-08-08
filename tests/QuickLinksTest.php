<?php

use Filament\Support\Facades\FilamentView;
use Filament\Tables\Table;
use Filament\Tables\View\TablesRenderHook;
use Livewire\Mechanisms\HandleComponents\HandleComponents;
use Niladam\QuickLinks\Facades\QuickLinks;
use Niladam\QuickLinks\Tests\Fixtures\TestComponent;
use Niladam\QuickLinks\Tests\Fixtures\TestResource;

function quickLinksTable(): Table
{
    return Table::make(new TestComponent);
}

it('builds a phpstorm link from a file path', function () {
    $link = QuickLinks::link('/app/Models/User.php');

    expect($link['link'])->toBe('phpstorm://open?file=' . rawurlencode('/app/Models/User.php'))
        ->and($link['title'])->toBe('User.php');
});

it('resolves a class name to the file it lives in', function () {
    expect(QuickLinks::link(TestResource::class)['title'])->toBe('TestResource.php');
});

/**
 * Read straight off the manager rather than through hasRenderHook(), which
 * Filament only grew in v4.
 */
function registeredHeaderHook(): Closure
{
    $hooks = (fn () => $this->renderHooks)->call(FilamentView::getFacadeRoot());

    return head(head($hooks[TablesRenderHook::HEADER_AFTER]));
}

it('registers a render hook underneath the table header', function () {
    $hooks = (fn () => $this->renderHooks)->call(FilamentView::getFacadeRoot());

    expect($hooks)->toHaveKey(TablesRenderHook::HEADER_AFTER);
});

it('renders nothing when no table component is being rendered', function () {
    expect(registeredHeaderHook()())->toBeNull();
});

it('renders the links into the header hook while a table component renders', function () {
    config()->set('quick-links.links', ['resource' => true, 'model' => false, 'env' => false]);

    // What Livewire::current() reads, so the hook sees a component the way it
    // would mid-render.
    HandleComponents::$componentStack[] = new TestComponent;

    try {
        $html = FilamentView::renderHook(TablesRenderHook::HEADER_AFTER)->toHtml();
    } finally {
        array_pop(HandleComponents::$componentStack);
    }

    expect($html)
        ->toContain('phpstorm://open?file=')
        ->toContain('TestResource.php');
});

it('renders the links through the blade view', function () {
    config()->set('quick-links.links', ['resource' => true, 'model' => false, 'env' => false]);

    $html = QuickLinks::build(quickLinksTable())->toHtml();

    expect($html)
        ->toContain('fi-ql')
        ->toContain('Open in PHPStorm:')
        ->toContain('phpstorm://open?file=')
        ->toContain('TestResource.php');
});

it('only includes the link types enabled in config', function () {
    config()->set('quick-links.links', ['resource' => true, 'model' => false, 'env' => false]);

    $links = QuickLinks::links(quickLinksTable());

    expect($links)->toHaveCount(1)
        ->and($links[0]['label'])->toBe('TestResource.php');
});

it('renders nothing when disabled through config', function () {
    config()->set('quick-links.enabled', false);

    expect(QuickLinks::build(quickLinksTable()))->toBeNull();
});

it('renders nothing when every link type is switched off', function () {
    config()->set('quick-links.links', []);
    config()->set('quick-links.files', []);

    expect(QuickLinks::build(quickLinksTable()))->toBeNull();
});

it('disables when the disableIf closure returns true', function () {
    QuickLinks::disableIf(fn () => true);

    expect(QuickLinks::isDisabled())->toBeTrue();
});

it('stays enabled when the disableIf closure returns false', function () {
    QuickLinks::disableIf(fn () => false);

    expect(QuickLinks::isEnabled())->toBeTrue();
});

it('evaluates the disableIf closure per check rather than once at registration', function () {
    $calls = 0;

    QuickLinks::disableIf(function () use (&$calls) {
        $calls++;

        return false;
    });

    expect($calls)->toBe(0);

    QuickLinks::isEnabled();
    QuickLinks::isEnabled();

    expect($calls)->toBe(2);
});

it('skips a resource disabled through code', function () {
    QuickLinks::disableOn(TestResource::class);

    expect(QuickLinks::build(quickLinksTable()))->toBeNull();
});

it('skips a resource disabled through config', function () {
    config()->set('quick-links.disabled', [TestResource::class]);

    expect(QuickLinks::build(quickLinksTable()))->toBeNull();
});

it('ignores configured files that do not exist', function () {
    config()->set('quick-links.files', ['/does/not/exist.php' => 'missing']);

    expect(QuickLinks::buildFileLinks())->toBe([]);
});

it('labels configured files with the configured title', function () {
    config()->set('quick-links.files', [__FILE__ => 'this test']);

    $links = QuickLinks::buildFileLinks();

    expect($links)->toHaveCount(1)
        ->and($links[0]['label'])->toBe('this test');
});
