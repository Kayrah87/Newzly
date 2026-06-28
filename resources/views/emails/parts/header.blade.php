{{--
    Masthead / header — shared across every publication.
    Two columns (brand + region on the left, logo on the right) over the header
    background, followed by an accent-topped sub-bar carrying the issue number
    and timeframe. Placeholders (logo, name, description, issue number, timeframe)
    are filled from the publication + issue; colours come from the palette.
    Table-based, align-column layout + mso ghost tables for cross-client safety.
--}}
@php
    $timeframe = $issue->coverage_label
        ?: ($issue->release_date ? $issue->release_date->format('F Y') : null);
@endphp
<tr>
    <td style="padding:0;">
        {{-- Brand row --}}
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="{{ $palette['header_bg'] }}" style="width:100%; border-collapse:collapse; background:{{ $palette['header_bg'] }}; mso-table-lspace:0pt; mso-table-rspace:0pt;">
            <tr>
                <td class="email-pad" style="padding:28px 24px;">
                    <!--[if mso]><table role="presentation" width="552" cellpadding="0" cellspacing="0" border="0"><tr><td width="360" valign="middle"><![endif]-->
                    <table role="presentation" cellpadding="0" cellspacing="0" border="0" align="left" style="max-width:360px;">
                        <tr>
                            <td style="font-family:Arial,Helvetica,sans-serif;">
                                <p style="margin:0; font-family:Georgia,'Times New Roman',serif; font-size:32px; line-height:36px; mso-line-height-rule:exactly; font-weight:bold; color:{{ $palette['header_text'] }};">{{ $publication->name }}</p>
                                @if($publication->description)
                                    <p style="margin:8px 0 0; font-family:Arial,Helvetica,sans-serif; font-style:italic; font-size:14px; line-height:20px; mso-line-height-rule:exactly; color:{{ $palette['header_text'] }};">{{ $publication->description }}</p>
                                @endif
                            </td>
                        </tr>
                    </table>
                    @if($publication->logoUrl())
                        <!--[if mso]></td><td width="192" valign="middle" align="right"><![endif]-->
                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" align="right">
                            <tr>
                                <td align="right">
                                    <img src="{{ $publication->logoUrl() }}" alt="{{ $publication->name }}" width="120" style="display:block; width:120px; max-width:120px; height:auto; border:0; outline:none; text-decoration:none; -ms-interpolation-mode:bicubic;">
                                </td>
                            </tr>
                        </table>
                    @endif
                    <!--[if mso]></td></tr></table><![endif]-->
                </td>
            </tr>
        </table>
        {{-- Issue + timeframe sub-bar --}}
        @if($issue->issue_number || $timeframe)
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="{{ $palette['subbar_bg'] }}" style="width:100%; border-collapse:collapse; background:{{ $palette['subbar_bg'] }}; border-top:4px solid {{ $palette['accent'] }}; mso-table-lspace:0pt; mso-table-rspace:0pt;">
                <tr>
                    <td class="email-pad" style="padding:10px 24px; font-family:Arial,Helvetica,sans-serif; font-size:13px; line-height:18px; mso-line-height-rule:exactly; letter-spacing:1px; text-transform:uppercase; color:{{ $palette['subbar_text'] }};">
                        @if($issue->issue_number)Issue&nbsp;{{ $issue->issue_number }}@endif
                        @if($issue->issue_number && $timeframe) &nbsp;&middot;&nbsp; @endif
                        @if($timeframe){{ $timeframe }}@endif
                    </td>
                </tr>
            </table>
        @endif
    </td>
</tr>
