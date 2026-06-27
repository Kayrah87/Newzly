<x-mail::message>
# You've been invited

You've been invited to join **{{ $publication->name }}** on Newzly as a **{{ str_replace('_', ' ', $invitation->role) }}**.

<x-mail::button :url="$acceptUrl">
View invitation
</x-mail::button>

If you don't have a Newzly account yet, you'll be able to create one with this email address ({{ $invitation->email }}) to accept.

This invitation expires {{ $invitation->expires_at?->format('M d, Y') }}.

Thanks,<br>
Newzly
</x-mail::message>
