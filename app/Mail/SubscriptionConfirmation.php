<?php

namespace App\Mail;

use App\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Subscriber $subscriber) {}

    public function envelope(): Envelope
    {
        $publication = $this->subscriber->publication;

        return new Envelope(
            from: $publication->from_email
                ? new Address($publication->from_email, $publication->from_name ?: $publication->name)
                : null,
            replyTo: $publication->reply_to_email
                ? [new Address($publication->reply_to_email)]
                : [],
            subject: 'Confirm your subscription to '.$publication->name,
        );
    }

    public function content(): Content
    {
        $publication = $this->subscriber->publication;

        return new Content(
            markdown: 'mail.subscription-confirmation',
            with: [
                'publication' => $publication,
                'confirmUrl' => route('public.confirm', [
                    'publication' => $publication->slug,
                    'token' => $this->subscriber->confirmation_token,
                ]),
            ],
        );
    }
}
