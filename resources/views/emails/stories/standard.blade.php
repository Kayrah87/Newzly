{{-- Standard story layout: title + body. Phase 5 adds more layouts (picture, title-only). --}}
<div style="margin-bottom:32px;">
    <h2 style="margin:0 0 8px; font-size:18px; color:#111827;">{{ $story->title }}</h2>
    <div style="font-size:15px; line-height:1.6; color:#374151;">
        {!! $story->content !!}
    </div>
</div>
