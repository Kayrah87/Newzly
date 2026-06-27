{{-- Picture story layout: hero image, then title + body. --}}
@php($hero = $story->heroImage())
<div style="margin-bottom:32px;">
    @if($hero)
        <img src="{{ $hero->url() }}" alt="{{ $hero->caption ?? $story->title }}" width="552" style="width:100%; max-width:552px; border-radius:6px; display:block; margin-bottom:12px;">
        @if($hero->caption)
            <p style="margin:0 0 12px; font-size:12px; color:#9ca3af;">{{ $hero->caption }}</p>
        @endif
    @endif
    <h2 style="margin:0 0 8px; font-size:18px; color:#111827;">{{ $story->title }}</h2>
    <div style="font-size:15px; line-height:1.6; color:#374151;">
        {!! $story->content !!}
    </div>
</div>
