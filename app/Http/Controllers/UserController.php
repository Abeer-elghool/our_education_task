<?php

namespace App\Http\Controllers;

use App\Http\Repositories\UserRepository;
use App\Http\Requests\Api\User\UserJsonRequest;
use App\Http\Resources\Api\User\UserResource;
use App\Jobs\SaveUsersJob;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Get a list of users with optional filters.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Retrieve users with optional filters
        $users = $this->userRepository->getAllWithTransactions($request->all());

        return UserResource::collection($users)->additional(['message' => 'Done.'], 200);
    }

    /**
     * Save users from a JSON file using a background job.
     *
     * @param  \App\Http\Requests\UserJsonRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function save_users(UserJsonRequest $request): JsonResponse
    {
        // Retrieve the uploaded JSON file from the request
        $jsonFile = $request->file('file');

        // Store the JSON file in the 'public' disk under the 'users' directory
        $jsonFilePath = $jsonFile->store('users', 'public');

        // Dispatch a background job to save the users from the JSON file
        dispatch(new SaveUsersJob(asset("storage/$jsonFilePath"), $this->userRepository));

        // Return a success response indicating that the users are being saved
        return response()->json(['message' => 'Users are being saved.'], 200);
    }
}
