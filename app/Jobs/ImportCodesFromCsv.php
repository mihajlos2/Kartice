<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Helpers\CodeImportStatus;
use App\Models\Pack;
use App\Services\CodeCsvImporter;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ImportCodesFromCsv implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    /** @var array<int, int> */
    public array $backoff = [10, 30, 60];

    public int $timeout = 60;

    public function __construct(
        public string $filePath,
        public int $packId,
        public int $userId,
        public ?string $importId = null,
    ) {
        $this->onQueue('imports');
    }

    public function handle(CodeCsvImporter $importer): void
    {
        $this->markProcessing();

        $disk = Storage::disk('local');

        if (! $disk->exists($this->filePath)) {
            Log::warning('Code CSV import file no longer exists.', $this->logContext());
            $this->markFailed();

            return;
        }

        $pack = Pack::query()->find($this->packId);

        if ($pack === null) {
            Log::warning('Code CSV import was cancelled because the pack no longer exists.', $this->logContext());
            $this->markFailed();
            $this->deleteStoredFile();

            return;
        }

        if ($pack->user_id !== $this->userId) {
            Log::warning('Code CSV import was cancelled because pack ownership changed.', $this->logContext());
            $this->markFailed();
            $this->deleteStoredFile();

            return;
        }

        try {
            $result = DB::transaction(
                fn (): array => $importer->import(
                    $disk->path($this->filePath),
                    $pack,
                    $this->userId,
                ),
            );

            Log::info('Code CSV import finished.', [
                ...$this->logContext(),
                ...$result,
            ]);

            $this->deleteStoredFile();

            if ($result['aborted']) {
                $this->markFailed();

                return;
            }

            $this->markCompleted($result['imported'], $result['skipped']);
        } catch (Throwable $exception) {
            Log::error('Code CSV import attempt failed and will be retried when possible.', [
                ...$this->logContext(),
                'exception' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    public function failed(?Throwable $exception): void
    {
        Log::error('Code CSV import failed permanently.', [
            ...$this->logContext(),
            'exception' => $exception?->getMessage(),
        ]);

        $this->markFailed();
        $this->deleteStoredFile();
    }

    /**
     * @return array{file_path: string, pack_id: int, user_id: int, import_id: string|null}
     */
    private function logContext(): array
    {
        return [
            'file_path' => $this->filePath,
            'pack_id' => $this->packId,
            'user_id' => $this->userId,
            'import_id' => $this->importId,
        ];
    }

    private function markProcessing(): void
    {
        if ($this->importId !== null) {
            CodeImportStatus::markProcessing($this->importId, $this->packId, $this->userId);
        }
    }

    private function markCompleted(int $importedRows, int $skippedRows): void
    {
        if ($this->importId !== null) {
            CodeImportStatus::markCompleted(
                $this->importId,
                $this->packId,
                $this->userId,
                $importedRows,
                $skippedRows,
            );
        }
    }

    private function markFailed(): void
    {
        if ($this->importId !== null) {
            CodeImportStatus::markFailed($this->importId, $this->packId, $this->userId);
        }
    }

    private function deleteStoredFile(): void
    {
        try {
            $disk = Storage::disk('local');

            if ($disk->exists($this->filePath) && ! $disk->delete($this->filePath)) {
                Log::warning('Code CSV import file could not be deleted.', $this->logContext());
            }
        } catch (Throwable $exception) {
            Log::error('Code CSV import file cleanup failed.', [
                ...$this->logContext(),
                'exception' => $exception->getMessage(),
            ]);
        }
    }
}
