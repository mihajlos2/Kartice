<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

final class CodeImportStatus
{
    public const QUEUED = 'queued';

    public const PROCESSING = 'processing';

    public const COMPLETED = 'completed';

    public const FAILED = 'failed';

    private const CACHE_TTL_SECONDS = 86400;

    public static function markQueued(string $importId, int $packId, int $userId): void
    {
        self::store($importId, $packId, $userId, self::QUEUED);
    }

    public static function markProcessing(string $importId, int $packId, int $userId): void
    {
        self::store($importId, $packId, $userId, self::PROCESSING);
    }

    public static function markCompleted(
        string $importId,
        int $packId,
        int $userId,
        int $importedRows,
        int $skippedRows,
    ): void {
        self::store($importId, $packId, $userId, self::COMPLETED, [
            'imported' => $importedRows,
            'skipped' => $skippedRows,
        ]);
    }

    public static function markFailed(string $importId, int $packId, int $userId): void
    {
        self::store($importId, $packId, $userId, self::FAILED);
    }

    /**
     * @return array{status: string, pack_id: int, user_id: int, imported?: int, skipped?: int}|null
     */
    public static function find(string $importId): ?array
    {
        $status = Cache::get(self::cacheKey($importId));

        if (
            ! is_array($status)
            || ! is_string($status['status'] ?? null)
            || ! in_array($status['status'], [
                self::QUEUED,
                self::PROCESSING,
                self::COMPLETED,
                self::FAILED,
            ], true)
            || ! is_int($status['pack_id'] ?? null)
            || ! is_int($status['user_id'] ?? null)
        ) {
            return null;
        }

        return $status;
    }

    public static function forget(string $importId): void
    {
        Cache::forget(self::cacheKey($importId));
    }

    /**
     * @param  array<string, int>  $details
     */
    private static function store(
        string $importId,
        int $packId,
        int $userId,
        string $status,
        array $details = [],
    ): void {
        Cache::put(self::cacheKey($importId), [
            ...$details,
            'status' => $status,
            'pack_id' => $packId,
            'user_id' => $userId,
        ], self::CACHE_TTL_SECONDS);
    }

    private static function cacheKey(string $importId): string
    {
        return 'code-import-status:'.$importId;
    }
}
