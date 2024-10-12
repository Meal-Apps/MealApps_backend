<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Manager extends Model
{
    /** @use HasFactory<\Database\Factories\ManagerFactory> */
    use HasFactory, Notifiable, HasApiTokens;
    protected $guard = 'manager';
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    protected $fillable = ['name', 'meal_name', 'email', 'password'];
}
