<?php

declare(strict_types=1);

namespace App\Services;

use App\Jobs\SendCodeEmail;
use App\Models\Code;
use Illuminate\Support\Facades\DB;

class CodeEmailDispatcher
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function dispatch(Code $code): void
    {
        DB::transaction(function () use ($code): void {
            $queuedAt = now();

            $updated = Code::query()
                ->whereKey($code->id)
                ->whereNotNull('date')
                ->where('date', '<=', $queuedAt)
                ->whereNull('queued_at')
                ->whereNull('sent_at')
                ->update([
                    'queued_at' => $queuedAt,
                ]);

            if ($updated === 0) {
                return;
            }

            SendCodeEmail::dispatch($code)->afterCommit();

        });
    }
}
