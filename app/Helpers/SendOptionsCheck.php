<?php

declare(strict_types=1);

namespace App\Helpers;

use Carbon\Carbon;

class SendOptionsCheck
{
    public static function resolveSendDate(string $sendOption, ?string $sendAt = null): ?Carbon
    {
        return match ($sendOption) {
            'send_at' => $sendAt !== null ? Carbon::parse($sendAt) : null,
            'instant' => now(),
            'no_date' => null,
        };
    }
}
