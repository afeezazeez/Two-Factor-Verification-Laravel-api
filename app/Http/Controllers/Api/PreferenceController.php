<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TwoFactorRequest;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Response;
use Jerry\JWT\JWT;

class PreferenceController extends Controller
{
    use ApiResponse;

    /**
     * get user preference 1.e 2fa status e.t.c
     * @param null
     * @return object
     */
    public function getPreference(): object
    {
        return $this->success(
            ['2fa_status'=>auth()->user()->get2faStatus()],null,
            Response::HTTP_OK
        );
    }

    /**
     * enable user 2fa
     * @param null
     * @return object
     */
    public function enable2fa(): object
    {
        auth()->user()->enable2FA();
        return $this->success(
            null,'2FA enabled!',
            Response::HTTP_OK
        );
    }

    /**
     * enable user 2fa
     * @param null
     * @return object
     */
    public function disable2fa():object
    {
        auth()->user()->disable2FA();
        return $this->success(
            null,'2FA disabled!',
            Response::HTTP_OK
        );

    }

//    /**
//     * enable user 2fa
//     * @param null
//     * @return null
//     */
    public function verify(TwoFactorRequest $request)
    {
        if(!$request->bearerToken()){
            return $this->error('Token is required', Response::HTTP_UNAUTHORIZED, null);
        }

        // decode jwt token and retrieve user id
        $userId = JWT::decode($request->bearerToken())['user_id'];

        $user = User::where('id',$userId)->first();

        if($user->two_factor_token){

            if($user->two_factor_expires_at->lt(now())){
                return $this->error('Two factor code has expired', Response::HTTP_UNAUTHORIZED, null);
            }
            if($request['two_factor_code'] == $user->two_factor_token) {
                $user->resetTwoFactorCode();
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

            return $this->error('Incorrect 2FA token', Response::HTTP_BAD_REQUEST, null);

        }


        return $this->error('2FA token not detected,try resending', Response::HTTP_BAD_REQUEST, null);

    }



}
