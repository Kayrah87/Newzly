<x-public-layout :publication="$publication" title="Unsubscribed">
    <span class="np-kicker">Subscription ended</span>
    <h2 class="font-display text-2xl font-bold text-ink mt-1 mb-3">You've been unsubscribed</h2>
    <div class="np-rule border-press-600 pt-3">
        <p class="text-ink">
            You will no longer receive {{ $publication->name }}. We're sorry to see you go.
        </p>
    </div>
    <a href="{{ route('public.subscribe', ['publication' => $publication->slug]) }}" class="inline-block mt-4 text-press-600 hover:text-press-700 font-semibold">
        Changed your mind? Subscribe again
    </a>
</x-public-layout>
