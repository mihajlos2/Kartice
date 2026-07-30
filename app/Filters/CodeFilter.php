<?php

declare(strict_types=1);

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

final class CodeFilter
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public function apply(Builder $query, array $filters): Builder
    {
        $status = $filters['status'] ?? 'all';

        if ($status === 'sent') {
            $query->whereNotNull('sent_at');
        }

        if ($status === 'unsent') {
            $query->whereNull('sent_at');
        }

        if (! empty($filters['recipient_type'])) {
            $query->where(
                'recipient_type',
                $filters['recipient_type']
            );
        }

        [$column, $direction] = match ($filters['order'] ?? 'date_desc') {
            'date_asc' => ['date', 'asc'],
            'amount_asc' => ['amount', 'asc'],
            'amount_desc' => ['amount', 'desc'],
            default => ['date', 'desc'],
        };

        return $query->orderBy($column, $direction);
    }
}
