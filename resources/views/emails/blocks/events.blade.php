{{--
    Events block — an optional title (accent banner / clear accent title / none),
    an optional intro, then a list of events. Each event is a clean single-column
    card with a left accent bar: an accent date line, a prominent name, then the
    location and description in a clear hierarchy. Single column (no fragile
    multi-column ghost tables) keeps it legible and robust across clients.
--}}
@php($palette = $palette ?? $block->publication->paletteColors())

@if($block->title_style === 'accent' && filled($block->title))
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;">
        <tr>
            <td class="email-pad" bgcolor="{{ $palette['accent'] }}" style="padding:14px 24px; background:{{ $palette['accent'] }};">
                <h2 style="margin:0; font-family:Arial,Helvetica,sans-serif; font-size:22px; line-height:28px; mso-line-height-rule:exactly; color:{{ $palette['accent_text'] }}; font-weight:bold;">{{ $block->title }}</h2>
            </td>
        </tr>
    </table>
@endif

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="{{ $palette['body_bg'] }}" style="width:100%; border-collapse:collapse; background:{{ $palette['body_bg'] }}; mso-table-lspace:0pt; mso-table-rspace:0pt;">
    @if($block->title_style === 'plain' && filled($block->title))
        <tr>
            <td class="email-pad" style="padding:22px 24px 0;">
                <h2 style="margin:0; font-family:Arial,Helvetica,sans-serif; font-size:22px; line-height:28px; mso-line-height-rule:exactly; color:{{ $palette['accent'] }}; font-weight:bold;">{{ $block->title }}</h2>
            </td>
        </tr>
    @endif

    @if(filled($block->intro))
        <tr>
            <td class="email-pad" style="padding:16px 24px 0; font-family:Arial,Helvetica,sans-serif; font-size:16px; line-height:24px; mso-line-height-rule:exactly; color:{{ $palette['body_text'] }};">{!! nl2br(e($block->intro)) !!}</td>
        </tr>
    @endif

    @forelse($block->events as $event)
        <tr>
            <td class="email-pad" style="padding:{{ $loop->first ? '18' : '10' }}px 24px 0;">
                {{-- Event card: white sheet, hairline border, left accent bar. --}}
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff" style="width:100%; border-collapse:separate; background:#ffffff; border:1px solid #e6e6e6; border-left:4px solid {{ $palette['event_accent'] }}; border-radius:8px;">
                    <tr>
                        <td style="padding:16px 18px; font-family:Arial,Helvetica,sans-serif;">
                            @if($event->date)
                                <p style="margin:0 0 6px 0; font-family:Arial,Helvetica,sans-serif; font-size:12px; line-height:16px; mso-line-height-rule:exactly; letter-spacing:0.5px; text-transform:uppercase; color:{{ $palette['event_accent'] }}; font-weight:bold;">{{ $event->date->format('jS F Y') }}</p>
                            @endif
                            <p style="margin:0; font-family:Arial,Helvetica,sans-serif; font-size:18px; line-height:24px; mso-line-height-rule:exactly; color:{{ $palette['body_text'] }}; font-weight:bold;">{{ $event->name }}</p>
                            @if(filled($event->location))
                                <p style="margin:6px 0 0 0; font-family:Arial,Helvetica,sans-serif; font-size:14px; line-height:20px; mso-line-height-rule:exactly; color:{{ $palette['body_text'] }}; font-weight:600;">{{ $event->location }}</p>
                            @endif
                            @if(filled($event->description))
                                <p style="margin:8px 0 0 0; font-family:Arial,Helvetica,sans-serif; font-size:14px; line-height:20px; mso-line-height-rule:exactly; color:{{ $palette['body_text'] }};">{{ $event->description }}</p>
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    @empty
        <tr>
            <td class="email-pad" style="padding:16px 24px 0; font-family:Arial,Helvetica,sans-serif; font-size:14px; line-height:20px; color:{{ $palette['body_text'] }};">No events listed.</td>
        </tr>
    @endforelse

    <tr>
        <td style="font-size:0; line-height:22px; height:22px; background:{{ $palette['body_bg'] }};">&nbsp;</td>
    </tr>
</table>
