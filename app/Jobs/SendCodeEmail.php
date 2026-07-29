<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Code;
use App\Notifications\CodeSenderUser;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Support\Facades\Notification;

class SendCodeEmail implements ShouldQueue
{
    use Queueable;

    public bool $deleteWhenMissingModels = true;

    public function __construct(public Code $code) // public CarbonImmutable $scheduledFor
    {
        return [
            (new RateLimited('mailtrap'))->releaseAfter(11),
        ];
    }

    public function handle(): void
    {
        if ($this->code->sent_at !== null) {
            return;
        }

        if ($this->code->date === null || $this->code->date->isFuture()) {
            $this->code->update([
                'queued_at' => null,
            ]);

            return;
        }

        Notification::route('mail', $this->code->email)
            ->notify(new CodeSenderUser($this->code));

        $this->code->update([
            'sent_at' => now(),
        ]);
    }
}
