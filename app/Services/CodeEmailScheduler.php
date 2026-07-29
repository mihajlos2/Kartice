<?php

namespace App\Services;

use App\Jobs\SendCodeEmail;
use App\Models\Code;

    class CodeEmailScheduler
{
    public function schedule(Code $code): void
    {
        if ($code->date === null) {
            return;
        }

        $scheduledFor = $code->date->toImmutable();

        SendCodeEmail::dispatch($code, $scheduledFor)
            ->delay($scheduledFor);
    }
}
