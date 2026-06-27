<?php

namespace App\Console\Commands;

use App\Jobs\SendIssue;
use App\Models\Issue;
use Illuminate\Console\Command;

class SendScheduledIssues extends Command
{
    protected $signature = 'issues:send-scheduled';

    protected $description = 'Dispatch sending for scheduled issues whose publish time has arrived';

    public function handle(): int
    {
        $due = Issue::where('status', 'scheduled')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->get();

        foreach ($due as $issue) {
            SendIssue::dispatch($issue);
            $this->info("Dispatched issue #{$issue->id} ({$issue->title}).");
        }

        $this->info("{$due->count()} scheduled issue(s) dispatched.");

        return self::SUCCESS;
    }
}
