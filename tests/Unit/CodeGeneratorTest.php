<?php

declare(strict_types=1);

use App\Helpers\CodeGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)->use(RefreshDatabase::class);

test('it generates a unique ten character code', function () {
    $generatedCode = CodeGenerator::generate_code();

    expect($generatedCode)
        ->toBeString()
        ->toHaveLength(10);

    $this->assertDatabaseMissing('codes', ['code' => $generatedCode]);
});
