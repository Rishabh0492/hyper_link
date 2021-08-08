<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Languagekey extends Model {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'language_key';
    protected $primaryKey = 'language_key_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['language_key_id', 'message_key', 'message_en', 'message_fr', 'message_zh', 'message_ar', 'message_vi', 'message_fa', 'created_at', 'updated_at'];

}
