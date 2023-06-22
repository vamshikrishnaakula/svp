<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Staff extends Model
{

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'force_password_change',
        'Dob',
        'MobileNumber',
        'role',
    ];
}
