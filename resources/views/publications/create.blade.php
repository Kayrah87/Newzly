<x-app-layout>
    <x-slot name="header">
        <div>
            <span class="np-kicker">Start the Presses</span>
            <h2 class="font-display text-3xl font-black text-ink leading-tight">
                {{ __('Create Publication') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 px-4">
            <div class="np-card">
                <div class="p-6">
                    <form method="POST" action="{{ route('publications.store') }}">
                        @csrf

                        <div class="mb-4">
                            <x-input-label for="name" :value="__('Publication Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" rows="4" class="block mt-1 w-full border-ink/25 bg-white text-ink focus:border-press-500 focus:ring-press-500 shadow-sm">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('publications.index') }}" class="np-btn-outline mr-4">Cancel</a>
                            <x-primary-button>
                                {{ __('Create Publication') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
