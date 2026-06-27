<x-public-layout :publication="$publication" title="Subscribe">
    <h2 class="text-lg font-semibold mb-4">Subscribe</h2>

    <form method="POST" action="{{ route('public.subscribe.store', ['publication' => $publication->slug]) }}" class="space-y-4">
        @csrf

        {{-- Honeypot: hidden from real users, tempting to bots. --}}
        <div class="hidden" aria-hidden="true">
            <label>Company website
                <input type="text" name="company_website" tabindex="-1" autocomplete="off">
            </label>
        </div>

        <div>
            <x-input-label for="email" :value="__('Email address')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="name" :value="__('Name (optional)')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <label class="flex items-start gap-2 text-sm text-gray-600">
            <input type="checkbox" name="consent" value="1" class="mt-1 rounded border-gray-300 text-indigo-600" required>
            <span>I agree to receive {{ $publication->name }} by email and understand I can unsubscribe at any time.</span>
        </label>
        <x-input-error :messages="$errors->get('consent')" class="mt-2" />

        <x-primary-button class="w-full justify-center">{{ __('Subscribe') }}</x-primary-button>
    </form>
</x-public-layout>
