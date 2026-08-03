<?php

declare(strict_types=1);

use App\Helpers\SendOptionsCheck;
use Carbon\Carbon;

afterEach(function () {
    Carbon::setTestNow();
});

test('no date option returns null', function () {
    expect(SendOptionsCheck::resolveSendDate('no_date'))->toBeNull();
});

test('send at option returns the provided date', function () {
    $sendDate = SendOptionsCheck::resolveSendDate('send_at', '2026-08-10 12:30:00');

    expect($sendDate)
        ->toBeInstanceOf(Carbon::class)
        ->and($sendDate?->toDateTimeString())
        ->toBe('2026-08-10 12:30:00');
});

test('instant option returns the current time', function () {
    $now = Carbon::parse('2026-08-03 12:00:00');
    Carbon::setTestNow($now);

    $sendDate = SendOptionsCheck::resolveSendDate('instant');

    expect($sendDate)
        ->toBeInstanceOf(Carbon::class)
        ->and($sendDate?->toDateTimeString())
        ->toBe($now->toDateTimeString());
});
