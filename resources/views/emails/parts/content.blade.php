{{--
    Content section — the issue's content stream (stories and blocks merged in
    their shared, reorderable order). No padding here: each item renders its own
    full-bleed section.
--}}
<tr>
    <td style="padding:0;">
        @forelse($items as $item)
            @if($item instanceof \App\Models\Block)
                @include('emails.blocks.'.$item->type, ['block' => $item])
            @else
                @include('emails.stories.'.($item->layout ?? 'standard'), ['story' => $item])
            @endif
        @empty
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="{{ $palette['body_bg'] }}" style="width:100%; border-collapse:collapse;">
                <tr>
                    <td class="email-pad" style="padding:24px; background:{{ $palette['body_bg'] }}; font-family:Arial,Helvetica,sans-serif; color:{{ $palette['body_text'] }}; font-size:15px; line-height:24px; mso-line-height-rule:exactly;">This issue has no stories yet.</td>
                </tr>
            </table>
        @endforelse
    </td>
</tr>
