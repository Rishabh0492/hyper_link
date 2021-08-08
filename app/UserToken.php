<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserToken extends Model
{
    protected $table = 'user_token';
    protected $primaryKey = 'user_token_id';
}
