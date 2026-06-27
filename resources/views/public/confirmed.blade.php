<x-public-layout :publication="$publication" title="Subscription confirmed">
    @if($confirmed)
        <h2 class="text-lg font-semibold mb-2 text-green-700">You're subscribed! 🎉</h2>
        <p class="text-gray-600">
            Thanks for confirming. You'll now receive {{ $publication->name }} in your inbox.
        </p>
    @else
        <h2 class="text-lg font-semibold mb-2">This link is invalid or has expired</h2>
        <p class="text-gray-600">
            The confirmation link couldn't be used — it may have already been confirmed or
            expired. Try subscribing again if you still want to receive {{ $publication->name }}.
        </p>
        <a href="{{ route('public.subscribe', ['publication' => $publication->slug]) }}" class="inline-block mt-4 text-indigo-600 hover:text-indigo-800">
            Back to subscribe
        </a>
    @endif
</x-public-layout>
