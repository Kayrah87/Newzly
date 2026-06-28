<x-app-layout>
    <x-slot name="header">
        <div>
            <span class="np-kicker">The Newsroom</span>
            <h2 class="font-display text-3xl font-black text-ink leading-tight">
                {{ __('Dashboard') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="np-card border-t-4 border-t-press-600 p-6">
                    <h3 class="np-kicker">My Publications</h3>
                    <p class="mt-2 font-display text-5xl font-black text-ink">{{ Auth::user()->ownedPublications()->count() }}</p>
                    <a href="{{ route('publications.index') }}" class="mt-2 inline-block text-sm font-semibold text-press-600 hover:text-press-700">View all →</a>
                </div>

                <div class="np-card border-t-4 border-t-ink p-6">
                    <h3 class="np-kicker">Collaborating On</h3>
                    <p class="mt-2 font-display text-5xl font-black text-ink">{{ Auth::user()->publications()->count() }}</p>
                    <a href="{{ route('publications.index') }}" class="mt-2 inline-block text-sm font-semibold text-press-600 hover:text-press-700">View all →</a>
                </div>

                <div class="np-card border-t-4 border-t-ink p-6">
                    <h3 class="np-kicker">Stories Written</h3>
                    <p class="mt-2 font-display text-5xl font-black text-ink">{{ Auth::user()->stories()->count() }}</p>
                </div>
            </div>

            <div class="np-card p-6">
                <h3 class="font-display text-xl font-bold mb-4">Quick Actions</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a href="{{ route('publications.create') }}" class="np-btn-primary px-6 py-4">
                        Create New Publication
                    </a>
                    <a href="{{ route('publications.index') }}" class="np-btn-outline px-6 py-4">
                        Browse All Publications
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
