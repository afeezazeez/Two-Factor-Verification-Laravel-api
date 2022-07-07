<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Jobs\SendTwoFactorToken;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Jerry\JWT\JWT;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * Login user.
     * @param LoginRequest $request
     * @return object
     */
    public function login(LoginRequest $request): object
    {
        // Check if email exists

        if (!$user = User::where('email',$request->validated()['email'])->first()) {
            return $this->error("This email is not associated with any user", Response::HTTP_UNAUTHORIZED);
        }

        // Check if password matches with DB Password

        if(!Hash::check($request->validated()['password'], $user->password)){
            return $this->error("Incorrect password detected!", Response::HTTP_UNAUTHORIZED);
        }

        // Check If 2FA is enabled

        if($user->get2FaStatus()){

            // generate 2fa code
            $user->generateTwoFactorCode();

            // Send token to User Via email
            SendTwoFactorToken::dispatch($user);

            return $this->success(
                [
                    '2fa_token' => JWT::encode( ['user_id'=>$user->id]),
                    '2fa_status'=>$user->get2FaStatus()
                ]
                ,'Two Factor Verification is required!',
                Response::HTTP_OK
            );

        } else{
            auth()->login($user);
            return $this->success(
                [
                    'token' => auth()->user()->createToken('API Token')->plainTextToken,
                    '2fa_status'=>auth()->user()->get2FaStatus()
                ]
                ,'Login success',
                Response::HTTP_OK
            );
        }
    }

    /**
     * Logout user
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
