<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    public $timestamps = false; 

    protected $fillable = [
        'user_id',
        'name',
        'surname',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
    ];
}
