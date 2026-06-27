<?php

namespace App\Mail;

use App\Models\Issue;
use App\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class IssueNewsletter extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Issue $issue,
        public Subscriber $subscriber,
    ) {}

    public function envelope(): Envelope
    {
        $publication = $this->issue->publication;

        return new Envelope(
            from: $publication->from_email
                ? new Address($publication->from_email, $publication->from_name ?: $publication->name)
                : null,
            replyTo: $publication->reply_to_email
                ? [new Address($publication->reply_to_email)]
                : [],
            subject: $this->issue->title,
        );
    }

    public function content(): Content
    {
        $publication = $this->issue->publication;

        return new Content(
            view: 'emails.issue',
            with: [
                'issue' => $this->issue,
                'publication' => $publication,
                'stories' => $this->issue->stories,
                'unsubscribeUrl' => route('public.unsubscribe', [
                    'publication' => $publication->slug,
                    'token' => $this->subscriber->unsubscribe_token,
                ]),
            ],
        );
    }
}
