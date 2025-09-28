<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'stylist_id',
        'user_id',
        'body',
    ];

    // コメント → スタイリスト
    public function stylist()
    {
        return $this->belongsTo(Stylist::class);
    }

    // コメント → ユーザー
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
