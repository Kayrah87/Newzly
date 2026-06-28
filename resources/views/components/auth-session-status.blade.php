@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-semibold text-sm text-press-700']) }}>
        {{ $status }}
    </div>
@endif
