<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Code;
use App\Services\CodeEmailDispatcher;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('codes:queue-scheduled-emails')]
#[Description('Command description')]
class QueueScheduledCodeEmails extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(CodeEmailDispatcher $emailDispatcher): int
    {
        Code::query()
            ->whereNotNull('date')
            ->where('date', '<=', now())
            ->whereNull('queued_at')
            ->whereNull('sent_at')
            ->eachById(function (Code $code) use ($emailDispatcher): void {
                $emailDispatcher->dispatch($code);
            });

        return self::SUCCESS;
    }
}
