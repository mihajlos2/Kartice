<?php

use App\Helpers\Enums\RecipientType;
use App\Jobs\SendCodeEmail;
use App\Models\Code;
use App\Models\User;
use App\Notifications\CodeSender;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;

pest()->use(RefreshDatabase::class);

test('an authenticated user can create a code for their pack', function () {
    Notification::fake();

    $user = User::factory()->create();
    $pack = $user->pack()->create([
        'name' => 'Test Pack',
        'date' => '2026-08-10',
    ]);

    $response = $this->actingAs($user)->post(route('store.code', $pack), [
        'recipient_name' => 'Gift Recipient',
        'recipient_email' => 'recipient@example.com',
        'amount' => 100,
        'recipient_type' => RecipientType::SPECIFIC->value,
        'send_options' => 'no_date',
    ]);

    $code = Code::query()->sole();

    $this->assertDatabaseHas('codes', [
        'id' => $code->id,
        'pack_id' => $pack->id,
        'name' => 'Gift Recipient',
        'email' => 'recipient@example.com',
        'amount' => 100,
        'date' => null,
        'recipient_type' => RecipientType::SPECIFIC->value,
    ]);

    expect($code->code)
        ->toHaveLength(10)
        ->and($code->hashcode)
        ->toBe(hash('sha256', $code->code));

    $response
        ->assertRedirectToRoute('create.code', ['pack' => $pack])
        ->assertSessionHas('success');

    Notification::assertSentTo($user, CodeSender::class);
});

test('a code is not created when the recipient email is invalid', function () {
    Notification::fake();

    $user = User::factory()->create();
    $pack = $user->pack()->create([
        'name' => 'Test Pack',
        'date' => '2026-08-10',
    ]);

    $response = $this->actingAs($user)->post(route('store.code', $pack), [
        'recipient_name' => 'Gift Recipient',
        'recipient_email' => 'invalid-email',
        'amount' => 100,
        'recipient_type' => RecipientType::SPECIFIC->value,
        'send_options' => 'no_date',
    ]);

    $response->assertInvalid(['recipient_email']);

    $this->assertDatabaseEmpty('codes');

    Notification::assertNothingSent();
});

test('a user cannot create a code for another users pack', function () {
    Notification::fake();

    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $pack = $owner->pack()->create([
        'name' => 'Owners Pack',
        'date' => '2026-08-10',
    ]);

    $response = $this->actingAs($otherUser)->post(route('store.code', $pack), [
        'recipient_name' => 'Gift Recipient',
        'recipient_email' => 'recipient@example.com',
        'amount' => 100,
        'recipient_type' => RecipientType::SPECIFIC->value,
        'send_options' => 'no_date',
    ]);

    $response->assertForbidden();

    $this->assertDatabaseEmpty('codes');

    Notification::assertNothingSent();
});

test('a code is not created with invalid input', function (array $invalidInput, string $invalidField) {
    Notification::fake();

    $user = User::factory()->create();
    $pack = $user->pack()->create([
        'name' => 'Test Pack',
        'date' => '2026-08-10',
    ]);
    $validInput = [
        'recipient_name' => 'Gift Recipient',
        'recipient_email' => 'recipient@example.com',
        'amount' => 100,
        'recipient_type' => RecipientType::SPECIFIC->value,
        'send_options' => 'no_date',
    ];

    $response = $this->actingAs($user)->post(
        route('store.code', $pack),
        [...$validInput, ...$invalidInput],
    );

    $response->assertInvalid([$invalidField]);

    $this->assertDatabaseEmpty('codes');

    Notification::assertNothingSent();
})->with([
    'amount above the maximum' => [['amount' => 256], 'amount'],
    'amount equal to zero' => [['amount' => 0], 'amount'],
    'negative amount' => [['amount' => -50], 'amount'],
    'missing recipient name' => [['recipient_name' => null], 'recipient_name'],
    'invalid recipient type' => [['recipient_type' => 'invalid'], 'recipient_type'],
]);

test('a code is not created when the send date is in the past', function () {
    Notification::fake();

    $user = User::factory()->create();
    $pack = $user->pack()->create([
        'name' => 'Test Pack',
        'date' => '2026-08-10',
    ]);

    $response = $this->actingAs($user)->post(route('store.code', $pack), [
        'recipient_name' => 'Gift Recipient',
        'recipient_email' => 'recipient@example.com',
        'amount' => 100,
        'recipient_type' => RecipientType::SPECIFIC->value,
        'send_options' => 'send_at',
        'send_at' => now()->subDay()->toDateTimeString(),
    ]);

    $response->assertInvalid(['send_at']);

    $this->assertDatabaseEmpty('codes');

    Notification::assertNothingSent();
});

