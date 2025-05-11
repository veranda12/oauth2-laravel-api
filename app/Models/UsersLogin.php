<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class UsersLogin extends Authenticatable
{
    use HasApiTokens,HasFactory, Notifiable;

    protected $table = 'users_login';

    protected $fillable = [
        'nama', 'email', 'password',
    ];

    protected $hidden = [
        'password',
    ];
}
    