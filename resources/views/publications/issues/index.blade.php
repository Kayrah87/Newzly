<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <span class="np-kicker">{{ $publication->name }}</span>
                <h2 class="font-display text-3xl font-black text-ink leading-tight">
                    {{ __('Issues') }}
                </h2>
            </div>
            @can('update', $publication)
                <a href="{{ route('publications.issues.create', $publication) }}" class="np-btn-primary">
                    Create Issue
                </a>
            @endcan
        </div>
        <x-breadcrumbs :items="[
            ['label' => 'Publications', 'url' => route('publications.index')],
            ['label' => $publication->name, 'url' => route('publications.show', $publication)],
            ['label' => 'Issues', 'url' => null],
        ]" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 px-4">
            @if(session('success'))
                <div class="mb-4 border-l-4 border-press-600 bg-press-50 text-ink px-4 py-3 font-semibold">
                    {{ session('success') }}
                </div>
            @endif

            <div class="np-card">
                <div class="p-6">
                    {{-- Search --}}
                    <form method="GET" action="{{ route('publications.issues.index', $publication) }}" class="mb-6 flex flex-wrap items-end gap-3">
                        <input type="hidden" name="sort" value="{{ $sort }}">
                        <input type="hidden" name="direction" value="{{ $direction }}">
                        <div class="flex-1 min-w-[16rem]">
                            <x-input-label for="search" :value="__('Search issues')" />
                            <x-text-input id="search" class="block mt-1 w-full" type="search" name="search"
                                :value="$search" placeholder="Title, coverage, or number…" />
                        </div>
                        <x-primary-button>{{ __('Search') }}</x-primary-button>
                        @if($search !== '')
                            <a href="{{ route('publications.issues.index', ['publication' => $publication, 'sort' => $sort, 'direction' => $direction]) }}"
                               class="np-btn-ghost">Clear</a>
                        @endif
                    </form>

                    @if($issues->total() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="text-left text-xs font-bold text-ink-soft uppercase tracking-wider border-b-2 border-ink">
                                        <th class="py-2 pr-4">
                                            <x-sort-header column="title" :sort="$sort" :direction="$direction">Title</x-sort-header>
                                        </th>
                                        <th class="py-2 pr-4">
                                            <x-sort-header column="status" :sort="$sort" :direction="$direction">Status</x-sort-header>
                                        </th>
                                        <th class="py-2 pr-4">
                                            <x-sort-header column="issue_number" :sort="$sort" :direction="$direction">No.</x-sort-header>
                                        </th>
                                        <th class="py-2 pr-4">Coverage</th>
                                        <th class="py-2 pr-4">
                                            <x-sort-header column="release_date" :sort="$sort" :direction="$direction">Release</x-sort-header>
                                        </th>
                                        <th class="py-2 pr-4">
                                            <x-sort-header column="created_at" :sort="$sort" :direction="$direction">Created</x-sort-header>
                                        </th>
                                        <th class="py-2 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-ink/10">
                                    @foreach($issues as $issue)
                                        <tr>
                                            <td class="py-3 pr-4">
                                                <a href="{{ route('publications.issues.show', [$publication, $issue]) }}"
                                                   class="font-display font-bold text-ink hover:text-press-600">{{ $issue->title }}</a>
                                            </td>
                                            <td class="py-3 pr-4">
                                                <span class="np-badge-{{ $issue->status === 'sent' ? 'press' : 'ink' }}">{{ $issue->status }}</span>
                                            </td>
                                            <td class="py-3 pr-4 text-ink-soft">{{ $issue->issue_number ?? '—' }}</td>
                                            <td class="py-3 pr-4 text-ink-soft">{{ $issue->coverage_label ?? '—' }}</td>
                                            <td class="py-3 pr-4 text-ink-soft whitespace-nowrap">{{ $issue->release_date?->format('M d, Y') ?? '—' }}</td>
                                            <td class="py-3 pr-4 text-ink-soft whitespace-nowrap">{{ $issue->created_at->format('M d, Y') }}</td>
                                            <td class="py-3 text-right space-x-3 whitespace-nowrap text-sm font-semibold">
                                                <a href="{{ route('publications.issues.show', [$publication, $issue]) }}" class="text-press-600 hover:text-press-700">View</a>
                                                @can('update', $publication)
                                                    <a href="{{ route('publications.issues.edit', [$publication, $issue]) }}" class="text-ink-soft hover:text-ink">Edit</a>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $issues->links() }}
                        </div>
                    @elseif($search !== '')
                        <div class="border border-dashed border-ink/15 px-4 py-8 text-center text-ink-soft">
                            No issues match “{{ $search }}”.
                        </div>
                    @else
                        <p class="text-ink-soft text-center py-8">No issues created yet. Create your first issue to get started!</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
