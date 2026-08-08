{{--
    Rendered into TablesRenderHook::HEADER_AFTER, underneath the table's own
    header, so a table that already defines a description keeps it.

    The `fi-*` classes are Filament's own. From v4 they carry the styling by
    themselves, while v3 styled the markup with the utility classes sitting
    alongside them, so both are present to cover every supported major.
--}}
<div class="fi-ql fi-ta-header flex flex-col gap-3 p-4 sm:px-6">
    <p class="fi-ql-links fi-ta-header-description text-sm text-gray-600 dark:text-gray-400">
        @if (filled($prefix))
            <span class="fi-ql-prefix">{{ $prefix }}</span>
        @endif

        @foreach ($links as $link)
            @if (! $loop->first){!! $separator !!}@endif<a
                class="fi-ql-link"
                href="{{ $link['url'] }}"
            ><strong>{{ $link['label'] }}</strong></a>
        @endforeach
    </p>
</div>
