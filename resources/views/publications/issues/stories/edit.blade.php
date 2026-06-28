<x-app-layout>
    <x-slot name="header">
        <div>
            <span class="np-kicker">{{ $issue->title }}</span>
            <h2 class="font-display text-3xl font-black text-ink leading-tight">
                {{ __('Edit Story') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 px-4">
            <div class="np-card">
                <div class="p-6">
                    <form method="POST" action="{{ route('publications.issues.stories.update', [$publication, $issue, $story]) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <div class="mb-4">
                            <x-input-label for="title" :value="__('Story Title')" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $story->title)" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="content" :value="__('Content')" />
                            <x-wysiwyg-editor name="content" :value="old('content', $story->content)" />
                            <x-input-error :messages="$errors->get('content')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <x-input-label for="layout" :value="__('Layout')" />
                                <select id="layout" name="layout" class="np-input mt-1" required>
                                    @foreach(\App\Models\Story::LAYOUT_LABELS as $value => $label)
                                        <option value="{{ $value }}" @selected(old('layout', $story->layout) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('layout')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="status" :value="__('Status')" />
                                <select id="status" name="status" class="np-input mt-1" required>
                                    <option value="pending" @selected(old('status', $story->status) === 'pending')>Pending</option>
                                    <option value="approved" @selected(old('status', $story->status) === 'approved')>Approved</option>
                                    <option value="rejected" @selected(old('status', $story->status) === 'rejected')>Rejected</option>
                                </select>
                                <p class="text-xs text-ink-soft mt-1">Only approved stories are emailed.</p>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="order" :value="__('Display Order')" />
                                <x-text-input id="order" class="block mt-1 w-full" type="number" name="order" :value="old('order', $story->order)" />
                                <p class="text-sm text-ink-soft mt-1">Lower numbers appear first</p>
                                <x-input-error :messages="$errors->get('order')" class="mt-2" />
                            </div>
                        </div>

                        @if($story->images->isNotEmpty())
                            <div class="mb-4">
                                <x-input-label :value="__('Current images')" />
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-2">
                                    @foreach($story->images as $image)
                                        <label class="block border border-ink/15 p-2 cursor-pointer hover:border-ink">
                                            <img src="{{ $image->url() }}" alt="" class="h-24 w-full object-cover mb-2">
                                            <span class="flex items-center text-sm text-ink-soft">
                                                <input type="checkbox" name="remove_images[]" value="{{ $image->id }}" class="rounded border-ink/30 text-press-600 focus:ring-press-500 mr-2">
                                                Remove
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="mb-4">
                            <x-input-label for="images" :value="__('Add images')" />
                            <input id="images" type="file" name="images[]" accept="image/*" multiple class="block mt-1 w-full text-sm text-ink-soft">
                            <p class="text-sm text-ink-soft mt-1">For the Picture layouts, the first image is used as the hero.</p>
                            <x-input-error :messages="$errors->get('images.0')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('publications.issues.show', [$publication, $issue]) }}" class="np-btn-outline mr-4">Cancel</a>
                            <x-primary-button>
                                {{ __('Update Story') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
