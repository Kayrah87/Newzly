<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Publication') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('publications.update', $publication) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        {{-- Basics --}}
                        <div class="mb-4">
                            <x-input-label for="name" :value="__('Publication Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $publication->name)" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label :value="__('Public URL slug')" />
                            <p class="mt-1 text-sm text-gray-500">{{ url('/p/'.$publication->slug) }}</p>
                        </div>

                        <div class="mb-4">
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description', $publication->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="website_url" :value="__('Website')" />
                            <x-text-input id="website_url" class="block mt-1 w-full" type="url" name="website_url" :value="old('website_url', $publication->website_url)" placeholder="https://example.com" />
                            <x-input-error :messages="$errors->get('website_url')" class="mt-2" />
                        </div>

                        {{-- Logo --}}
                        <hr class="my-6">
                        <h3 class="text-lg font-semibold mb-4">{{ __('Branding') }}</h3>

                        <div class="mb-4">
                            <x-input-label for="logo" :value="__('Logo')" />
                            @if ($publication->logoUrl())
                                <div class="flex items-center gap-4 mt-2 mb-3">
                                    <img src="{{ $publication->logoUrl() }}" alt="Logo" class="h-16 w-16 object-contain rounded border">
                                    <label class="inline-flex items-center text-sm text-gray-600">
                                        <input type="checkbox" name="remove_logo" value="1" class="rounded border-gray-300 text-indigo-600 mr-2">
                                        {{ __('Remove current logo') }}
                                    </label>
                                </div>
                            @endif
                            <input id="logo" type="file" name="logo" accept="image/*" class="block mt-1 w-full text-sm text-gray-600">
                            <p class="mt-1 text-xs text-gray-500">{{ __('PNG, JPG, GIF, SVG up to 2MB.') }}</p>
                            <x-input-error :messages="$errors->get('logo')" class="mt-2" />
                        </div>

                        {{-- Social links --}}
                        <div class="mb-2">
                            <x-input-label :value="__('Social links')" />
                        </div>
                        @foreach (\App\Models\Publication::SOCIAL_PLATFORMS as $key => $label)
                            <div class="mb-3">
                                <label for="social_{{ $key }}" class="block text-sm text-gray-600">{{ $label }}</label>
                                <x-text-input id="social_{{ $key }}" class="block mt-1 w-full" type="url" name="social_links[{{ $key }}]" :value="old('social_links.'.$key, $publication->social_links[$key] ?? '')" placeholder="https://..." />
                                <x-input-error :messages="$errors->get('social_links.'.$key)" class="mt-2" />
                            </div>
                        @endforeach

                        {{-- Sending identity --}}
                        <hr class="my-6">
                        <h3 class="text-lg font-semibold mb-4">{{ __('Email sending identity') }}</h3>
                        <p class="text-sm text-gray-500 mb-4">{{ __('Used as the From/Reply-To when issues are sent. SMTP delivery is configured in a later step.') }}</p>

                        <div class="mb-4">
                            <x-input-label for="from_name" :value="__('From name')" />
                            <x-text-input id="from_name" class="block mt-1 w-full" type="text" name="from_name" :value="old('from_name', $publication->from_name)" placeholder="{{ $publication->name }}" />
                            <x-input-error :messages="$errors->get('from_name')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="from_email" :value="__('From email')" />
                            <x-text-input id="from_email" class="block mt-1 w-full" type="email" name="from_email" :value="old('from_email', $publication->from_email)" placeholder="hello@example.com" />
                            <x-input-error :messages="$errors->get('from_email')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="reply_to_email" :value="__('Reply-to email')" />
                            <x-text-input id="reply_to_email" class="block mt-1 w-full" type="email" name="reply_to_email" :value="old('reply_to_email', $publication->reply_to_email)" placeholder="replies@example.com" />
                            <x-input-error :messages="$errors->get('reply_to_email')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('publications.show', $publication) }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                            <x-primary-button>
                                {{ __('Update Publication') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
