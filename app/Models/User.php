<?php

namespace App\Models;

use App\Observers\UserObserver;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     *  Observe model to listen to certain events
     *
     */
    protected static function booted()
    {
       self::observe(UserObserver::class);
    }

    /**
     * get user 2fa status.
     * @param null
     * @return boolean
     */
    public function get2FaStatus(): bool
    {
        return Preference::where('user_id',$this->id)->first()->is_2fa_enabled;
    }

    /**
     * enable user 2fa.
     * @param null
     * @return null
     */
    public function  enable2FA()
    {
        Preference::where("user_id", $this->id)->update(["is_2fa_enabled" => true]);
    }

    /**
     * disable user 2fa.
     * @param null
     * @return null
     */
    public function  disable2Fa(){
        Preference::where("user_id", $this->id)->update(["is_2fa_enabled" => false]);
    }

    public function generateTwoFactorCode(){
        $this->timestamps = false;
        $this->two_factor_token =  mt_rand(100000,999999);
        $this->two_factor_expires_at = now()->addMinutes(10);
        $this->save();
    }

    public function resetTwoFactorCode(){
        $this->timestamps = false;
        $this->two_factor_token = null;
        $this->two_factor_expires_at = null;
        $this->save();
    }




}
