<?php

use App\Helpers\Enums\RecipientType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

pest()->use(RefreshDatabase::class);

test('an authenticated user can export their pack as csv', function () {
    $user = User::factory()->create();
    $pack = $user->pack()->create([
        'name' => 'Test Pack',
        'date' => '2026-08-10',
    ]);
    $code = $pack->code()->create([
        'name' => 'Gift Code',
        'email' => 'recipient@example.com',
        'amount' => 500,
        'date' => null,
        'recipient_type' => RecipientType::SPECIFIC,
        'code' => 'ABC1234567',
        'hashcode' => hash('sha256', 'ABC1234567'),
    ]);

    $response = $this->actingAs($user)->get(route('export.pack', $pack));

    $expectedCsv = "ID,Name,Email,Amount,\"Send Date\",\"Recipient Type\",Code,\"Queued At\",\"Sent At\"\n";
    $expectedCsv .= "{$code->id},\"Gift Code\",recipient@example.com,500,,specific,ABC1234567,,\n";

    $response
        ->assertSuccessful()
        ->assertStreamed()
        ->assertHeader('Content-Type', 'text/csv; charset=utf-8')
        ->assertHeader('Content-Disposition', 'attachment; filename="pack-'.$pack->id.'.csv"')
        ->assertStreamedContent($expectedCsv);
});
