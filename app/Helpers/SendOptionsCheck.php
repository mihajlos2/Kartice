<?php

declare(strict_types=1);

namespace App\Helpers;

use Carbon\Carbon;

class SendOptionsCheck
{
    public static function resolveSendDate(string $sendOption, ?string $sendAt = null): Carbon|string|null
    {
        return match ($sendOption) {
            'send_at' => $sendAt,
            'instant' => now()->addMinutes(5),
            'no_date' => 'no send'
        };
    }
}
