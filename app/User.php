<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable; 
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'username', 'first_name', 'last_name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'access_token'
    ]; 

    protected $attributes = [ 
        'subscription_id' => 1,
    ]; 

    public static function generateToken() {

        $generated = [
            'remember_token' => md5(str_random(32)), 
            'access_token' => md5(str_random(16)),
            ]; 

        return $generated;
    }
}
