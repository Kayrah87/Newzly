@props(['items' => []])

{{--
    Newspaper-style trail. $items is an array of ['label' => string, 'url' => ?string].
    The last item is rendered as the current page (no link).
--}}
@if(count($items) > 0)
    <nav aria-label="Breadcrumb" {{ $attributes->merge(['class' => 'mt-3']) }}>
        <ol class="flex flex-wrap items-center gap-x-2 gap-y-1 font-sans text-xs font-bold uppercase tracking-[0.1em]">
            @foreach($items as $item)
                <li class="flex items-center gap-x-2">
                    @if(! $loop->last && ! empty($item['url']))
                        <a href="{{ $item['url'] }}" class="text-ink-soft hover:text-press-600 transition-colors">{{ $item['label'] }}</a>
                    @else
                        <span @class(['text-ink' => $loop->last, 'text-ink-soft' => ! $loop->last]) @if($loop->last) aria-current="page" @endif>{{ $item['label'] }}</span>
                    @endif

                    @unless($loop->last)
                        <span class="text-ink/30" aria-hidden="true">/</span>
                    @endunless
                </li>
            @endforeach
        </ol>
    </nav>
@endif
