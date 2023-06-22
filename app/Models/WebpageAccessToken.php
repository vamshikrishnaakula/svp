<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebpageAccessToken extends Model
{
    use HasFactory;

    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
   protected $fillable = [
       'user_id',
       'token',
       'expires_at',
   ];

   /**
    * The attributes that should be hidden for arrays.
    *
    * @var array
    */
   protected $hidden = [
       'token',
   ];
}
