<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
}
