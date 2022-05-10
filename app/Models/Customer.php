<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Customer extends Authenticatable
{
    use Notifiable,HasApiTokens;
    protected $fillable = ['name','email','mobile_number','password'];
    protected $hidden = array('password');
}
