<?php

namespace Niladam\QuickLinks;

use Closure;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
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

        if (in_array($resource, $disabledResources, true)) {
            return true;
        }

        return false;
    }

    public function disableIf(Closure $closure): void
    {
        static::$enabled = $closure();
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
        if (static::$enabled instanceof Closure) {
            return static::$enabled;
        }

        if (static::$enabled === false) {
            return false;
        }

        if (! config('quick-links.enabled', true)) {
            return false;
        }

        return true;
    }

    public function isDisabled(): bool
    {
        return ! $this->isEnabled();
    }

    public function buildResourceLinks(Table $table): array
    {
        $resourceLinks = config('quick-links.links', []);

        if (empty($resourceLinks)) {
            return [];
        }

        $availableLinks = array_filter([
            'resource' => $table->getLivewire()->getResource(),
            'model' => $table->getLivewire()->getModel(),
            'env' => base_path('.env'),
        ], fn ($key) => $resourceLinks[$key] ?? false, ARRAY_FILTER_USE_KEY);

        $links = [];

        foreach ($availableLinks as $path) {
            $links[] = $this->renderLink($path);
        }

        return $links;
    }

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
            $links[] = $this->renderLink($file, $title);
        }

        return $links;
    }

    public function build(Table $table): ?HtmlString
    {
        if ($this->isDisabled()) {
            return null;
        }

        if (! method_exists($table->getLivewire(), 'getResource')) {
            return null;
        }

        if ($this->resourceIsDisabled($table->getLivewire()->getResource())) {
            return null;
        }

        $links = [
            ...$this->buildResourceLinks($table),
            ...$this->buildFileLinks(),
        ];

        if (empty($links)) {
            return null;
        }

        $prefix = config('quick-links.prefix', 'Open in PHPStorm:');
        $separator = config('quick-links.separator', ' &bull; ');
        $linksHtml = implode($separator, $links);

        return new HtmlString("{$prefix} {$linksHtml}");
    }

    protected function renderLink(string $filePath, ?string $linkTitle = null): string
    {
        ['link' => $url, 'title' => $title] = static::link($filePath);

        $title = is_null($linkTitle) ? $title : $linkTitle;

        return sprintf(
            '<a href="%s"><strong>%s</strong></a>',
            $url,
            $title
        );
    }
}
