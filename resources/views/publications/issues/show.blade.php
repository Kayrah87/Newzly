<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <span class="np-kicker">{{ $publication->name }}</span>
                <h2 class="font-display text-3xl font-black text-ink leading-tight">
                    {{ $issue->title }}
                </h2>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('publications.issues.preview', [$publication, $issue]) }}" target="_blank" rel="noopener" class="np-btn-ghost">Preview</a>
                @can('update', $publication)
                    <a href="{{ route('publications.issues.edit', [$publication, $issue]) }}" class="np-btn-outline">Edit</a>
                @endcan
                @can('manageStories', $publication)
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button type="button" class="np-btn-outline">
                                Add Block
                                <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            @foreach(\App\Models\Block::TYPE_LABELS as $blockType => $blockLabel)
                                <x-dropdown-link :href="route('publications.issues.blocks.create', [$publication, $issue, 'type' => $blockType])">
                                    {{ $blockLabel }}
                                </x-dropdown-link>
                            @endforeach
                        </x-slot>
                    </x-dropdown>
                    <a href="{{ route('publications.issues.stories.create', [$publication, $issue]) }}" class="np-btn-primary">
                        Add Story
                    </a>
                @endcan
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
            @if(session('error'))
                <div class="mb-4 border-l-4 border-ink bg-ink/5 text-ink px-4 py-3 font-semibold">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="md:col-span-2">
                    <!-- Issue Content -->
                    <div class="np-card mb-6">
                        <div class="p-6">
                            <h3 class="font-display text-lg font-bold mb-4">Issue Content</h3>
                            @if($issue->content)
                                <div class="prose max-w-none">
                                    {!! $issue->content !!}
                                </div>
                            @else
                                <p class="text-ink-soft">No content added yet.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Content stream: stories + blocks -->
                    <div class="np-card"
                        @can('manageStories', $publication)
                            x-data="sortable({ url: '{{ route('publications.issues.reorder', [$publication, $issue]) }}' })"
                        @endcan>
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="font-display text-lg font-bold">Content</h3>
                                @can('manageStories', $publication)
                                    @if($items->count() > 1)
                                        <div class="flex items-center gap-2 text-xs">
                                            <span class="text-ink-soft uppercase tracking-wide">Drag to reorder</span>
                                            <span x-show="saved" x-cloak x-transition class="np-badge-press">Order saved</span>
                                            <span x-show="failed" x-cloak x-transition class="np-badge-ink">Save failed</span>
                                        </div>
                                    @endif
                                @endcan
                            </div>
                            @if($items->count() > 0)
                                <div class="space-y-3" x-ref="list"
                                     @dragstart="start($event)" @dragover="over($event)" @drop="drop($event)" @dragend="end($event)">
                                    @foreach($items as $item)
                                        @if($item instanceof \App\Models\Block)
                                            {{-- Events block row --}}
                                            <div class="border border-ink/15 border-l-4 border-l-press-600 bg-white p-4 flex gap-3 transition-shadow hover:shadow-sm"
                                                @can('manageStories', $publication)
                                                    data-sort-item draggable="true" data-sort-id="block:{{ $item->id }}"
                                                @endcan>
                                                @can('manageStories', $publication)
                                                    <span class="text-ink-soft/60 select-none cursor-grab active:cursor-grabbing pt-1 leading-none text-lg" title="Drag to reorder" aria-hidden="true">⠿</span>
                                                @endcan
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex justify-between items-start mb-2 gap-2">
                                                        <h4 class="font-display font-bold text-lg text-ink">{{ $item->title ?: 'Events' }}</h4>
                                                        @can('manageStories', $publication)
                                                            <div class="flex items-center gap-3 text-sm font-semibold shrink-0">
                                                                <a href="{{ route('publications.issues.blocks.edit', [$publication, $issue, $item]) }}" class="text-ink-soft hover:text-ink">Edit</a>
                                                                <form method="POST" action="{{ route('publications.issues.blocks.destroy', [$publication, $issue, $item]) }}" class="inline" onsubmit="return confirm('Remove this block?');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="text-press-600 hover:text-press-700">Delete</button>
                                                                </form>
                                                            </div>
                                                        @endcan
                                                    </div>
                                                    <p class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-ink-soft mb-2">
                                                        <span class="np-badge-press">Events block</span>
                                                        <span class="np-badge-ink">{{ \App\Models\Block::TITLE_STYLES[$item->title_style] ?? $item->title_style }}</span>
                                                        <span>{{ $item->events->count() }} event(s)</span>
                                                    </p>
                                                    @if($item->events->isNotEmpty())
                                                        <ul class="text-sm text-ink-soft list-disc ms-5">
                                                            @foreach($item->events->take(3) as $event)
                                                                <li>{{ $event->date?->format('jS M') }}{{ $event->date ? ' — ' : '' }}{{ $event->name }}</li>
                                                            @endforeach
                                                            @if($item->events->count() > 3)
                                                                <li>+{{ $item->events->count() - 3 }} more…</li>
                                                            @endif
                                                        </ul>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            {{-- Story row --}}
                                            <div class="border border-ink/15 bg-white p-4 flex gap-3 transition-shadow hover:shadow-sm"
                                                @can('manageStories', $publication)
                                                    data-sort-item draggable="true" data-sort-id="story:{{ $item->id }}"
                                                @endcan>
                                                @can('manageStories', $publication)
                                                    <span class="text-ink-soft/60 select-none cursor-grab active:cursor-grabbing pt-1 leading-none text-lg" title="Drag to reorder" aria-hidden="true">⠿</span>
                                                @endcan
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex justify-between items-start mb-2 gap-2">
                                                        <h4 class="font-display font-bold text-lg text-ink">{{ $item->title }}</h4>
                                                        @can('manageStories', $publication)
                                                            <div class="flex items-center gap-3 text-sm font-semibold shrink-0">
                                                                <a href="{{ route('publications.issues.stories.edit', [$publication, $issue, $item]) }}" class="text-ink-soft hover:text-ink">Edit</a>
                                                                <form method="POST" action="{{ route('publications.issues.stories.destroy', [$publication, $issue, $item]) }}" class="inline" onsubmit="return confirm('Are you sure?');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="text-press-600 hover:text-press-700">Delete</button>
                                                                </form>
                                                            </div>
                                                        @endcan
                                                    </div>
                                                    <p class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-ink-soft mb-2">
                                                        <span>By {{ $item->author->name ?? 'Unknown' }}</span>
                                                        <span class="np-badge-ink">{{ str_replace('_', ' ', $item->layout) }}</span>
                                                        <span class="np-badge-{{ $item->status === 'approved' ? 'press' : 'ink' }}">{{ ucfirst($item->status) }}</span>
                                                        @if($item->images->isNotEmpty())
                                                            <span>{{ $item->images->count() }} image(s)</span>
                                                        @endif
                                                    </p>
                                                    <div class="prose max-w-none">
                                                        {!! Str::limit(strip_tags($item->content), 300) !!}
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <p class="text-ink-soft">No content yet. Add a story or an events block to get started!</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div>
                    <div class="np-card mb-6">
                        <div class="p-6">
                            <h3 class="font-display text-lg font-bold mb-4">Issue Details</h3>
                            <div class="space-y-3">
                                <div>
                                    <span class="text-ink-soft font-medium">Status:</span>
                                    <p class="np-badge-{{ $issue->status === 'sent' ? 'press' : 'ink' }} mt-1">
                                        {{ $issue->status }}
                                    </p>
                                </div>
                                @if($issue->issue_number)
                                    <div>
                                        <span class="text-ink-soft font-medium">Issue number:</span>
                                        <p class="text-ink">#{{ $issue->issue_number }}</p>
                                    </div>
                                @endif
                                @if($issue->coverage_label)
                                    <div>
                                        <span class="text-ink-soft font-medium">Coverage:</span>
                                        <p class="text-ink">{{ $issue->coverage_label }}</p>
                                    </div>
                                @endif
                                @if($issue->release_date)
                                    <div>
                                        <span class="text-ink-soft font-medium">Release date:</span>
                                        <p class="text-ink">{{ $issue->release_date->format('M d, Y') }}</p>
                                    </div>
                                @endif
                                <div>
                                    <span class="text-ink-soft font-medium">Created:</span>
                                    <p class="text-ink">{{ $issue->created_at->format('M d, Y') }}</p>
                                </div>
                                @if($issue->published_at)
                                    <div>
                                        <span class="text-ink-soft font-medium">Published:</span>
                                        <p class="text-ink">{{ $issue->published_at->format('M d, Y H:i') }}</p>
                                    </div>
                                @endif
                                <div>
                                    <span class="text-ink-soft font-medium">Stories:</span>
                                    <p class="text-ink">{{ $issue->stories->count() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @can('update', $publication)
                        <div class="np-card mb-6">
                            <div class="p-6">
                                <h3 class="font-display text-lg font-bold mb-4">Send</h3>
                                @if($issue->isSent())
                                    <p class="text-sm text-ink-soft mb-1">
                                        Sent {{ $issue->published_at?->format('M d, Y H:i') }}
                                    </p>
                                    <p class="text-sm text-ink-soft">
                                        {{ $issue->deliveries()->where('status', 'sent')->count() }} delivered
                                        @php $failed = $issue->deliveries()->where('status', 'failed')->count(); @endphp
                                        @if($failed > 0)<span class="text-press-600 font-semibold">, {{ $failed }} failed</span>@endif
                                    </p>
                                @else
                                    <p class="text-sm text-ink-soft mb-3">
                                        {{ $publication->subscribers()->where('status', 'confirmed')->count() }} confirmed subscriber(s) will receive this issue.
                                    </p>
                                    <form method="POST" action="{{ route('publications.issues.send', [$publication, $issue]) }}" onsubmit="return confirm('Send this issue to all confirmed subscribers?');">
                                        @csrf
                                        <x-primary-button>Send to subscribers</x-primary-button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endcan

                    <div class="np-card mb-6">
                        <div class="p-6">
                            <h3 class="font-display text-lg font-bold mb-4">Quick Actions</h3>
                            <div class="space-y-2 text-sm font-semibold">
                                <a href="{{ route('publications.issues.index', $publication) }}" class="block text-press-600 hover:text-press-700">
                                    Back to Issues
                                </a>
                                <a href="{{ route('publications.show', $publication) }}" class="block text-press-600 hover:text-press-700">
                                    View Publication
                                </a>
                            </div>
                        </div>
                    </div>

                    @can('update', $publication)
                        <div class="np-card">
                            <div class="p-6">
                                <h3 class="font-display text-lg font-bold mb-4 text-press-600">Danger Zone</h3>
                                <form method="POST" action="{{ route('publications.issues.destroy', [$publication, $issue]) }}" onsubmit="return confirm('Are you sure you want to delete this issue? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <x-danger-button type="submit">
                                        Delete Issue
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
