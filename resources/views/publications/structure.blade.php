<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <span class="np-kicker">{{ $publication->name }}</span>
                <h2 class="font-display text-3xl font-black text-ink leading-tight">
                    {{ __('Layout & Theme') }}
                </h2>
            </div>
            <a href="{{ route('publications.show', $publication) }}" class="np-btn-ghost">Back</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 px-4">
            @if(session('success'))
                <div class="mb-4 border-l-4 border-press-600 bg-press-50 text-ink px-4 py-3 font-semibold">
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="mb-4 border-l-4 border-ink bg-ink/5 text-ink px-4 py-3 font-semibold">
                    {{ $errors->first() }}
                </div>
            @endif

            <p class="mb-6 max-w-2xl text-ink-soft">
                Set the running order of every issue and the colours of its masthead and footer.
                This structure stays the same across <strong class="text-ink">all</strong> issues of
                {{ $publication->name }} — only the stories change.
            </p>

            <form method="POST" action="{{ route('publications.structure.update', $publication) }}"
                  x-data="structureEditor({
                      order: @js($publication->structureOrder()),
                      palette: @js($publication->paletteColors()),
                      meta: @js(\App\Models\Publication::STRUCTURE_LABELS)
                  })">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
                    {{-- Left: section order + palette controls --}}
                    <div class="space-y-6">
                        <div class="np-card p-6">
                            <h3 class="font-display text-lg font-bold mb-1">Section order</h3>
                            <p class="text-sm text-ink-soft mb-4">Drag the sections (or use the arrows) to reorder the masthead, stories and footer.</p>

                            <ul class="space-y-2">
                                <template x-for="(key, index) in order" :key="key">
                                    <li data-sort-item
                                        draggable="true"
                                        @dragstart="start($event, key)"
                                        @dragover="over($event, key)"
                                        @dragend="end()"
                                        class="flex items-center gap-3 border border-ink/15 bg-white px-3 py-3 cursor-grab active:cursor-grabbing"
                                        :class="dragKey === key ? 'opacity-40 border-press-600' : ''">
                                        {{-- Posts the section order as a real array (in drag order). --}}
                                        <input type="hidden" name="structure[]" :value="key">
                                        {{-- grip --}}
                                        <span class="text-ink-soft select-none" aria-hidden="true">⠿</span>
                                        <span class="flex h-7 w-7 items-center justify-center bg-ink text-white font-display font-black text-sm"
                                              x-text="index + 1"></span>
                                        <div class="flex-1">
                                            <p class="font-display font-bold text-ink" x-text="meta[key]?.label ?? key"></p>
                                            <p class="text-xs text-ink-soft" x-text="meta[key]?.description ?? ''"></p>
                                        </div>
                                        <div class="flex flex-col">
                                            <button type="button" @click="moveUp(key)" :disabled="index === 0"
                                                    class="px-1 text-ink-soft hover:text-press-600 disabled:opacity-25" aria-label="Move up">▲</button>
                                            <button type="button" @click="moveDown(key)" :disabled="index === order.length - 1"
                                                    class="px-1 text-ink-soft hover:text-press-600 disabled:opacity-25" aria-label="Move down">▼</button>
                                        </div>
                                    </li>
                                </template>
                            </ul>
                        </div>

                        <div class="np-card p-6">
                            <h3 class="font-display text-lg font-bold mb-1">Colour palette</h3>
                            <p class="text-sm text-ink-soft mb-4">Used to render the masthead and footer of every issue.</p>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach(\App\Models\Publication::PALETTE_FIELDS as $key => $field)
                                    <div>
                                        <label for="palette_{{ $key }}" class="np-label mb-1">{{ $field['label'] }}</label>
                                        <div class="flex items-center gap-2">
                                            <input type="color" id="palette_{{ $key }}"
                                                   x-model="palette.{{ $key }}"
                                                   class="h-9 w-12 border border-ink/20 bg-white p-0.5 cursor-pointer">
                                            <input type="text" name="palette[{{ $key }}]"
                                                   x-model="palette.{{ $key }}"
                                                   class="np-input flex-1 font-mono text-sm uppercase"
                                                   pattern="#[0-9a-fA-F]{6}" maxlength="7">
                                        </div>
                                        <x-input-error :messages="$errors->get('palette.'.$key)" class="mt-1" />
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('publications.show', $publication) }}" class="np-btn-outline">Cancel</a>
                            <x-primary-button>{{ __('Save layout') }}</x-primary-button>
                        </div>
                    </div>

                    {{-- Right: live preview --}}
                    <div class="lg:sticky lg:top-6">
                        <div class="np-card p-4">
                            <p class="np-kicker mb-3">Live preview</p>
                            <div class="border border-ink/15" :style="`background:${palette.page_bg}`">
                                <div class="m-4 bg-white shadow-sm overflow-hidden">
                                    <template x-for="key in order" :key="'preview-'+key">
                                        <div>
                                            {{-- Header preview: brand + logo, then accent issue sub-bar --}}
                                            <template x-if="key === 'header'">
                                                <div>
                                                    <div class="px-5 py-5 flex items-center justify-between gap-3" :style="`background:${palette.header_bg}`">
                                                        <div class="min-w-0">
                                                            <p class="font-display font-black text-xl leading-none truncate" :style="`color:${palette.header_text}`">{{ $publication->name }}</p>
                                                            @if($publication->description)
                                                                <p class="text-[0.6rem] italic mt-1 truncate" :style="`color:${palette.header_text}`">{{ Str::limit($publication->description, 40) }}</p>
                                                            @endif
                                                        </div>
                                                        @if($publication->logoUrl())
                                                            <img src="{{ $publication->logoUrl() }}" alt="" class="h-8 object-contain shrink-0">
                                                        @endif
                                                    </div>
                                                    <div class="px-5 py-1.5 text-[0.6rem] font-bold uppercase tracking-[0.2em]"
                                                         :style="`background:${palette.subbar_bg}; color:${palette.subbar_text}; border-top:3px solid ${palette.accent}`">
                                                        Issue&nbsp;1 &middot; This Month
                                                    </div>
                                                </div>
                                            </template>
                                            {{-- Content preview: accent title banners over body blocks --}}
                                            <template x-if="key === 'content'">
                                                <div>
                                                    <div class="px-5 py-2 text-sm font-bold" :style="`background:${palette.accent}; color:${palette.accent_text}`">Lead Story Headline</div>
                                                    <div class="px-5 py-3 space-y-1" :style="`background:${palette.body_bg}`">
                                                        <div class="h-1.5 w-full" :style="`background:${palette.body_text}; opacity:.25`"></div>
                                                        <div class="h-1.5 w-5/6" :style="`background:${palette.body_text}; opacity:.25`"></div>
                                                    </div>
                                                    <div class="px-5 py-2 text-sm font-bold" :style="`background:${palette.accent}; color:${palette.accent_text}`">Second Story</div>
                                                    <div class="px-5 py-3 space-y-1" :style="`background:${palette.body_bg}`">
                                                        <div class="h-1.5 w-full" :style="`background:${palette.body_text}; opacity:.25`"></div>
                                                        <div class="h-1.5 w-2/3" :style="`background:${palette.body_text}; opacity:.25`"></div>
                                                    </div>
                                                </div>
                                            </template>
                                            {{-- Footer preview --}}
                                            <template x-if="key === 'footer'">
                                                <div class="px-5 py-5"
                                                     :style="`background:${palette.footer_bg}; border-top:3px solid ${palette.accent}`">
                                                    <p class="font-display text-sm font-bold" :style="`color:${palette.footer_text}`">{{ $publication->name }}</p>
                                                    <p class="text-[0.65rem] mt-1" :style="`color:${palette.footer_text}`">You're receiving this because you subscribed. · <span class="underline">Unsubscribe</span></p>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <p class="mt-3 text-xs text-ink-soft text-center">
                                Reordering and colour changes preview instantly. Open a full
                                <span class="font-semibold text-ink">issue preview</span> from any issue page.
                            </p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
