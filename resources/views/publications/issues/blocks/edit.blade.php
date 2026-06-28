<x-app-layout>
    <x-slot name="header">
        <div>
            <span class="np-kicker">{{ $issue->title }}</span>
            <h2 class="font-display text-3xl font-black text-ink leading-tight">
                {{ __('Edit Events Block') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 px-4">
            <div class="np-card p-6">
                @include('publications.issues.blocks._form', [
                    'action' => route('publications.issues.blocks.update', [$publication, $issue, $block]),
                    'method' => 'PATCH',
                    'block' => $block,
                ])
            </div>
        </div>
    </div>
</x-app-layout>
