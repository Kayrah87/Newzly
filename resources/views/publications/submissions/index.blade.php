<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $publication->name }} — Submissions
            </h2>
            <a href="{{ route('publications.show', $publication) }}" class="text-gray-600 hover:text-gray-900">← Back to publication</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-2">Public submission link</h3>
                    <p class="text-sm text-gray-500 mb-2">Share this so readers can submit stories and photos.</p>
                    <input type="text" readonly value="{{ route('public.submit', ['publication' => $publication->slug]) }}"
                        onclick="this.select()" class="block w-full bg-gray-50 border-gray-300 rounded-md text-sm">
                </div>
            </div>

            @forelse($submissions as $submission)
                <div class="bg-white shadow-sm sm:rounded-lg mb-4">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-semibold">{{ $submission->title }}</h3>
                            <span class="text-xs text-gray-400">{{ $submission->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm text-gray-500 mb-3">
                            From {{ $submission->submitter_name ?: 'Anonymous' }}
                            @if($submission->submitter_email) &lt;{{ $submission->submitter_email }}&gt; @endif
                        </p>

                        @if($submission->images->isNotEmpty())
                            <div class="flex gap-2 mb-3 flex-wrap">
                                @foreach($submission->images as $image)
                                    <img src="{{ $image->url() }}" alt="" class="h-24 w-24 object-cover rounded border">
                                @endforeach
                            </div>
                        @endif

                        <div class="prose max-w-none text-sm mb-4">{!! nl2br(e(Str::limit($submission->content, 600))) !!}</div>

                        <div class="flex flex-wrap items-end gap-3 border-t pt-4">
                            <form method="POST" action="{{ route('publications.submissions.approve', [$publication, $submission]) }}" class="flex items-end gap-2">
                                @csrf
                                @method('PATCH')
                                <div>
                                    <label for="issue_{{ $submission->id }}" class="block text-xs text-gray-500">Assign to issue</label>
                                    <select id="issue_{{ $submission->id }}" name="issue_id" required class="mt-1 border-gray-300 rounded-md text-sm">
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
                            <p class="text-xs text-red-600 mt-2">Create an issue first so you can assign approved submissions to it.</p>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-500">No pending submissions.</div>
                </div>
            @endforelse

            <div class="mt-4">{{ $submissions->links() }}</div>
        </div>
    </div>
</x-app-layout>
