{{--
    Footer — shared across every publication. Dark band with an accent top rule
    and two columns: brand/identity on the left, contact + links + the mandatory
    GDPR unsubscribe on the right. Colours come from the palette.
    Align-column layout + mso ghost tables for cross-client safety.
--}}
<tr>
    <td style="padding:0;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="{{ $palette['footer_bg'] }}" style="width:100%; border-collapse:collapse; background:{{ $palette['footer_bg'] }}; border-top:4px solid {{ $palette['accent'] }}; mso-table-lspace:0pt; mso-table-rspace:0pt;">
            <tr>
                <td class="email-pad" style="padding:24px;">
                    <!--[if mso]><table role="presentation" width="552" cellpadding="0" cellspacing="0" border="0"><tr><td width="330" valign="top"><![endif]-->
                    <table role="presentation" cellpadding="0" cellspacing="0" border="0" align="left" style="max-width:330px;">
                        <tr>
                            <td style="font-family:Arial,Helvetica,sans-serif; color:{{ $palette['footer_text'] }};">
                                <p style="margin:0; font-family:Georgia,'Times New Roman',serif; font-size:16px; line-height:20px; mso-line-height-rule:exactly; font-weight:bold; color:{{ $palette['footer_text'] }};">{{ $publication->name }}</p>
                                @if($publication->description)
                                    <p style="margin:8px 0 0; font-size:12px; line-height:18px; mso-line-height-rule:exactly; color:{{ $palette['footer_text'] }};">{{ $publication->description }}</p>
                                @endif
                                <p style="margin:12px 0 0; font-size:12px; line-height:18px; mso-line-height-rule:exactly; color:{{ $palette['footer_text'] }};">&copy; {{ date('Y') }} {{ $publication->name }}</p>
                            </td>
                        </tr>
                    </table>
                    <!--[if mso]></td><td width="20"></td><td width="200" valign="top"><![endif]-->
                    <table role="presentation" cellpadding="0" cellspacing="0" border="0" align="right" style="max-width:200px;">
                        <tr>
                            <td style="font-family:Arial,Helvetica,sans-serif; font-size:12px; line-height:18px; mso-line-height-rule:exactly; color:{{ $palette['footer_text'] }};">
                                @if($publication->website_url)
                                    <p style="margin:0 0 6px 0;"><a href="{{ $publication->website_url }}" style="color:{{ $palette['footer_text'] }}; text-decoration:underline;">Visit website</a></p>
                                @endif
                                @if($publication->reply_to_email || $publication->from_email)
                                    <p style="margin:0 0 6px 0;">Contact: {{ $publication->reply_to_email ?: $publication->from_email }}</p>
                                @endif
                                @if(!empty($publication->social_links))
                                    <p style="margin:0 0 6px 0;">
                                        @foreach($publication->social_links as $platform => $url)
                                            <a href="{{ $url }}" style="color:{{ $palette['footer_text'] }}; text-decoration:underline;">{{ \App\Models\Publication::SOCIAL_PLATFORMS[$platform] ?? ucfirst($platform) }}</a>@if(!$loop->last) &middot; @endif
                                        @endforeach
                                    </p>
                                @endif
                                <p style="margin:12px 0 0 0;">You're receiving this because you subscribed to {{ $publication->name }}.</p>
                                <p style="margin:6px 0 0 0;"><a href="{{ $unsubscribeUrl }}" style="color:{{ $palette['footer_text'] }}; text-decoration:underline;">Unsubscribe</a></p>
                            </td>
                        </tr>
                    </table>
                    <!--[if mso]></td></tr></table><![endif]-->
                </td>
            </tr>
        </table>
    </td>
</tr>
