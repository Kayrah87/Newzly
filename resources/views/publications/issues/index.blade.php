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
                    @if($issues->count() > 0)
                        <div class="space-y-4">
                            @foreach($issues as $issue)
                                <div class="border border-ink/15 p-4 transition hover:border-ink">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h3 class="font-display text-lg font-bold text-ink mb-2">{{ $issue->title }}</h3>
                                            <div class="flex flex-wrap items-center gap-3 text-sm text-ink-soft">
                                                <span class="np-badge-{{ $issue->status === 'sent' ? 'press' : 'ink' }}">
                                                    {{ $issue->status }}
                                                </span>
                                                @if($issue->issue_number)
                                                    <span>No. {{ $issue->issue_number }}</span>
                                                @endif
                                                @if($issue->coverage_label)
                                                    <span>{{ $issue->coverage_label }}</span>
                                                @endif
                                                @if($issue->release_date)
                                                    <span>{{ $issue->release_date->format('M d, Y') }}</span>
                                                @endif
                                                <span>{{ $issue->created_at->format('M d, Y') }}</span>
                                                @if($issue->published_at)
                                                    <span>Published: {{ $issue->published_at->format('M d, Y') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-4 text-sm font-semibold">
                                            <a href="{{ route('publications.issues.show', [$publication, $issue]) }}" class="text-press-600 hover:text-press-700">View</a>
                                            @can('update', $publication)
                                                <a href="{{ route('publications.issues.edit', [$publication, $issue]) }}" class="text-ink-soft hover:text-ink">Edit</a>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            {{ $issues->links() }}
                        </div>
                    @else
                        <p class="text-ink-soft text-center py-8">No issues created yet. Create your first issue to get started!</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
