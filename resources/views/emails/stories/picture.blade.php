{{--
    Picture story layout (with photo): hero image, then title + body.
    Falls back to a standard title+body block when no image is attached, so the
    layout is safe for an "article with or without a photo".
    Table-based + inline styles + Outlook image fixes for cross-client safety.
--}}
@php($hero = $story->heroImage())
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;">
    @if($hero)
        <tr>
            <td style="padding:0 0 12px 0;">
                <img src="{{ $hero->url() }}" alt="{{ $hero->caption ?? $story->title }}" width="552" style="display:block; width:100%; max-width:552px; height:auto; border:0; outline:none; text-decoration:none; -ms-interpolation-mode:bicubic; border-radius:6px;">
            </td>
        </tr>
        @if($hero->caption)
            <tr>
                <td style="padding:0 0 12px 0; font-family:Arial,Helvetica,sans-serif; font-size:12px; line-height:18px; mso-line-height-rule:exactly; color:#9ca3af;">{{ $hero->caption }}</td>
            </tr>
        @endif
    @endif
    <tr>
        <td style="padding:0 0 32px 0;">
            <h2 style="margin:0 0 8px 0; font-family:Arial,Helvetica,sans-serif; font-size:18px; line-height:24px; mso-line-height-rule:exactly; color:#111827; font-weight:bold;">{{ $story->title }}</h2>
            <div class="story-content" style="font-family:Arial,Helvetica,sans-serif; font-size:15px; line-height:24px; mso-line-height-rule:exactly; color:#374151;">
                {!! $story->content !!}
            </div>
        </td>
    </tr>
</table>
