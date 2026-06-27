<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $issue->title }}</title>
</head>
<body style="margin:0; padding:0; background:#f3f4f6; font-family:Arial, Helvetica, sans-serif; color:#111827;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6; padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="width:600px; max-width:100%; background:#ffffff; border-radius:8px; overflow:hidden;">
                    {{-- Header --}}
                    <tr>
                        <td style="padding:24px; text-align:center; border-bottom:1px solid #e5e7eb;">
                            @if($publication->logoUrl())
                                <img src="{{ $publication->logoUrl() }}" alt="{{ $publication->name }}" height="48" style="height:48px;">
                            @endif
                            <h1 style="margin:8px 0 0; font-size:20px;">{{ $publication->name }}</h1>
                            <p style="margin:4px 0 0; color:#6b7280; font-size:14px;">{{ $issue->title }}</p>
                        </td>
                    </tr>

                    {{-- Stories --}}
                    <tr>
                        <td style="padding:24px;">
                            @forelse($stories as $story)
                                @include('emails.stories.'.($story->layout ?? 'standard'), ['story' => $story])
                            @empty
                                <p style="color:#6b7280;">This issue has no stories yet.</p>
                            @endforelse
                        </td>
                    </tr>

                    {{-- Mandatory unsubscribe footer (GDPR) --}}
                    <tr>
                        <td style="padding:24px; border-top:1px solid #e5e7eb; text-align:center; color:#9ca3af; font-size:12px;">
                            <p style="margin:0 0 8px;">You're receiving this because you subscribed to {{ $publication->name }}.</p>
                            <p style="margin:0;">
                                <a href="{{ $unsubscribeUrl }}" style="color:#6b7280; text-decoration:underline;">Unsubscribe</a>
                                @if($publication->website_url)
                                    &nbsp;·&nbsp;<a href="{{ $publication->website_url }}" style="color:#6b7280; text-decoration:underline;">Visit website</a>
                                @endif
                            </p>
                        </td>
                    </tr>
                </table>
                <p style="color:#9ca3af; font-size:11px; margin-top:16px;">Powered by Newzly</p>
            </td>
        </tr>
    </table>
</body>
</html>
