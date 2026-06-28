<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Issue') }} - {{ $publication->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('publications.issues.store', $publication) }}">
                        @csrf

                        <div class="mb-4">
                            <x-input-label for="title" :value="__('Issue Title')" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <x-input-label for="issue_number" :value="__('Issue number')" />
                                <x-text-input id="issue_number" class="block mt-1 w-full" type="number" name="issue_number" :value="old('issue_number')" placeholder="e.g. 42" />
                                <x-input-error :messages="$errors->get('issue_number')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="coverage_label" :value="__('Coverage')" />
                                <x-text-input id="coverage_label" class="block mt-1 w-full" type="text" name="coverage_label" :value="old('coverage_label')" placeholder="e.g. May–June, Q2, Spring" />
                                <x-input-error :messages="$errors->get('coverage_label')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="release_date" :value="__('Release date')" />
                                <x-text-input id="release_date" class="block mt-1 w-full" type="date" name="release_date" :value="old('release_date')" />
                                <x-input-error :messages="$errors->get('release_date')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mb-4">
                            <x-input-label for="content" :value="__('Content')" />
                            <x-wysiwyg-editor name="content" :value="old('content')" />
                            <x-input-error :messages="$errors->get('content')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <x-input-label for="status" :value="__('Status')" />
                                <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="scheduled" {{ old('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                    <option value="sent" {{ old('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="published_at" :value="__('Publish Date (Optional)')" />
                                <x-text-input id="published_at" class="block mt-1 w-full" type="datetime-local" name="published_at" :value="old('published_at')" />
                                <x-input-error :messages="$errors->get('published_at')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('publications.issues.index', $publication) }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                            <x-primary-button>
                                {{ __('Create Issue') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
