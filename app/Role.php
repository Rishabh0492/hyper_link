<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    //
    use SoftDeletes;
    protected $table = 'roles';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'name', 'guard_name', 'auth_id', 'created_at', 'updated_at', 'deleted_at'
    ];
    
    protected $dates = ['deleted_at'];
}
