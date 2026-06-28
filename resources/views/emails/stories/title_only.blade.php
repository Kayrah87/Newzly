{{--
    Title-only story layout: just the accent title banner — a section divider or
    teaser between articles. Table-based for cross-client safety.
--}}
@php($palette = $palette ?? $story->publication->paletteColors())
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;">
    <tr>
        <td class="email-pad" bgcolor="{{ $palette['accent'] }}" style="padding:14px 24px; background:{{ $palette['accent'] }};">
            <h2 style="margin:0; font-family:Arial,Helvetica,sans-serif; font-size:22px; line-height:28px; mso-line-height-rule:exactly; color:{{ $palette['accent_text'] }}; font-weight:bold;">{{ $story->title }}</h2>
        </td>
    </tr>
</table>
