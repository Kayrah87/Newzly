<x-public-layout :publication="$publication" title="Thanks!">
    <h2 class="text-lg font-semibold mb-2">Thanks for your submission! 🙌</h2>
    <p class="text-gray-600">
        Your story has been sent to the {{ $publication->name }} team for review. If it's
        selected, you'll see it in an upcoming issue.
    </p>
    <a href="{{ route('public.submit', ['publication' => $publication->slug]) }}" class="inline-block mt-4 text-indigo-600 hover:text-indigo-800">
        Submit another story
    </a>
</x-public-layout>
