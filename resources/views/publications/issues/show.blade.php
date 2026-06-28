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
        <x-breadcrumbs :items="[
            ['label' => 'Publications', 'url' => route('publications.index')],
            ['label' => $publication->name, 'url' => route('publications.show', $publication)],
            ['label' => 'Issues', 'url' => route('publications.issues.index', $publication)],
            ['label' => $issue->title, 'url' => null],
        ]" />
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
                        @php $confirmedCount = $publication->subscribers()->where('status', 'confirmed')->count(); @endphp
                        <div class="np-card mb-6" x-data="{ scheduling: {{ $errors->has('published_at') ? 'true' : 'false' }} }">
                            <div class="p-6">
                                <h3 class="font-display text-lg font-bold mb-4">Publish &amp; Send</h3>

                                @if($issue->isSent())
                                    <p class="np-badge-press mb-3">Sent</p>
                                    <p class="text-sm text-ink-soft mb-1">
                                        Sent {{ $issue->published_at?->format('M j, Y g:i A') }}
                                    </p>
                                    <p class="text-sm text-ink-soft">
                                        {{ $issue->deliveries()->where('status', 'sent')->count() }} delivered
                                        @php $failed = $issue->deliveries()->where('status', 'failed')->count(); @endphp
                                        @if($failed > 0)<span class="text-press-600 font-semibold">, {{ $failed }} failed</span>@endif
                                    </p>
                                @elseif($issue->status === 'scheduled' && $issue->published_at)
                                    <p class="np-badge-ink mb-3">Scheduled</p>
                                    <p class="text-sm text-ink-soft mb-3">
                                        Sends automatically on
                                        <span class="text-ink font-semibold">{{ $issue->published_at->format('M j, Y g:i A') }}</span>
                                        to {{ $confirmedCount }} confirmed subscriber(s).
                                    </p>
                                    <div class="flex flex-wrap gap-2">
                                        <form method="POST" action="{{ route('publications.issues.send', [$publication, $issue]) }}" onsubmit="return confirm('Send this issue now to all confirmed subscribers?');">
                                            @csrf
                                            <x-primary-button>Send now</x-primary-button>
                                        </form>
                                        <form method="POST" action="{{ route('publications.issues.unschedule', [$publication, $issue]) }}">
                                            @csrf
                                            <x-secondary-button type="submit">Cancel schedule</x-secondary-button>
                                        </form>
                                    </div>
                                @else
                                    <p class="text-sm text-ink-soft mb-3">
                                        {{ $confirmedCount }} confirmed subscriber(s) will receive this issue.
                                    </p>
                                    @if($confirmedCount === 0)
                                        <p class="mb-3 border-l-4 border-ink bg-ink/5 text-ink px-3 py-2 text-sm">
                                            No confirmed subscribers yet — you can still schedule ahead.
                                        </p>
                                    @endif

                                    <div class="flex flex-wrap gap-2">
                                        <form method="POST" action="{{ route('publications.issues.send', [$publication, $issue]) }}" onsubmit="return confirm('Send this issue now to all confirmed subscribers?');">
                                            @csrf
                                            <x-primary-button>Send now</x-primary-button>
                                        </form>
                                        <x-secondary-button type="button" @click="scheduling = ! scheduling">Schedule…</x-secondary-button>
                                    </div>

                                    <div x-show="scheduling" x-cloak class="mt-4 border-t border-ink/10 pt-4">
                                        <form method="POST" action="{{ route('publications.issues.schedule', [$publication, $issue]) }}">
                                            @csrf
                                            <label for="published_at" class="np-label mb-1">Send at</label>
                                            <input type="datetime-local" id="published_at" name="published_at"
                                                   min="{{ now()->format('Y-m-d\TH:i') }}"
                                                   value="{{ old('published_at') }}"
                                                   class="np-input" required>
                                            <x-input-error :messages="$errors->get('published_at')" class="mt-2" />
                                            <div class="mt-3">
                                                <x-primary-button>Schedule send</x-primary-button>
                                            </div>
                                            <p class="mt-2 text-xs text-ink-soft">Sends automatically at this time (checked every minute).</p>
                                        </form>
                                    </div>
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
