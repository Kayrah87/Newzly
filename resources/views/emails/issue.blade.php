<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>{{ $issue->title }}</title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
    {{-- Progressive enhancement only. Every critical style is also inlined so
         clients that strip <style> (e.g. some Gmail contexts) stay readable. --}}
    <style type="text/css">
        body { margin:0 !important; padding:0 !important; width:100% !important; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; }
        table { border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; }
        img { border:0; outline:none; text-decoration:none; -ms-interpolation-mode:bicubic; }
        a { color:#2563eb; }
        /* Normalise WYSIWYG (Tiptap) body content for email readability. */
        .story-content p { margin:0 0 12px 0; }
        .story-content h2, .story-content h3 { margin:0 0 8px 0; line-height:1.3; color:#111827; }
        .story-content ul, .story-content ol { margin:0 0 12px 0; padding-left:20px; }
        .story-content li { margin:0 0 4px 0; }
        .story-content blockquote { margin:0 0 12px 0; padding:0 0 0 12px; border-left:3px solid #e5e7eb; color:#6b7280; }
        .story-content a { color:#2563eb; }
        .story-content img { max-width:100% !important; height:auto !important; }
        .story-content > *:last-child { margin-bottom:0 !important; }
        @media only screen and (max-width:600px) {
            .email-container { width:100% !important; }
            .email-pad { padding-left:16px !important; padding-right:16px !important; }
        }
    </style>
</head>
<body style="margin:0; padding:0; background:{{ $palette['page_bg'] }}; font-family:Arial, Helvetica, sans-serif; color:#111827;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="{{ $palette['page_bg'] }}" style="background:{{ $palette['page_bg'] }}; mso-table-lspace:0pt; mso-table-rspace:0pt;">
        <tr>
            <td align="center" style="padding:24px 12px;">
                <!--[if mso]>
                <table role="presentation" width="600" align="center" cellpadding="0" cellspacing="0" border="0"><tr><td>
                <![endif]-->
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff" class="email-container" style="width:600px; max-width:100%; background:#ffffff; border-radius:8px; overflow:hidden; mso-table-lspace:0pt; mso-table-rspace:0pt;">
                    {{-- Sections render in the publication's configured order
                         (static across all of its issues). Header & footer are
                         the same shared templates for every publication. --}}
                    @foreach($structure as $section)
                        @include('emails.parts.'.$section)
                    @endforeach
                </table>
                <!--[if mso]>
                </td></tr></table>
                <![endif]-->
                <p style="font-family:Arial,Helvetica,sans-serif; color:#9ca3af; font-size:11px; line-height:16px; margin:16px 0 0 0;">Powered by Newzly</p>
            </td>
        </tr>
    </table>
</body>
</html>
