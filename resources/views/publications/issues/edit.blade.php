<x-app-layout>
    <x-slot name="header">
        <div>
            <span class="np-kicker">{{ $publication->name }}</span>
            <h2 class="font-display text-3xl font-black text-ink leading-tight">
                {{ __('Edit Issue') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 px-4">
            <div class="np-card">
                <div class="p-6">
                    <form method="POST" action="{{ route('publications.issues.update', [$publication, $issue]) }}">
                        @csrf
                        @method('PATCH')

                        <div class="mb-4">
                            <x-input-label for="title" :value="__('Issue Title')" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $issue->title)" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <x-input-label for="issue_number" :value="__('Issue number')" />
                                <x-text-input id="issue_number" class="block mt-1 w-full" type="number" name="issue_number" :value="old('issue_number', $issue->issue_number)" placeholder="e.g. 42" />
                                <x-input-error :messages="$errors->get('issue_number')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="coverage_label" :value="__('Coverage')" />
                                <x-text-input id="coverage_label" class="block mt-1 w-full" type="text" name="coverage_label" :value="old('coverage_label', $issue->coverage_label)" placeholder="e.g. May–June, Q2, Spring" />
                                <x-input-error :messages="$errors->get('coverage_label')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="release_date" :value="__('Release date')" />
                                <x-text-input id="release_date" class="block mt-1 w-full" type="date" name="release_date" :value="old('release_date', $issue->release_date?->format('Y-m-d'))" />
                                <x-input-error :messages="$errors->get('release_date')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mb-4">
                            <x-input-label for="content" :value="__('Content')" />
                            <x-wysiwyg-editor name="content" :value="old('content', $issue->content)" />
                            <x-input-error :messages="$errors->get('content')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <x-input-label for="status" :value="__('Status')" />
                                <select id="status" name="status" class="np-input mt-1" required>
                                    <option value="draft" {{ old('status', $issue->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="scheduled" {{ old('status', $issue->status) === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                    <option value="sent" {{ old('status', $issue->status) === 'sent' ? 'selected' : '' }}>Sent</option>
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="published_at" :value="__('Publish Date (Optional)')" />
                                <x-text-input id="published_at" class="block mt-1 w-full" type="datetime-local" name="published_at" 
                                    :value="old('published_at', $issue->published_at ? $issue->published_at->format('Y-m-d\TH:i') : '')" />
                                <x-input-error :messages="$errors->get('published_at')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('publications.issues.show', [$publication, $issue]) }}" class="np-btn-outline mr-4">Cancel</a>
                            <x-primary-button>
                                {{ __('Update Issue') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
