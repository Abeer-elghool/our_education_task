<?php

namespace App\Http\Repositories;

use App\Enums\TransactionStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class UserRepository
{
    /**
     * Get all users with optional filters.
     *
     * @param  array  $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllWithTransactions(array $filters = [])
    {
        $query = User::with('transactions');

        $this->applyFilters($query, $filters);

        return $query->get();
    }

    public function updateOrCreate($attributes, $values)
    {
        return User::updateOrCreate($attributes, $values);
    }

    /**
     * Apply optional filters to the query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  array  $filters
     * @return void
     */
    protected function applyFilters(Builder $query, array $filters)
    {
        if (isset($filters['status_code'])) {
            $filter = TransactionStatus::get_status_code($filters['status_code']);
            $query->whereHas('transactions', fn ($query) => $query->where('transactions.statusCode', $filter));
        }

        if (isset($filters['currency'])) {
            $query->where('currency', $filters['currency']);
        }

        if (!empty($filters['min_amount'])) {
            $query->whereHas('transactions', fn ($query) => $query->where('paidAmount', '>=', $filters['min_amount']));
        }

        if (!empty($filters['max_amount'])) {
            $query->whereHas('transactions', fn ($query) => $query->where('paidAmount', '<=', $filters['max_amount']));
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereHas('transactions', fn ($query) => $query->whereBetween('paymentDate', [$filters['start_date'], $filters['end_date']]));
        }
    }
}
