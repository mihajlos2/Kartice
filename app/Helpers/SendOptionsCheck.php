<?php

namespace App\Helpers;
use Carbon\Carbon;


class SendOptionsCheck
{
    public static function resolveSendDate(string $sendOption, ?string $sendAt = null): Carbon|string|null {
        return match ($sendOption) {
            'send_at' => $sendAt,
            'instant' => now()->addMinutes(5),
            'no date' => 'no send'
        };
    }
}
