@props(['value'])

<label {{ $attributes->merge(['class' => 'np-label']) }}>
    {{ $value ?? $slot }}
</label>
