<x-public-layout :publication="$publication" title="Unsubscribed">
    <h2 class="text-lg font-semibold mb-2">You've been unsubscribed</h2>
    <p class="text-gray-600">
        You will no longer receive {{ $publication->name }}. We're sorry to see you go.
    </p>
    <a href="{{ route('public.subscribe', ['publication' => $publication->slug]) }}" class="inline-block mt-4 text-indigo-600 hover:text-indigo-800">
        Changed your mind? Subscribe again
    </a>
</x-public-layout>
