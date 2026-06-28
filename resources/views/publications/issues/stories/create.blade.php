<x-app-layout>
    <x-slot name="header">
        <div>
            <span class="np-kicker">{{ $issue->title }}</span>
            <h2 class="font-display text-3xl font-black text-ink leading-tight">
                {{ __('Add Story') }}
            </h2>
        </div>
        <x-breadcrumbs :items="[
            ['label' => 'Publications', 'url' => route('publications.index')],
            ['label' => $publication->name, 'url' => route('publications.show', $publication)],
            ['label' => 'Issues', 'url' => route('publications.issues.index', $publication)],
            ['label' => $issue->title, 'url' => route('publications.issues.show', [$publication, $issue])],
            ['label' => 'Add Story', 'url' => null],
        ]" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 px-4">
            <div class="np-card">
                <div class="p-6">
                    <form method="POST" action="{{ route('publications.issues.stories.store', [$publication, $issue]) }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <x-input-label for="title" :value="__('Story Title')" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="content" :value="__('Content')" />
                            <x-wysiwyg-editor name="content" :value="old('content')" />
                            <x-input-error :messages="$errors->get('content')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <x-input-label for="layout" :value="__('Layout')" />
                                <select id="layout" name="layout" class="np-input mt-1" required>
                                    @foreach(\App\Models\Story::LAYOUT_LABELS as $value => $label)
                                        <option value="{{ $value }}" @selected(old('layout', 'standard') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('layout')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="order" :value="__('Display Order (Optional)')" />
                                <x-text-input id="order" class="block mt-1 w-full" type="number" name="order" :value="old('order', 0)" />
                                <p class="text-sm text-ink-soft mt-1">Lower numbers appear first</p>
                                <x-input-error :messages="$errors->get('order')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mb-4">
                            <x-input-label for="images" :value="__('Images (optional)')" />
                            <input id="images" type="file" name="images[]" accept="image/*" multiple class="block mt-1 w-full text-sm text-ink-soft">
                            <p class="text-sm text-ink-soft mt-1">For the Picture layouts, the first image is used as the hero.</p>
                            <x-input-error :messages="$errors->get('images.0')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('publications.issues.show', [$publication, $issue]) }}" class="np-btn-outline mr-4">Cancel</a>
                            <x-primary-button>
                                {{ __('Add Story') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
