<?php

namespace App\Jobs;

use App\Mail\IssueNewsletter;
use App\Models\Issue;
use App\Models\IssueDelivery;
use App\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendIssue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Issue $issue) {}

    public function handle(): void
    {
        $issue = $this->issue->loadMissing('publication', 'stories');
        $publication = $issue->publication;
        $mailerName = $publication->configuredMailerName();

        // Subscribers already sent this issue — never send twice.
        $alreadySent = $issue->deliveries()->pluck('subscriber_id')->all();

        $publication->subscribers()
            ->mailable()
            ->whereNotIn('id', $alreadySent)
            ->chunkById(200, function ($subscribers) use ($issue, $publication, $mailerName) {
                foreach ($subscribers as $subscriber) {
                    $this->deliver($issue, $publication->id, $subscriber, $mailerName);
                }
            });

        $issue->markSent();
    }

    protected function deliver(Issue $issue, int $publicationId, Subscriber $subscriber, string $mailerName): void
    {
        try {
            Mail::mailer($mailerName)
                ->to($subscriber->email)
                ->send(new IssueNewsletter($issue, $subscriber));

            $issue->deliveries()->create([
                'subscriber_id' => $subscriber->id,
                'publication_id' => $publicationId,
                'status' => IssueDelivery::STATUS_SENT,
                'sent_at' => now(),
            ]);
        } catch (Throwable $e) {
            $issue->deliveries()->create([
                'subscriber_id' => $subscriber->id,
                'publication_id' => $publicationId,
                'status' => IssueDelivery::STATUS_FAILED,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
