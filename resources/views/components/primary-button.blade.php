<button {{ $attributes->merge(['type' => 'submit', 'class' => 'np-btn-primary']) }}>
    {{ $slot }}
</button>
