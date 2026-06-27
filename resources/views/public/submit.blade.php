<x-public-layout :publication="$publication" title="Submit a story">
    <h2 class="text-lg font-semibold mb-2">Submit a story</h2>
    <p class="text-gray-600 text-sm mb-4">
        Share a story or photos with {{ $publication->name }}. An editor will review your
        submission before it's published.
    </p>

    <form method="POST" action="{{ route('public.submit.store', ['publication' => $publication->slug]) }}" enctype="multipart/form-data" class="space-y-4">
        @csrf

        {{-- Honeypot --}}
        <div class="hidden" aria-hidden="true">
            <label>Company website
                <input type="text" name="company_website" tabindex="-1" autocomplete="off">
            </label>
        </div>

        <div>
            <x-input-label for="title" :value="__('Story title')" />
            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required autofocus />
            <x-input-error :messages="$errors->get('title')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="content" :value="__('Your story')" />
            <textarea id="content" name="content" rows="8" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('content') }}</textarea>
            <x-input-error :messages="$errors->get('content')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="images" :value="__('Photos (optional)')" />
            <input id="images" type="file" name="images[]" accept="image/*" multiple class="block mt-1 w-full text-sm text-gray-600">
            <p class="text-xs text-gray-500 mt-1">Up to 5 images.</p>
            <x-input-error :messages="$errors->get('images.0')" class="mt-2" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-input-label for="submitter_name" :value="__('Your name (optional)')" />
                <x-text-input id="submitter_name" class="block mt-1 w-full" type="text" name="submitter_name" :value="old('submitter_name')" />
                <x-input-error :messages="$errors->get('submitter_name')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="submitter_email" :value="__('Your email (optional)')" />
                <x-text-input id="submitter_email" class="block mt-1 w-full" type="email" name="submitter_email" :value="old('submitter_email')" />
                <x-input-error :messages="$errors->get('submitter_email')" class="mt-2" />
            </div>
        </div>

        <label class="flex items-start gap-2 text-sm text-gray-600">
            <input type="checkbox" name="consent" value="1" class="mt-1 rounded border-gray-300 text-indigo-600" required>
            <span>I confirm this is my own work (or I have permission to share it) and I grant {{ $publication->name }} permission to publish it.</span>
        </label>
        <x-input-error :messages="$errors->get('consent')" class="mt-2" />

        <x-primary-button class="w-full justify-center">{{ __('Submit story') }}</x-primary-button>
    </form>
</x-public-layout>
