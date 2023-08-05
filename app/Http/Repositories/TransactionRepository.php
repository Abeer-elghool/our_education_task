<?php

namespace App\Http\Repositories;

use App\Models\Transaction;

class TransactionRepository
{
    public function getAll()
    {
        return Transaction::with('users')->get();
    }

    public function updateOrCreate($attributes, $values)
    {
        return Transaction::updateOrCreate($attributes, $values);
    }

    public function create($data)
    {
        return Transaction::create($data);
    }
}
