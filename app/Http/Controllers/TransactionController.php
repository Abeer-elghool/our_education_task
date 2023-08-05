<?php

namespace App\Http\Controllers;

use App\Http\Repositories\TransactionRepository;
use App\Http\Requests\Api\Transaction\TransactionRequest;
use App\Jobs\SaveTransactionsJob;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    protected $transactionRepository;

    public function __construct(TransactionRepository $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    public function save_transactions(TransactionRequest $request)
    {
        $jsonFile = $request->file('file');
        $jsonFilePath = $jsonFile->store('transactions', 'public');
        dispatch(new SaveTransactionsJob(asset("storage/$jsonFilePath"), $this->transactionRepository));
        return response()->json(['message' => 'Transactions are being saved.'], 200);
    }
}
