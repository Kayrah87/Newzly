{{--
    Standard story layout (no photo): an accent title banner over a body block.
    Table-based + inline styles for cross-client (incl. Outlook) readability.
    $palette is shared from the issue email; fall back defensively.
--}}
@php($palette = $palette ?? $story->publication->paletteColors())
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;">
    <tr>
        <td class="email-pad" bgcolor="{{ $palette['accent'] }}" style="padding:14px 24px; background:{{ $palette['accent'] }};">
            <h2 style="margin:0; font-family:Arial,Helvetica,sans-serif; font-size:22px; line-height:28px; mso-line-height-rule:exactly; color:{{ $palette['accent_text'] }}; font-weight:bold;">{{ $story->title }}</h2>
        </td>
    </tr>
    <tr>
        <td class="email-pad" bgcolor="{{ $palette['body_bg'] }}" style="padding:20px 24px; background:{{ $palette['body_bg'] }};">
            <div class="story-content" style="font-family:Arial,Helvetica,sans-serif; font-size:16px; line-height:24px; mso-line-height-rule:exactly; color:{{ $palette['body_text'] }};">
                {!! $story->content !!}
            </div>
        </td>
    </tr>
</table>
