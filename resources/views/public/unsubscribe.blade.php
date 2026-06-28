<x-public-layout :publication="$publication" title="Unsubscribe">
    <span class="np-kicker">Manage subscription</span>
    <h2 class="font-display text-2xl font-bold text-ink mt-1 mb-2">Unsubscribe</h2>
    <p class="text-ink-soft mb-4">
        Are you sure you want to stop receiving {{ $publication->name }} at
        <strong class="text-ink">{{ $subscriber->email }}</strong>?
    </p>

    <form method="POST" action="{{ route('public.unsubscribe.perform', ['publication' => $publication->slug, 'token' => $token]) }}">
        @csrf
        <x-danger-button class="w-full justify-center">{{ __('Yes, unsubscribe me') }}</x-danger-button>
    </form>
</x-public-layout>