test('a guest cannot create a code', function () {
    Notification::fake();

    $owner = User::factory()->create();
    $pack = $owner->pack()->create([
        'name' => 'Test Pack',
        'date' => '2026-08-10',
    ]);

    $response = $this->post(route('store.code', $pack), [
        'recipient_name' => 'Gift Recipient',
        'recipient_email' => 'recipient@example.com',
        'amount' => 100,
        'recipient_type' => RecipientType::SPECIFIC->value,
        'send_options' => 'no_date',
    ]);

    $response->assertRedirectToRoute('login');

    $this->assertDatabaseEmpty('codes');

    Notification::assertNothingSent();
});

test('an instant code is queued for sending immediately', function () {
    Notification::fake();
    Queue::fake();

    $now = $this->freezeSecond();
    $user = User::factory()->create();
    $pack = $user->pack()->create([
        'name' => 'Test Pack',
        'date' => '2026-08-10',
    ]);

    $response = $this->actingAs($user)->post(route('store.code', $pack), [
        'recipient_name' => 'Gift Recipient',
        'recipient_email' => 'recipient@example.com',
        'amount' => 100,
        'recipient_type' => RecipientType::SPECIFIC->value,
        'send_options' => 'instant',
    ]);

    $code = Code::query()->sole();

    expect($code->date?->equalTo($now))->toBeTrue()
        ->and($code->queued_at?->equalTo($now))->toBeTrue();

    $response->assertRedirectToRoute('create.code', ['pack' => $pack]);

    Queue::assertPushed(
        SendCodeEmail::class,
        fn (SendCodeEmail $job): bool => $job->code->is($code),
    );
});

test('a code is not created with invalid scheduling input', function (array $schedulingInput, string $invalidField) {
    Notification::fake();

    $user = User::factory()->create();
    $pack = $user->pack()->create([
        'name' => 'Test Pack',
        'date' => '2026-08-10',
    ]);

    $response = $this->actingAs($user)->post(route('store.code', $pack), [
        'recipient_name' => 'Gift Recipient',
        'recipient_email' => 'recipient@example.com',
        'amount' => 100,
        'recipient_type' => RecipientType::SPECIFIC->value,
        ...$schedulingInput,
    ]);

    $response->assertInvalid([$invalidField]);

    $this->assertDatabaseEmpty('codes');

    Notification::assertNothingSent();
})->with([
    'missing send date' => [['send_options' => 'send_at'], 'send_at'],
    'invalid send option' => [['send_options' => 'later'], 'send_options'],
]);

test('a code can be created for the bulk recipient type', function () {
    Notification::fake();

    $user = User::factory()->create();
    $pack = $user->pack()->create([
        'name' => 'Test Pack',
        'date' => '2026-08-10',
    ]);

    $response = $this->actingAs($user)->post(route('store.code', $pack), [
        'recipient_name' => 'Bulk Recipient',
        'recipient_email' => 'bulk@example.com',
        'amount' => 100,
        'recipient_type' => RecipientType::BULK->value,
        'send_options' => 'no_date',
    ]);

    $code = Code::query()->sole();

    expect($code->recipient_type)->toBe(RecipientType::BULK);

    $this->assertDatabaseHas('codes', [
        'id' => $code->id,
        'pack_id' => $pack->id,
        'recipient_type' => RecipientType::BULK->value,
    ]);

    $response->assertRedirectToRoute('create.code', ['pack' => $pack]);
});

test('each created code receives a unique generated value', function () {
    Notification::fake();

    $user = User::factory()->create();
    $pack = $user->pack()->create([
        'name' => 'Test Pack',
        'date' => '2026-08-10',
    ]);
    $validInput = [
        'recipient_name' => 'Gift Recipient',
        'recipient_email' => 'recipient@example.com',
        'amount' => 100,
        'recipient_type' => RecipientType::SPECIFIC->value,
        'send_options' => 'no_date',
    ];

    $this->actingAs($user)->post(route('store.code', $pack), $validInput);
    $this->actingAs($user)->post(route('store.code', $pack), [
        ...$validInput,
        'recipient_email' => 'second@example.com',
    ]);

    $generatedCodes = Code::query()->pluck('code');

    $this->assertDatabaseCount('codes', 2);

    expect($generatedCodes)
        ->toHaveCount(2)
        ->and($generatedCodes->unique())
        ->toHaveCount(2);
});
