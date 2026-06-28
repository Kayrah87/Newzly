<x-public-layout :publication="$publication" title="Thanks!">
    <span class="np-kicker">Submission received</span>
    <h2 class="font-display text-2xl font-bold text-ink mt-1 mb-3">Thanks for your submission! 🙌</h2>
    <div class="np-rule border-press-600 pt-3">
        <p class="text-ink">
            Your story has been sent to the {{ $publication->name }} team for review. If it's
            selected, you'll see it in an upcoming issue.
        </p>
    </div>
    <a href="{{ route('public.submit', ['publication' => $publication->slug]) }}" class="inline-block mt-4 text-press-600 hover:text-press-700 font-semibold">
        Submit another story
    </a>
</x-public-layout>
