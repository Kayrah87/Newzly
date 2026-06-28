<x-public-layout :publication="$publication" title="Check your email">
    <span class="np-kicker">Check your inbox</span>
    <h2 class="font-display text-2xl font-bold text-ink mt-1 mb-2">Almost there!</h2>
    <p class="text-ink-soft">
        We've sent a confirmation link to your email address. Please click the link to
        confirm your subscription to {{ $publication->name }}.
    </p>
    <p class="text-ink-soft/70 text-sm mt-4">
        Didn't get it? Check your spam folder, or try subscribing again.
    </p>
</x-public-layout>
