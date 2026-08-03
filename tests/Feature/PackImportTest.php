<?php

use App\Jobs\ImportCodesFromCsv;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

pest()->use(RefreshDatabase::class);

test('an authenticated user can queue a csv import for their pack', function () {
    Queue::fake();
    Storage::fake('local');

    $user = User::factory()->create();
    $pack = $user->pack()->create([
        'name' => 'Test Pack',
        'date' => '2026-08-10',
    ]);
    $csvFile = UploadedFile::fake()->createWithContent(
        'codes.csv',
        "ID,Name,Email,Amount,Send Date,Recipient Type,Code,Queued At,Sent At\n".
        "1,Gift Code,recipient@example.com,500,,specific,ABC1234567,,\n",
    );

    $response = $this->actingAs($user)->post(route('codes.import', $pack), [
        'csv_file' => $csvFile,
    ]);

    $storedPath = 'code-imports/'.$csvFile->hashName();

    $response
        ->assertRedirectToRoute('show.code', ['pack' => $pack])
        ->assertSessionHas('success')
        ->assertSessionHas('code_import_id');

    Storage::disk('local')->assertExists($storedPath);

    Queue::assertPushedOn('imports', ImportCodesFromCsv::class);
    Queue::assertPushed(
        ImportCodesFromCsv::class,
        fn (ImportCodesFromCsv $job): bool => $job->filePath === $storedPath
            && $job->packId === $pack->id
            && $job->userId === $user->id
            && $job->importId !== null,
    );
});
