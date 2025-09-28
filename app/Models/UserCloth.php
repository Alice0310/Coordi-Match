<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCloth extends Model
{
    protected $fillable = ['user_id', 'photo_path', 'category', 'color', 'is_active'];

    protected $table = 'user_clothes';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
