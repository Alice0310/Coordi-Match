<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TradeMessage extends Model
{
    protected $fillable = [
        'trade_id',
        'user_id',
        'message',
        'photo_path',
    ];

    public function trade()
    {
        return $this->belongsTo(Trade::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function photos()
    {
        return $this->hasMany(TradeMessagePhoto::class);
    }
}
