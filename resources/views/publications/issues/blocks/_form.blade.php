@php
    $rows = $block ? $block->events->map(fn ($e) => [
        '_k' => $e->id,
        'name' => $e->name,
        'date' => optional($e->date)->format('Y-m-d'),
        'location' => $e->location,
        'description' => $e->description,
    ])->values() : collect();

    if ($rows->isEmpty()) {
        $rows = collect([['_k' => 0, 'name' => '', 'date' => null, 'location' => null, 'description' => null]]);
    }
@endphp

<form method="POST" action="{{ $action }}">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif
    @unless($block)
        <input type="hidden" name="type" value="{{ $type ?? \App\Models\Block::TYPE_EVENTS }}">
    @endunless

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div>
            <x-input-label for="title" :value="__('Block title (optional)')" />
            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $block->title ?? 'Upcoming Events')" placeholder="Upcoming Events" />
            <x-input-error :messages="$errors->get('title')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="title_style" :value="__('Title style')" />
            <select id="title_style" name="title_style" class="np-input mt-1" required>
                @foreach(\App\Models\Block::TITLE_STYLES as $value => $label)
                    <option value="{{ $value }}" @selected(old('title_style', $block->title_style ?? 'accent') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('title_style')" class="mt-2" />
        </div>
    </div>

    <div class="mb-6">
        <x-input-label for="intro" :value="__('Intro text (optional)')" />
        <textarea id="intro" name="intro" rows="2" class="np-input mt-1" placeholder="A short line introducing the events…">{{ old('intro', $block->intro ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('intro')" class="mt-2" />
    </div>

    {{-- Events repeater --}}
    <div x-data="{
            rows: @js($rows->values()),
            nextKey: {{ $rows->count() + 1 }},
            add() { this.rows.push({ _k: this.nextKey++, name: '', date: '', location: '', description: '' }); },
            remove(i) { this.rows.splice(i, 1); if (this.rows.length === 0) { this.add(); } },
         }" class="mb-6">
        <div class="flex items-center justify-between mb-3">
            <h3 class="font-display text-lg font-bold">Events</h3>
            <button type="button" @click="add()" class="np-btn-outline">+ Add event</button>
        </div>

        <div class="space-y-3">
            <template x-for="(row, index) in rows" :key="row._k">
                <div class="border border-ink/15 bg-white p-4">
                    <div class="flex items-start gap-3">
                        <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="np-label mb-1">Event name</label>
                                <input type="text" :name="`events[${index}][name]`" x-model="row.name" class="np-input" placeholder="Old Stores Meetup">
                            </div>
                            <div>
                                <label class="np-label mb-1">Date</label>
                                <input type="date" :name="`events[${index}][date]`" x-model="row.date" class="np-input">
                            </div>
                            <div class="md:col-span-2">
                                <label class="np-label mb-1">Location</label>
                                <input type="text" :name="`events[${index}][location]`" x-model="row.location" class="np-input" placeholder="The Old Stores, Caernarfon, LL55 1AB">
                            </div>
                            <div class="md:col-span-2">
                                <label class="np-label mb-1">Description (optional)</label>
                                <input type="text" :name="`events[${index}][description]`" x-model="row.description" class="np-input" placeholder="Casual meetup and a cuppa">
                            </div>
                        </div>
                        <button type="button" @click="remove(index)" class="text-press-600 hover:text-press-700 font-semibold text-sm pt-6 shrink-0" aria-label="Remove event">Remove</button>
                    </div>
                </div>
            </template>
        </div>
        <p class="text-xs text-ink-soft mt-2">Blank events (no name) are ignored. The date shows as e.g. “17th May”.</p>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('publications.issues.show', [$publication, $issue]) }}" class="np-btn-outline">Cancel</a>
        <x-primary-button>{{ $block ? __('Update block') : __('Add block') }}</x-primary-button>
    </div>
</form>
