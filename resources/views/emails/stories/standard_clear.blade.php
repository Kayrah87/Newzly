{{--
    Standard "clear" story layout (no photo): no filled accent banner — the
    header sits on the article background and the title itself is the accent
    colour. Table-based + inline styles for cross-client safety.
--}}
@php($palette = $palette ?? $story->publication->paletteColors())
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;">
    <tr>
        <td class="email-pad" bgcolor="{{ $palette['body_bg'] }}" style="padding:22px 24px; background:{{ $palette['body_bg'] }};">
            <h2 style="margin:0 0 10px 0; font-family:Arial,Helvetica,sans-serif; font-size:22px; line-height:28px; mso-line-height-rule:exactly; color:{{ $palette['accent'] }}; font-weight:bold;">{{ $story->title }}</h2>
            <div class="story-content" style="font-family:Arial,Helvetica,sans-serif; font-size:16px; line-height:24px; mso-line-height-rule:exactly; color:{{ $palette['body_text'] }};">
                {!! $story->content !!}
            </div>
        </td>
    </tr>
</table>
