{{--
    Standard story layout (no photo): title + body.
    Table-based + inline styles for cross-client (incl. Outlook) readability.
    Bottom spacing uses td padding (reliable in Outlook, unlike div margins).
--}}
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;">
    <tr>
        <td style="padding:0 0 32px 0;">
            <h2 style="margin:0 0 8px 0; font-family:Arial,Helvetica,sans-serif; font-size:18px; line-height:24px; mso-line-height-rule:exactly; color:#111827; font-weight:bold;">{{ $story->title }}</h2>
            <div class="story-content" style="font-family:Arial,Helvetica,sans-serif; font-size:15px; line-height:24px; mso-line-height-rule:exactly; color:#374151;">
                {!! $story->content !!}
            </div>
        </td>
    </tr>
</table>
