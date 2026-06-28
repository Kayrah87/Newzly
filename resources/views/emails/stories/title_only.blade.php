{{--
    Title-only story layout (no photo): just the headline, with an underline rule
    (e.g. a section divider or teaser). Table-based for cross-client safety.
--}}
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;">
    <tr>
        <td style="padding:0 0 8px 0; border-bottom:1px solid #e5e7eb;">
            <h2 style="margin:0; font-family:Arial,Helvetica,sans-serif; font-size:18px; line-height:24px; mso-line-height-rule:exactly; color:#111827; font-weight:bold;">{{ $story->title }}</h2>
        </td>
    </tr>
    <tr>
        <td style="font-size:0; line-height:24px; height:24px;">&nbsp;</td>
    </tr>
</table>
