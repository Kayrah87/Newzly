<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <span class="np-kicker">The Masthead</span>
                <h2 class="font-display text-3xl font-black text-ink leading-tight">
                    {{ __('My Publications') }}
                </h2>
            </div>
            <a href="{{ route('publications.create') }}" class="np-btn-primary">
                Create Publication
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 px-4">
            @if(session('success'))
                <div class="mb-4 border-l-4 border-press-600 bg-press-50 text-ink px-4 py-3 font-semibold">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Owned Publications -->
            @if($ownedPublications->count() > 0)
                <div class="np-card mb-6">
                    <div class="p-6">
                        <h3 class="font-display text-xl font-bold mb-4">Publications You Own</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($ownedPublications as $publication)
                                <div class="border border-ink/15 border-t-2 border-t-press-600 bg-white p-4 transition hover:border-ink">
                                    <h4 class="font-display text-lg font-bold mb-2">{{ $publication->name }}</h4>
                                    <p class="text-ink-soft text-sm mb-4">{{ Str::limit($publication->description, 100) }}</p>
                                    <div class="flex space-x-4">
                                        <a href="{{ route('publications.show', $publication) }}" class="text-press-600 hover:text-press-700 font-semibold text-sm">View</a>
                                        <a href="{{ route('publications.edit', $publication) }}" class="text-ink-soft hover:text-ink font-semibold text-sm">Edit</a>
                                        <a href="{{ route('publications.issues.index', $publication) }}" class="text-press-600 hover:text-press-700 font-semibold text-sm">Issues</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- All Publications -->
            <div class="np-card">
                <div class="p-6">
                    <h3 class="font-display text-xl font-bold mb-4">All My Publications</h3>
                    @if($publications->count() > 0)
                        <div class="space-y-4">
                            @foreach($publications as $publication)
                                <div class="border border-ink/15 bg-white p-4">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-display text-lg font-bold">{{ $publication->name }}</h4>
                                            <p class="text-ink-soft text-sm">{{ $publication->description }}</p>
                                            <p class="text-xs text-ink-soft mt-2">
                                                Owner: {{ $publication->owner->name }} |
                                                Role: <span class="np-badge-ink">{{ $publication->members()->where('user_id', auth()->id())->first()?->pivot->role ?? 'N/A' }}</span>
                                            </p>
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="{{ route('publications.show', $publication) }}" class="text-press-600 hover:text-press-700 font-semibold text-sm">View</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            {{ $publications->links() }}
                        </div>
                    @else
                        <p class="text-ink-soft">You don't have any publications yet. Create one to get started!</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
