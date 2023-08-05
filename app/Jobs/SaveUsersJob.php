<?php

namespace App\Jobs;

use App\Http\Repositories\UserRepository;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Validator;
use DB;

class SaveUsersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $json_file_path;
    protected $user_repository;

    public function __construct($json_file_path, UserRepository $user_repository)
    {
        $this->json_file_path = $json_file_path;
        $this->user_repository = $user_repository;
    }

    public function handle()
    {
        $json_data = file_get_contents($this->json_file_path);
        $users_data = json_decode($json_data, true)['users'];


        try {
            DB::beginTransaction();
            foreach ($users_data as $user_data) {

                if ($this->is_valid($user_data) == false) {
                    continue;
                }

                $create_date = Carbon::createFromFormat('d/m/Y', $user_data['created_at']);

                $this->user_repository->updateOrCreate(
                    ['email' => $user_data['email']],
                    [
                        'balance' => $user_data['balance'],
                        'currency' => $user_data['currency'],
                        'created_at' => $create_date,
                        'id' => $user_data['id']
                    ]
                );
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            info($e);
        }
    }

    function is_valid($user_data)
    {
        $rules = [
            'balance' => 'required|numeric',
            'currency' => 'required|string',
            'email' => 'required|email|unique:users',
            'created_at' => 'required',
            'id' => 'required|unique:users',
        ];

        $validator = Validator::make($user_data, $rules);

        if ($validator->fails()) {
            return false;
        }

        return true;
    }
}
