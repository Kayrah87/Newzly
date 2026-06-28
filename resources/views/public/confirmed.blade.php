<x-public-layout :publication="$publication" title="Subscription confirmed">
    @if($confirmed)
        <span class="np-kicker">Subscription confirmed</span>
        <h2 class="font-display text-2xl font-bold text-ink mt-1 mb-3">You're subscribed! 🎉</h2>
        <div class="np-rule border-press-600 pt-3">
            <p class="text-ink">
                Thanks for confirming. You'll now receive {{ $publication->name }} in your inbox.
            </p>
        </div>
    @else
        <span class="np-kicker">Link unavailable</span>
        <h2 class="font-display text-2xl font-bold text-ink mt-1 mb-2">This link is invalid or has expired</h2>
        <p class="text-ink-soft">
            The confirmation link couldn't be used — it may have already been confirmed or
            expired. Try subscribing again if you still want to receive {{ $publication->name }}.
        </p>
        <a href="{{ route('public.subscribe', ['publication' => $publication->slug]) }}" class="inline-block mt-4 text-press-600 hover:text-press-700 font-semibold">
            Back to subscribe
        </a>
    @endif
</x-public-layout>
