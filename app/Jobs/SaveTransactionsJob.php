<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Validator;
use App\Http\Repositories\TransactionRepository;
use Carbon\Carbon;
use DB;

class SaveTransactionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $json_file_path;
    protected $transaction_repository;

    public function __construct($json_file_path, TransactionRepository $transaction_repository)
    {
        $this->json_file_path = $json_file_path;
        $this->transaction_repository = $transaction_repository;
    }

    public function handle()
    {
        $json_data = file_get_contents($this->json_file_path);
        $transactions_data = json_decode($json_data, true)['transactions'];


        try {
            DB::beginTransaction();
            foreach ($transactions_data as $transaction_data) {

                if ($this->is_valid($transaction_data) == false) {
                    continue;
                }

                $transaction_data['paymentDate'] = Carbon::createFromFormat('Y-m-d', $transaction_data['paymentDate']);

                $this->transaction_repository->create($transaction_data);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            info($e);
        }
    }

    function is_valid($transaction_data)
    {
        $rules = [
            'paidAmount' => 'required|numeric',
            'Currency' => 'required|string',
            'parentEmail' => 'required|email',
            'statusCode' => 'required|in:1,2,3',
            'paymentDate' => 'required|date',
            'parentIdentification' => 'required|string',
        ];

        $validator = Validator::make($transaction_data, $rules);

        if ($validator->fails()) {
            return false;
        }

        return true;
    }
}
