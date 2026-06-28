<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <span class="np-kicker">Publication</span>
                <h2 class="font-display text-3xl font-black text-ink leading-tight">
                    {{ $publication->name }}
                </h2>
            </div>
            <div class="flex items-center space-x-3">
                @can('update', $publication)
                    <a href="{{ route('publications.edit', $publication) }}" class="np-btn-outline">Edit</a>
                @endcan
                <a href="{{ route('publications.issues.index', $publication) }}" class="np-btn-primary">
                    View Issues
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 px-4">
            @if(session('success'))
                <div class="mb-4 border-l-4 border-press-600 bg-press-50 text-ink px-4 py-3 font-semibold">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Publication Info -->
                <div class="md:col-span-2">
                    <div class="np-card mb-6">
                        <div class="p-6">
                            <div class="flex items-start gap-4 mb-4">
                                @if($publication->logoUrl())
                                    <img src="{{ $publication->logoUrl() }}" alt="{{ $publication->name }} logo" class="h-16 w-16 object-contain border border-ink/15">
                                @endif
                                <h3 class="font-display text-lg font-bold pt-1">Publication Information</h3>
                            </div>
                            <div class="space-y-3">
                                <div>
                                    <span class="text-ink-soft font-semibold">Description:</span>
                                    <p class="text-ink">{{ $publication->description ?? 'No description provided' }}</p>
                                </div>
                                <div>
                                    <span class="text-ink-soft font-semibold">Owner:</span>
                                    <p class="text-ink">{{ $publication->owner->name }}</p>
                                </div>
                                @if($publication->website_url)
                                    <div>
                                        <span class="text-ink-soft font-semibold">Website:</span>
                                        <a href="{{ $publication->website_url }}" target="_blank" rel="noopener" class="text-press-600 hover:text-press-700 font-semibold break-all">{{ $publication->website_url }}</a>
                                    </div>
                                @endif
                                @if(!empty($publication->social_links))
                                    <div>
                                        <span class="text-ink-soft font-semibold">Social:</span>
                                        <span class="space-x-3">
                                            @foreach($publication->social_links as $platform => $url)
                                                <a href="{{ $url }}" target="_blank" rel="noopener" class="text-press-600 hover:text-press-700 font-semibold">{{ \App\Models\Publication::SOCIAL_PLATFORMS[$platform] ?? ucfirst($platform) }}</a>
                                            @endforeach
                                        </span>
                                    </div>
                                @endif
                                @if($publication->from_email)
                                    <div>
                                        <span class="text-ink-soft font-semibold">Sends as:</span>
                                        <p class="text-ink">{{ $publication->from_name ?: $publication->name }} &lt;{{ $publication->from_email }}&gt;</p>
                                    </div>
                                @endif
                                <div>
                                    <span class="text-ink-soft font-semibold">Created:</span>
                                    <p class="text-ink">{{ $publication->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Issues -->
                    <div class="np-card">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="font-display text-lg font-bold">Recent Issues</h3>
                                @can('update', $publication)
                                    <a href="{{ route('publications.issues.create', $publication) }}" class="text-press-600 hover:text-press-700 font-semibold text-sm">Create Issue</a>
                                @endcan
                            </div>
                            @if($publication->issues->count() > 0)
                                <div class="space-y-3">
                                    @foreach($publication->issues->take(5) as $issue)
                                        <div class="border-l-4 border-press-600 bg-ink/5 pl-4 pr-3 py-2">
                                            <a href="{{ route('publications.issues.show', [$publication, $issue]) }}" class="font-display font-bold text-ink hover:text-press-600">
                                                {{ $issue->title }}
                                            </a>
                                            <p class="text-sm text-ink-soft">
                                                @if($issue->status === 'sent')
                                                    <span class="np-badge-press">{{ $issue->status }}</span>
                                                @else
                                                    <span class="np-badge-ink">{{ $issue->status }}</span>
                                                @endif
                                                <span class="ml-1">{{ $issue->created_at->format('M d, Y') }}</span>
                                            </p>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-ink-soft">No issues created yet.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div>
                    <div class="np-card border-t-4 border-t-press-600 mb-6">
                        <div class="p-6">
                            <h3 class="np-kicker mb-4">Quick Stats</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-ink-soft">Total Issues:</span>
                                    <span class="font-display font-bold">{{ $publication->issues->count() }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-ink-soft">Editors:</span>
                                    <span class="font-display font-bold">{{ $publication->editors()->count() }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-ink-soft">Subscribers:</span>
                                    <span class="font-display font-bold">{{ $publication->subscribers()->where('status', 'confirmed')->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @can('update', $publication)
                        <div class="np-card mb-6">
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="font-display text-lg font-bold">Layout &amp; Theme</h3>
                                    <a href="{{ route('publications.structure.edit', $publication) }}" class="text-press-600 hover:text-press-700 font-semibold text-sm">Edit</a>
                                </div>
                                <p class="text-xs text-ink-soft uppercase tracking-wide mb-1">Section order</p>
                                <p class="text-sm text-ink mb-4">
                                    @foreach($publication->structureOrder() as $section)
                                        <span class="font-semibold">{{ \App\Models\Publication::STRUCTURE_LABELS[$section]['label'] ?? ucfirst($section) }}</span>@if(!$loop->last)<span class="text-ink-soft"> → </span>@endif
                                    @endforeach
                                </p>
                                <p class="text-xs text-ink-soft uppercase tracking-wide mb-2">Palette</p>
                                <div class="flex items-center gap-1.5">
                                    @foreach($publication->paletteColors() as $color)
                                        <span class="h-6 w-6 border border-ink/15" style="background: {{ $color }}" title="{{ $color }}"></span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endcan

                    @can('manageSubscribers', $publication)
                        <div class="np-card mb-6">
                            <div class="p-6">
                                <h3 class="font-display text-lg font-bold mb-4">Mailing List</h3>
                                <a href="{{ route('publications.subscribers.index', $publication) }}" class="block text-press-600 hover:text-press-700 font-semibold">
                                    Manage Subscribers
                                </a>
                            </div>
                        </div>
                    @endcan

                    @can('moderateSubmissions', $publication)
                        @php
                            $pendingSubmissions = $publication->stories()
                                ->where('source', 'public')->where('status', 'pending')->count();
                        @endphp
                        <div class="np-card mb-6">
                            <div class="p-6">
                                <h3 class="font-display text-lg font-bold mb-4">Submissions</h3>
                                <a href="{{ route('publications.submissions.index', $publication) }}" class="flex items-center justify-between text-press-600 hover:text-press-700 font-semibold">
                                    <span>Review submissions</span>
                                    @if($pendingSubmissions > 0)
                                        <span class="np-badge-press">{{ $pendingSubmissions }} pending</span>
                                    @endif
                                </a>
                            </div>
                        </div>
                    @endcan

                    @can('manageEditors', $publication)
                        <div class="np-card mb-6">
                            <div class="p-6">
                                <h3 class="font-display text-lg font-bold mb-4">Manage Team</h3>
                                <div class="space-y-2">
                                    <a href="{{ route('publications.editors', $publication) }}" class="block text-press-600 hover:text-press-700 font-semibold">
                                        Manage Editors
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endcan

                    @can('delete', $publication)
                        <div class="np-card border-t-4 border-t-ink">
                            <div class="p-6">
                                <h3 class="font-display text-lg font-bold mb-4 text-press-600">Danger Zone</h3>
                                <form method="POST" action="{{ route('publications.destroy', $publication) }}" onsubmit="return confirm('Are you sure you want to delete this publication? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <x-danger-button type="submit">
                                        Delete Publication
                                    </x-danger-button>
                                </form>
                            </div>
                        </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
