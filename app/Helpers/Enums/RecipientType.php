<?php

declare(strict_types=1);

namespace App\Helpers\Enums;

enum RecipientType: string
{
    case BULK = 'bulk';
    case SPECIFIC = 'specific';

    public function label(): string
    {
        return match ($this) {
            self::SPECIFIC => 'Specify Recipient',
            self::BULK => 'Bulk Store Credit Code',
        };
    }
}
