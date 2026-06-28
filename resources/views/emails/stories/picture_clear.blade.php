{{--
    Picture "clear" story layout (with photo): no filled accent banner — the
    title is the accent colour over the article background, then a full-bleed
    hero image, then the body. Falls back to title + body when no image is
    attached. Table-based + inline styles + Outlook image fixes for safety.
--}}
@php($palette = $palette ?? $story->publication->paletteColors())
@php($hero = $story->heroImage())
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;">
    <tr>
        <td class="email-pad" bgcolor="{{ $palette['body_bg'] }}" style="padding:22px 24px {{ $hero ? '14px' : '22px' }}; background:{{ $palette['body_bg'] }};">
            <h2 style="margin:0; font-family:Arial,Helvetica,sans-serif; font-size:22px; line-height:28px; mso-line-height-rule:exactly; color:{{ $palette['accent'] }}; font-weight:bold;">{{ $story->title }}</h2>
        </td>
    </tr>
    @if($hero)
        <tr>
            <td bgcolor="{{ $palette['body_bg'] }}" style="padding:0; background:{{ $palette['body_bg'] }}; font-size:0; line-height:0;">
                <img src="{{ $hero->url() }}" alt="{{ $hero->caption ?? $story->title }}" width="600" style="display:block; width:100%; max-width:600px; height:auto; border:0; outline:none; text-decoration:none; -ms-interpolation-mode:bicubic;">
            </td>
        </tr>
        @if($hero->caption)
            <tr>
                <td class="email-pad" bgcolor="{{ $palette['body_bg'] }}" style="padding:8px 24px 0; background:{{ $palette['body_bg'] }}; font-family:Arial,Helvetica,sans-serif; font-size:12px; line-height:18px; mso-line-height-rule:exactly; color:{{ $palette['body_text'] }};">{{ $hero->caption }}</td>
            </tr>
        @endif
    @endif
    <tr>
        <td class="email-pad" bgcolor="{{ $palette['body_bg'] }}" style="padding:16px 24px 22px; background:{{ $palette['body_bg'] }};">
            <div class="story-content" style="font-family:Arial,Helvetica,sans-serif; font-size:16px; line-height:24px; mso-line-height-rule:exactly; color:{{ $palette['body_text'] }};">
                {!! $story->content !!}
            </div>
        </td>
    </tr>
</table>
