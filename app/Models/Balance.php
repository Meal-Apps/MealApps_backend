<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Balance extends Model
{
    use HasFactory,HasApiTokens;
    protected $fillable = ['user_id','manager_id','balance','user_name'];
    public function user(){
        return $this->belongsTo(User::class);

    }
}
