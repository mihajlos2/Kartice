<?php

use App\Models\Pack;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

pest()->use(RefreshDatabase::class);

test('an authenticated user can create a pack', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('packs.store'), [
        'name' => 'Test Pack',
        'date' => '2026-08-10',
    ]);

    $pack = Pack::query()->sole();

    $this->assertDatabaseHas('packs', [
        'id' => $pack->id,
        'user_id' => $user->id,
        'name' => 'Test Pack',
        'date' => '2026-08-10',
    ]);

    $response->assertRedirectToRoute('create.code', ['pack' => $pack]);
});
