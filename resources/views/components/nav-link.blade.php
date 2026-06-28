@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-press-600 text-sm font-bold uppercase tracking-wide leading-5 text-ink focus:outline-none transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-semibold uppercase tracking-wide leading-5 text-ink-soft hover:text-ink hover:border-ink/30 focus:outline-none focus:text-ink focus:border-ink/30 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
