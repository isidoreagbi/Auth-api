<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Interfaces\AuthInterface;
use App\Responses\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\TryCatch;

class AuthController extends Controller
{
    private AuthInterface $authInterface;
    public function __construct(AuthInterface $authInterface){
        $this->authInterface = $authInterface;
    }

    public function register(RegisterRequest $registerRequest){
        $data = [
            'name' => $registerRequest->name, 
            'email' => $registerRequest->email,
            'password' => $registerRequest->password, 
        ];

        DB::beginTransaction();
        try {
            $user = $this->authInterface->register($data);

            DB::commit();

            return ApiResponse::sendResponse(true, [new UserResource($user)], 'Opération effectuée.', 201);
        } catch (\Throwable $th) {
            
            return ApiResponse::rollback($th);       
            }
    }
    public function login(LoginRequest $loginRequest){
        $data = [
            'email' => $loginRequest->email,
            'password' => $loginRequest->password,
        ];

        DB::beginTransaction();
        try {
            $user = $this->authInterface->login($data);

            DB::commit();

            return ApiResponse::sendResponse(
                $user,
                [],
                'Opération effectuée.', 
                $user ? 200 : 401
            );

        } catch (\Throwable $th) {
            
            return ApiResponse::rollback($th);       
            }
    }
}
