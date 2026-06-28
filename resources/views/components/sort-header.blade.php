@props([
    'column',
    'sort' => null,
    'direction' => 'desc',
])

@php
    $isActive = $sort === $column;
    $nextDirection = $isActive && $direction === 'asc' ? 'desc' : 'asc';
    // Preserve other query params (e.g. search); drop page so re-sorting starts on page 1.
    $url = request()->fullUrlWithQuery(['sort' => $column, 'direction' => $nextDirection, 'page' => null]);
@endphp

<a href="{{ $url }}"
   {{ $attributes->merge(['class' => 'group inline-flex items-center gap-1 ' . ($isActive ? 'text-ink' : 'hover:text-ink')]) }}
   aria-sort="{{ $isActive ? ($direction === 'asc' ? 'ascending' : 'descending') : 'none' }}">
    {{ $slot }}
    <span class="text-ink/50" aria-hidden="true">
        @if($isActive)
            {{ $direction === 'asc' ? '▲' : '▼' }}
        @else
            <span class="opacity-0 transition group-hover:opacity-60">↕</span>
        @endif
    </span>
</a>
