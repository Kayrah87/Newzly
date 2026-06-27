<x-public-layout :publication="$publication" title="Unsubscribe">
    <h2 class="text-lg font-semibold mb-2">Unsubscribe</h2>
    <p class="text-gray-600 mb-4">
        Are you sure you want to stop receiving {{ $publication->name }} at
        <strong>{{ $subscriber->email }}</strong>?
    </p>

    <form method="POST" action="{{ route('public.unsubscribe.perform', ['publication' => $publication->slug, 'token' => $token]) }}">
        @csrf
        <x-danger-button class="w-full justify-center">{{ __('Yes, unsubscribe me') }}</x-danger-button>
    </form>
</x-public-layout>
