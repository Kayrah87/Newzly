<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Story') }} - {{ $issue->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
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
                            <textarea id="content" name="content" rows="15" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('content', $story->content) }}</textarea>
                            <x-input-error :messages="$errors->get('content')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <x-input-label for="layout" :value="__('Layout')" />
                                <select id="layout" name="layout" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="standard" @selected(old('layout', $story->layout) === 'standard')>Standard (title + text)</option>
                                    <option value="picture" @selected(old('layout', $story->layout) === 'picture')>Picture (hero image + text)</option>
                                    <option value="title_only" @selected(old('layout', $story->layout) === 'title_only')>Title only</option>
                                </select>
                                <x-input-error :messages="$errors->get('layout')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="status" :value="__('Status')" />
                                <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="pending" @selected(old('status', $story->status) === 'pending')>Pending</option>
                                    <option value="approved" @selected(old('status', $story->status) === 'approved')>Approved</option>
                                    <option value="rejected" @selected(old('status', $story->status) === 'rejected')>Rejected</option>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Only approved stories are emailed.</p>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="order" :value="__('Display Order')" />
                                <x-text-input id="order" class="block mt-1 w-full" type="number" name="order" :value="old('order', $story->order)" />
                                <p class="text-sm text-gray-600 mt-1">Lower numbers appear first</p>
                                <x-input-error :messages="$errors->get('order')" class="mt-2" />
                            </div>
                        </div>

                        @if($story->images->isNotEmpty())
                            <div class="mb-4">
                                <x-input-label :value="__('Current images')" />
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-2">
                                    @foreach($story->images as $image)
                                        <label class="block border rounded p-2 cursor-pointer">
                                            <img src="{{ $image->url() }}" alt="" class="h-24 w-full object-cover rounded mb-2">
                                            <span class="flex items-center text-sm text-gray-600">
                                                <input type="checkbox" name="remove_images[]" value="{{ $image->id }}" class="rounded border-gray-300 text-red-600 mr-2">
                                                Remove
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="mb-4">
                            <x-input-label for="images" :value="__('Add images')" />
                            <input id="images" type="file" name="images[]" accept="image/*" multiple class="block mt-1 w-full text-sm text-gray-600">
                            <p class="text-sm text-gray-600 mt-1">For the Picture layout, the first image is used as the hero.</p>
                            <x-input-error :messages="$errors->get('images.0')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('publications.issues.show', [$publication, $issue]) }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                            <x-primary-button>
                                {{ __('Update Story') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '#content',
            height: 500,
            menubar: false,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | bold italic forecolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
        });
    </script>
    @endpush
</x-app-layout>
