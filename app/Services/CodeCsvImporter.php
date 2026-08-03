<?php

declare(strict_types=1);

namespace App\Services;

use App\Helpers\CodeGenerator;
use App\Helpers\Enums\RecipientType;
use App\Models\Code;
use App\Models\Pack;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use SplFileObject;

class CodeCsvImporter
{
    /**
     * @var array<int, string>
     */
    private const REQUIRED_HEADERS = [
        'ID',
        'Name',
        'Email',
        'Amount',
        'Send Date',
        'Recipient Type',
        'Code',
        'Queued At',
        'Sent At',
    ];

    /**
     * @return array{imported: int, skipped: int, aborted: bool}
     */
    public function import(string $absoluteFilePath, Pack $pack, int $userId): array
    {
        $csvFile = new SplFileObject($absoluteFilePath, 'r');
        $headerRow = $csvFile->fgetcsv(',', '"', '');

        if (! is_array($headerRow)) {
            $this->logInvalidHeaders($pack, $userId, self::REQUIRED_HEADERS, []);

            return $this->abortedResult();
        }

        $headerIndexes = $this->headerIndexes($headerRow, $pack, $userId);

        if ($headerIndexes === null) {
            return $this->abortedResult();
        }

        $importedRows = 0;
        $skippedRows = 0;
        $rowNumber = 1;

        while (! $csvFile->eof()) {
            $row = $csvFile->fgetcsv(',', '"', '');
            $rowNumber++;

            if (! is_array($row) || $this->isEmptyRow($row)) {
                continue;
            }

            $row = $this->restoreLegacyMissingId($row, $headerIndexes);
            $rowData = $this->rowData($row, $headerIndexes);
            $validator = Validator::make($rowData, $this->validationRules());

            if ($validator->fails()) {
                Log::warning('Code CSV row was skipped because validation failed.', [
                    'pack_id' => $pack->id,
                    'user_id' => $userId,
                    'row_number' => $rowNumber,
                    'errors' => $validator->errors()->toArray(),
                ]);

                $skippedRows++;

                continue;
            }

            $validated = $validator->validated();
            $newCode = CodeGenerator::generate_code();

            Code::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'amount' => (int) $validated['amount'],
                'date' => $this->futureSendDate($validated['send_date'] ?? null),
                'recipient_type' => RecipientType::from($validated['recipient_type']),
                'code' => $newCode,
                'hashcode' => hash('sha256', $newCode),
                'pack_id' => $pack->id,
            ]);

            $importedRows++;
        }

        return [
            'imported' => $importedRows,
            'skipped' => $skippedRows,
            'aborted' => false,
        ];
    }

    /**
     * @param  array<int, string|null>  $headerRow
     * @return array<string, int>|null
     */
    private function headerIndexes(array $headerRow, Pack $pack, int $userId): ?array
    {
        $headers = array_map(
            static fn (mixed $header): string => trim((string) $header),
            $headerRow,
        );

        if (isset($headers[0])) {
            $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', $headers[0]) ?? $headers[0];
        }

        $headerIndexes = [];
        $duplicateHeaders = [];

        foreach ($headers as $index => $header) {
            if (array_key_exists($header, $headerIndexes)) {
                $duplicateHeaders[] = $header;

                continue;
            }

            $headerIndexes[$header] = $index;
        }

        $missingHeaders = array_values(array_diff(
            self::REQUIRED_HEADERS,
            array_keys($headerIndexes),
        ));
        $duplicateRequiredHeaders = array_values(array_intersect(
            self::REQUIRED_HEADERS,
            $duplicateHeaders,
        ));

        if ($missingHeaders !== [] || $duplicateRequiredHeaders !== []) {
            $this->logInvalidHeaders(
                $pack,
                $userId,
                $missingHeaders,
                $duplicateRequiredHeaders,
            );

            return null;
        }

        return $headerIndexes;
    }

    /**
     * @param  array<int, string|null>  $row
     * @param  array<string, int>  $headerIndexes
     * @return array<int, string|null>
     */
    private function restoreLegacyMissingId(array $row, array $headerIndexes): array
    {
        if (count($row) !== count(self::REQUIRED_HEADERS) - 1) {
            return $row;
        }

        foreach (self::REQUIRED_HEADERS as $index => $header) {
            if (($headerIndexes[$header] ?? null) !== $index) {
                return $row;
            }
        }

        array_unshift($row, null);

        return $row;
    }

    /**
     * @param  array<int, string|null>  $row
     * @param  array<string, int>  $headerIndexes
     * @return array<string, string|null>
     */
    private function rowData(array $row, array $headerIndexes): array
    {
        return [
            'name' => $this->trimmedValue($row[$headerIndexes['Name']] ?? null),
            'email' => $this->trimmedValue($row[$headerIndexes['Email']] ?? null),
            'amount' => $this->trimmedValue($row[$headerIndexes['Amount']] ?? null),
            'send_date' => $this->nullableTrimmedValue($row[$headerIndexes['Send Date']] ?? null),
            'recipient_type' => $this->trimmedValue($row[$headerIndexes['Recipient Type']] ?? null),
        ];
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function validationRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'amount' => ['required', 'integer', 'min:1', 'max:255'],
            'recipient_type' => ['required', Rule::enum(RecipientType::class)],
            'send_date' => ['nullable', 'date'],
        ];
    }

    private function futureSendDate(?string $sendDate): ?Carbon
    {
        if ($sendDate === null) {
            return null;
        }

        $parsedSendDate = Carbon::parse(
            $sendDate,
            (string) config('app.timezone'),
        );

        return $parsedSendDate->isFuture() ? $parsedSendDate : null;
    }

    /**
     * @param  array<int, string|null>  $row
     */
    private function isEmptyRow(array $row): bool
    {
        foreach ($row as $value) {
            if ($value !== null && trim($value) !== '') {
                return false;
            }
        }

        return true;
    }

    private function trimmedValue(mixed $value): ?string
    {
        return $value === null ? null : trim((string) $value);
    }

    private function nullableTrimmedValue(mixed $value): ?string
    {
        $trimmedValue = $this->trimmedValue($value);

        return $trimmedValue === '' ? null : $trimmedValue;
    }

    /**
     * @param  array<int, string>  $missingHeaders
     * @param  array<int, string>  $duplicateHeaders
     */
    private function logInvalidHeaders(
        Pack $pack,
        int $userId,
        array $missingHeaders,
        array $duplicateHeaders,
    ): void {
        Log::error('Code CSV import was aborted because its headers are invalid.', [
            'pack_id' => $pack->id,
            'user_id' => $userId,
            'missing_headers' => $missingHeaders,
            'duplicate_headers' => $duplicateHeaders,
        ]);
    }

    /**
     * @return array{imported: int, skipped: int, aborted: bool}
     */
    private function abortedResult(): array
    {
        return [
            'imported' => 0,
            'skipped' => 0,
            'aborted' => true,
        ];
    }
}
