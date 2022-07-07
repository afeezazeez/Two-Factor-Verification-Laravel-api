<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * Store and send  new email.
     * @param LoginRequest $request
     * @return object
     */
    public function login(LoginRequest $request): object
    {
        if (!Auth::attempt($request->all())) {
            return $this->error("Credentials not match", Response::HTTP_UNAUTHORIZED);
        }

        return $this->success(
            [
                'token' => auth()->user()->createToken('API Token')->plainTextToken,
                '2fa_status'=>auth()->user()->get2FaStatus()
            ]
            ,'Login success',
            Response::HTTP_OK
        );
    }

    /**
     * Store and send  new email.
     * @param null
     * @return object
     */
    public function logout()
    {
        auth()->user()->tokens()->delete();
        return $this->success(
            null,'Logout success',
            Response::HTTP_OK
        );
    }
}
