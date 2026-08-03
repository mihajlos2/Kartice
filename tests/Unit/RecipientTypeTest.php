<?php

declare(strict_types=1);

use App\Helpers\Enums\RecipientType;

test('recipient types have the expected values and labels', function (
    RecipientType $recipientType,
    string $value,
    string $label,
) {
    expect($recipientType->value)
        ->toBe($value)
        ->and($recipientType->label())
        ->toBe($label);
})->with([
    'specific recipient' => [
        RecipientType::SPECIFIC,
        'specific',
        'Specify Recipient',
    ],
    'bulk recipient' => [
        RecipientType::BULK,
        'bulk',
        'Bulk Store Credit Code',
    ],
]);
