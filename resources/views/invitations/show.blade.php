<x-guest-layout>
    <span class="np-kicker">You've been invited</span>
    <h2 class="font-display text-2xl font-black text-ink leading-tight mt-1 mb-3">Join the team</h2>
    <p class="text-ink-soft mb-4">
        Join <strong class="text-ink">{{ $publication->name }}</strong> as a
        <span class="np-badge-press">{{ str_replace('_', ' ', $invitation->role) }}</span>.
    </p>

    @if(session('error'))
        <div class="mb-4 border-l-4 border-ink bg-ink/5 text-ink px-4 py-3 font-semibold text-sm">
            {{ session('error') }}
        </div>
    @endif

    @auth
        @if(auth()->user()->email === $invitation->email)
            <form method="POST" action="{{ route('invitations.accept', $invitation->token) }}">
                @csrf
                <x-primary-button class="w-full justify-center">Accept invitation</x-primary-button>
            </form>
        @else
            <p class="text-sm text-ink-soft">
                This invitation is for <strong class="text-ink">{{ $invitation->email }}</strong>, but you're signed in as
                {{ auth()->user()->email }}. Please sign out and sign in with the invited email to accept.
            </p>
            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button type="submit" class="text-press-600 hover:text-press-700 font-semibold text-sm">Sign out</button>
            </form>
        @endif
    @else
        <p class="text-sm text-ink-soft mb-4">
            Sign in or create an account with <strong class="text-ink">{{ $invitation->email }}</strong> to accept.
        </p>
        <div class="flex gap-4">
            <a href="{{ route('login') }}" class="text-press-600 hover:text-press-700 font-semibold">Log in</a>
            <a href="{{ route('register') }}" class="text-press-600 hover:text-press-700 font-semibold">Create account</a>
        </div>
    @endauth
</x-guest-layout>
