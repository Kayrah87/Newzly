<x-mail::message>
# Confirm your subscription

Thanks for signing up to **{{ $publication->name }}**.

Please confirm your email address to start receiving this publication. If you didn't request this, you can safely ignore this email — no subscription will be created.

<x-mail::button :url="$confirmUrl">
Confirm subscription
</x-mail::button>

If the button doesn't work, copy and paste this link into your browser:

{{ $confirmUrl }}

Thanks,<br>
{{ $publication->from_name ?: $publication->name }}
</x-mail::message>
