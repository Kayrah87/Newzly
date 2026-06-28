{{--
    Content section — the issue's stories, rendered in their own (reorderable)
    order via the per-layout partials. No padding here: each story renders its
    own full-bleed accent title banner + body block.
--}}
<tr>
    <td style="padding:0;">
        @forelse($stories as $story)
            @include('emails.stories.'.($story->layout ?? 'standard'), ['story' => $story])
        @empty
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="{{ $palette['body_bg'] }}" style="width:100%; border-collapse:collapse;">
                <tr>
                    <td class="email-pad" style="padding:24px; background:{{ $palette['body_bg'] }}; font-family:Arial,Helvetica,sans-serif; color:{{ $palette['body_text'] }}; font-size:15px; line-height:24px; mso-line-height-rule:exactly;">This issue has no stories yet.</td>
                </tr>
            </table>
        @endforelse
    </td>
</tr>
