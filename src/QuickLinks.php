<?php

namespace Niladam\QuickLinks;

use Closure;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use ReflectionClass;

class QuickLinks
{
    public static array $disabled = [];

    public static bool | Closure $enabled = true;

    public function disableOn(string $resource): void
    {
        static::$disabled[$resource] = true;
    }

    public function resourceIsDisabled(string $resource): bool
    {
        if (isset(static::$disabled[$resource])) {
            return true;
        }

        $disabledResources = config('quick-links.disabled', []);

        return in_array($resource, $disabledResources, true);
    }

    /**
     * Store the closure rather than its result, so it is evaluated once per
     * render. Calling it here would run it while the service provider boots,
     * before there is a request to decide against - auth() has no user yet.
     */
    public function disableIf(Closure $closure): void
    {
        static::$enabled = static fn (): bool => ! $closure();
    }

    /**
     * Create a "phpstorm://" link from a file path or class name.
     */
    public function link(string $filePath): array
    {
        // If $filePath is a class, get its file name
        $file = class_exists($filePath)
            ? (new ReflectionClass($filePath))->getFileName()
            : $filePath;

        // Normalize Windows paths to forward slashes (required by PhpStorm)
        $file = str_replace('\\', '/', $file);

        // Encode the path for safe use in a URL
        $encodedPath = rawurlencode($file);

        return ['link' => "phpstorm://open?file={$encodedPath}", 'title' => basename($file)];
    }

    public function isEnabled(): bool
    {
        if (! config('quick-links.enabled', true)) {
            return false;
        }

        $enabled = static::$enabled;

        if ($enabled instanceof Closure) {
            $enabled = $enabled();
        }

        return (bool) $enabled;
    }

    public function isDisabled(): bool
    {
        return ! $this->isEnabled();
    }

    /**
     * @return array<int, array{url: string, label: string}>
     */
    public function buildResourceLinks(Table $table): array
    {
        $resourceLinks = config('quick-links.links', []);

        if (empty($resourceLinks)) {
            return [];
        }

        $livewire = $table->getLivewire();

        $availableLinks = array_filter([
            'resource' => $livewire->getResource(),
            'model' => $livewire->getModel(),
            'env' => base_path('.env'),
        ], fn ($key) => $resourceLinks[$key] ?? false, ARRAY_FILTER_USE_KEY);

        $links = [];

        foreach ($availableLinks as $path) {
            $links[] = $this->toLink($path);
        }

        return $links;
    }

    /**
     * @return array<int, array{url: string, label: string}>
     */
    public function buildFileLinks(): array
    {
        $files = config('quick-links.files', []);

        if (empty($files)) {
            return [];
        }

        $validFiles = array_filter(
            $files,
            'file_exists',
            ARRAY_FILTER_USE_KEY
        );

        $links = [];

        foreach ($validFiles as $file => $title) {
            $links[] = $this->toLink($file, $title);
        }

        return $links;
    }

    /**
     * The links for a table, or an empty array when they are switched off.
     *
     * @return array<int, array{url: string, label: string}>
     */
    public function links(Table $table): array
    {
        if ($this->isDisabled()) {
            return [];
        }

        $livewire = $table->getLivewire();

        if (! method_exists($livewire, 'getResource')) {
            return [];
        }

        if ($this->resourceIsDisabled($livewire->getResource())) {
            return [];
        }

        return [
            ...$this->buildResourceLinks($table),
            ...$this->buildFileLinks(),
        ];
    }

    public function build(Table $table): ?Htmlable
    {
        $links = $this->links($table);

        if (empty($links)) {
            return null;
        }

        return view('quick-links::quick-links', [
            'links' => $links,
            'prefix' => config('quick-links.prefix', 'Open in PHPStorm:'),
            'separator' => config('quick-links.separator', ' &bull; '),
        ]);
    }

    /**
     * @return array{url: string, label: string}
     */
    protected function toLink(string $filePath, ?string $linkTitle = null): array
    {
        ['link' => $url, 'title' => $title] = $this->link($filePath);

        return ['url' => $url, 'label' => $linkTitle ?? $title];
    }
}
