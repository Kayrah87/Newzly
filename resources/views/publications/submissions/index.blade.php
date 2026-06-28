<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <span class="np-kicker">{{ $publication->name }}</span>
                <h2 class="font-display text-3xl font-black text-ink leading-tight">
                    Submissions
                </h2>
            </div>
            <a href="{{ route('publications.show', $publication) }}" class="text-press-600 hover:text-press-700 font-semibold">← Back to publication</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 px-4">
            @if(session('success'))
                <div class="mb-4 border-l-4 border-press-600 bg-press-50 text-ink px-4 py-3 font-semibold">
                    {{ session('success') }}
                </div>
            @endif

            <div class="np-card mb-6">
                <div class="p-6">
                    <h3 class="font-display text-lg font-bold mb-2">Public submission link</h3>
                    <p class="text-sm text-ink-soft mb-2">Share this so readers can submit stories and photos.</p>
                    <input type="text" readonly value="{{ route('public.submit', ['publication' => $publication->slug]) }}"
                        onclick="this.select()" class="np-input text-sm">
                </div>
            </div>

            @forelse($submissions as $submission)
                <div class="np-card mb-4">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-display text-lg font-bold text-ink">{{ $submission->title }}</h3>
                            <span class="text-xs text-ink-soft/70">{{ $submission->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm text-ink-soft mb-3">
                            From {{ $submission->submitter_name ?: 'Anonymous' }}
                            @if($submission->submitter_email) &lt;{{ $submission->submitter_email }}&gt; @endif
                        </p>

                        @if($submission->images->isNotEmpty())
                            <div class="flex gap-2 mb-3 flex-wrap">
                                @foreach($submission->images as $image)
                                    <img src="{{ $image->url() }}" alt="" class="h-24 w-24 object-cover border border-ink/15">
                                @endforeach
                            </div>
                        @endif

                        <div class="prose max-w-none text-sm mb-4 text-ink">{!! nl2br(e(Str::limit($submission->content, 600))) !!}</div>

                        <div class="flex flex-wrap items-end gap-3 border-t border-ink/10 pt-4">
                            <form method="POST" action="{{ route('publications.submissions.approve', [$publication, $submission]) }}" class="flex items-end gap-2">
                                @csrf
                                @method('PATCH')
                                <div>
                                    <label for="issue_{{ $submission->id }}" class="np-label">Assign to issue</label>
                                    <select id="issue_{{ $submission->id }}" name="issue_id" required class="mt-1 border-ink/25 bg-white text-ink text-sm shadow-sm focus:border-press-500 focus:ring-press-500">
                                        <option value="">Choose…</option>
                                        @foreach($issues as $issue)
                                            <option value="{{ $issue->id }}">{{ $issue->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <x-primary-button>Approve</x-primary-button>
                            </form>

                            <form method="POST" action="{{ route('publications.submissions.reject', [$publication, $submission]) }}" onsubmit="return confirm('Reject this submission?');">
                                @csrf
                                @method('PATCH')
                                <x-danger-button>Reject</x-danger-button>
                            </form>
                        </div>
                        @if($issues->isEmpty())
                            <p class="text-xs text-press-600 mt-2 font-semibold">Create an issue first so you can assign approved submissions to it.</p>
                        @endif
                    </div>
                </div>
            @empty
                <div class="np-card">
                    <div class="p-6">
                        <div class="border border-dashed border-ink/15 px-4 py-8 text-center text-ink-soft">No pending submissions.</div>
                    </div>
                </div>
            @endforelse

            <div class="mt-4">{{ $submissions->links() }}</div>
        </div>
    </div>
</x-app-layout>
