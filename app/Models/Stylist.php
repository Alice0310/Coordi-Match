<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stylist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'photos',
        'overview',
        'genres',
        'appeal',
        'twitter',
        'instagram',
        'price',
        'status',
    ];

    protected $casts = [
        'photos' => 'array',
        'genres' => 'array',
    ];

        // コメントとのリレーションを追加
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // app/Models/Stylist.php
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function isFavoritedBy($userId)
    {
        return $this->favorites()->where('user_id', $userId)->exists();
    }
}
