<x-guest-layout>
    <h2 class="text-lg font-semibold mb-2">You've been invited</h2>
    <p class="text-gray-600 mb-4">
        Join <strong>{{ $publication->name }}</strong> as a
        <strong>{{ str_replace('_', ' ', $invitation->role) }}</strong>.
    </p>

    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-3 py-2 rounded text-sm">
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
            <p class="text-sm text-gray-600">
                This invitation is for <strong>{{ $invitation->email }}</strong>, but you're signed in as
                {{ auth()->user()->email }}. Please sign out and sign in with the invited email to accept.
            </p>
            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button type="submit" class="text-indigo-600 hover:text-indigo-800 text-sm">Sign out</button>
            </form>
        @endif
    @else
        <p class="text-sm text-gray-600 mb-4">
            Sign in or create an account with <strong>{{ $invitation->email }}</strong> to accept.
        </p>
        <div class="flex gap-4">
            <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-800">Log in</a>
            <a href="{{ route('register') }}" class="text-indigo-600 hover:text-indigo-800">Create account</a>
        </div>
    @endauth
</x-guest-layout>
